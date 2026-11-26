<?php

namespace App\Http\Controllers;

use App\Models\EstudianteSuspension;
use App\Http\Requests\EstudianteSuspensionValidation;
use App\Models\Curso;
use App\Models\Grado;
use App\Models\Nivel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EstudianteSuspensionController extends Controller
{
    public function view_index()
    {
        $this->authorize('viewAny', EstudianteSuspension::class);

        $tipo_perfil = Auth::user()->persona?->tipo_perfil;

        if ($tipo_perfil === 'DIRECTOR DE NIVEL') {
            $filtros['nivel'] = Auth::user()->persona?->docente?->id_nivel;
            $cursos = (new Curso())->get_cursos($filtros);
            $grados = (new Grado())->get_grados($filtros);
        } else {
            $cursos = (new Curso())->get_all_cursos();
            $grados = (new Grado())->get_all_grados();
        }

        $niveles = (new Nivel())->get_all_niveles();

        return view('estudiantes_suspensiones.index', [
            'head_title' => 'GESTIÓN DE SUSPENSIONES DE ESTUDIANTES',
            'cursos' => $cursos,
            'niveles' => $niveles,
            'grados' => $grados
        ]);
    }

    public function listar(Request $request)
    {
        $this->authorize('viewAny', EstudianteSuspension::class);

        $tipo_perfil = Auth::user()->persona?->tipo_perfil;
        $filtros = [];
        $suspensiones = null;
        $now = now();

        if ($request->filled('rango_fechas')) {
            switch ($request->rango_fechas) {
                case 'hoy':
                    $filtros['fecha_exacta'] = $now->format('Y-m-d');
                    break;

                case 'esta_semana':
                    $filtros['fecha_inicio'] = $now->copy()->startOfWeek()->format('Y-m-d');
                    $filtros['fecha_fin']    = $now->copy()->endOfWeek()->format('Y-m-d');
                    break;

                case 'este_mes':
                    $filtros['fecha_inicio'] = $now->copy()->startOfMonth()->format('Y-m-d');
                    $filtros['fecha_fin']    = $now->copy()->endOfMonth()->format('Y-m-d');
                    break;

                case 'personalizado':
                    if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
                        $filtros['fecha_inicio'] = $request->fecha_inicio;
                        $filtros['fecha_fin']    = $request->fecha_fin;
                    }
                    break;
            }
        }

        if ($request->filled('id_nivel')) {
            $filtros['nivel'] = $request->id_nivel;
        }
        if ($request->filled('id_grado')) {
            $filtros['grado'] = $request->id_grado;
        }
        if ($request->filled('id_curso')) {
            $filtros['curso'] = $request->id_curso;
        }
        if ($request->filled('id_paralelo')) {
            $filtros['paralelo'] = $request->id_paralelo;
        }
        if ($request->estado !== null && $request->estado !== '') {
            $filtros['estado'] = $request->estado;
        }

        if ($tipo_perfil === 'DIRECTOR DE NIVEL') {
            $filtros['nivel'] = Auth::user()->persona?->docente?->id_nivel;
            $suspensiones = (new EstudianteSuspension())->get_estudiantes_suspensiones($filtros);
        } elseif ($filtros && count($filtros) > 0) {
            $suspensiones = (new EstudianteSuspension())->get_estudiantes_suspensiones($filtros);
        } else {
            $suspensiones = (new EstudianteSuspension())->get_all_estudiantes_suspensiones();
        }

        return response()->json(['data' => $suspensiones]);
    }

    public function mostrar(Request $request)
    {
        $suspension = (new EstudianteSuspension())->get_estudiante_suspension($request->estudiante_suspension);

        $this->authorize('view', $suspension);

        return response()->json(['data' => $suspension]);
    }

    public function create(EstudianteSuspensionValidation $request)
    {
        $this->authorize('create', EstudianteSuspension::class);

        DB::beginTransaction();
        try {
            // 1. Crear la Suspensión
            $suspension = new EstudianteSuspension();
            $suspension->id_estudiante = $request->id_estudiante;
            $suspension->motivo        = $request->motivo;
            $suspension->fecha_inicio  = $request->fecha_inicio;
            $suspension->fecha_fin     = $request->fecha_fin;
            $suspension->evidencia     = $request->evidencia ?? '';

            $suspension->creado_por    = auth()->id();
            $suspension->ip            = $request->ip();
            $suspension->dispositivo   = $request->userAgent();
            $suspension->save();

            // 2. Efecto Retroactivo: Buscar asistencias maestras que caigan dentro de la suspensión
            $asistenciasAfectadas = DB::table('estudiantes_asistencias')
                ->join('horarios_asignaturas', 'estudiantes_asistencias.id_horario_asignatura', '=', 'horarios_asignaturas.id_horario_asignatura')
                ->whereRaw("TIMESTAMP(estudiantes_asistencias.fecha, horarios_asignaturas.hora_inicio) <= ?", [$suspension->fecha_fin])
                ->whereRaw("TIMESTAMP(estudiantes_asistencias.fecha, horarios_asignaturas.hora_fin) >= ?", [$suspension->fecha_inicio])
                ->pluck('estudiantes_asistencias.id_estudiante_asistencia');

            // 3. Sobreescribir automáticamente los detalles de asistencia del estudiante
            if ($asistenciasAfectadas->isNotEmpty()) {
                DB::table('detalles_estudiantes_asistencias')
                    ->whereIn('id_estudiante_asistencia', $asistenciasAfectadas)
                    ->where('id_estudiante', $suspension->id_estudiante)
                    ->update([
                        'tipo' => 'SUSPENDIDO',
                        'tiempo_atraso' => 0,
                        'justificacion_permiso' => null,
                        'id_estudiante_licencia' => null,
                        'id_estudiante_suspension' => $suspension->id_estudiante_suspension
                    ]);
            }

            DB::commit();

            return response()->json([
                'status'     => 'success',
                'message'    => 'La suspensión fue registrada exitosamente y se sincronizó con sus asistencias.',
                'suspension' => $suspension
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Ocurrió un error crítico al registrar la suspensión: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(EstudianteSuspensionValidation $request, int $id_estudiante_suspension)
    {
        $suspension = (new EstudianteSuspension())->get_estudiante_suspension($id_estudiante_suspension);

        $this->authorize('update', $suspension);

        DB::beginTransaction();
        try {
            // 1. Detectar si hubo un cambio en el rango de fechas
            $fechasCambiaron = ($suspension->fecha_inicio != $request->fecha_inicio) || ($suspension->fecha_fin != $request->fecha_fin);

            // 2. Limpieza de historial previo (Efecto Retroactivo Inverso Inteligente)
            if ($fechasCambiaron) {
                $asistenciasAfectadasInversas = DB::table('detalles_estudiantes_asistencias')
                    ->where('id_estudiante_suspension', $suspension->id_estudiante_suspension)
                    ->pluck('id_estudiante_asistencia');

                if ($asistenciasAfectadasInversas->isNotEmpty()) {
                    $asistenciasOtros = DB::table('estudiantes_asistencias')
                        ->whereIn('id_estudiante_asistencia', $asistenciasAfectadasInversas)
                        ->whereNotNull('justificacion_otros')
                        ->pluck('id_estudiante_asistencia');

                    $asistenciasFalta = $asistenciasAfectadasInversas->diff($asistenciasOtros);

                    if ($asistenciasOtros->isNotEmpty()) {
                        DB::table('detalles_estudiantes_asistencias')
                            ->where('id_estudiante_suspension', $suspension->id_estudiante_suspension)
                            ->whereIn('id_estudiante_asistencia', $asistenciasOtros)
                            ->update([
                                'tipo' => 'OTROS',
                                'tiempo_atraso' => 0,
                                'justificacion_permiso' => null,
                                'id_estudiante_suspension' => null
                            ]);
                    }

                    if ($asistenciasFalta->isNotEmpty()) {
                        DB::table('detalles_estudiantes_asistencias')
                            ->where('id_estudiante_suspension', $suspension->id_estudiante_suspension)
                            ->whereIn('id_estudiante_asistencia', $asistenciasFalta)
                            ->update([
                                'tipo' => 'FALTA',
                                'tiempo_atraso' => 0,
                                'justificacion_permiso' => null,
                                'id_estudiante_suspension' => null
                            ]);
                    }
                }
            }

            // 3. Actualizar los datos de la Suspensión (id_estudiante es inmutable)
            $suspension->motivo         = $request->motivo;
            $suspension->fecha_inicio   = $request->fecha_inicio;
            $suspension->fecha_fin      = $request->fecha_fin;
            $suspension->evidencia      = $request->evidencia ?? '';

            $suspension->modificado_por = auth()->id();
            $suspension->ip             = $request->ip();
            $suspension->dispositivo    = $request->userAgent();
            $suspension->save();

            // 4. Efecto Retroactivo Normal: Buscar asistencias maestras dentro del NUEVO rango
            $asistenciasAfectadas = DB::table('estudiantes_asistencias')
                ->join('horarios_asignaturas', 'estudiantes_asistencias.id_horario_asignatura', '=', 'horarios_asignaturas.id_horario_asignatura')
                ->whereRaw("TIMESTAMP(estudiantes_asistencias.fecha, horarios_asignaturas.hora_inicio) <= ?", [$suspension->fecha_fin])
                ->whereRaw("TIMESTAMP(estudiantes_asistencias.fecha, horarios_asignaturas.hora_fin) >= ?", [$suspension->fecha_inicio])
                ->pluck('estudiantes_asistencias.id_estudiante_asistencia');

            // 5. Aplicar la Suspensión a las asistencias afectadas del estudiante
            if ($asistenciasAfectadas->isNotEmpty()) {
                DB::table('detalles_estudiantes_asistencias')
                    ->whereIn('id_estudiante_asistencia', $asistenciasAfectadas)
                    ->where('id_estudiante', $suspension->id_estudiante)
                    ->update([
                        'tipo' => 'SUSPENDIDO',
                        'tiempo_atraso' => 0,
                        'justificacion_permiso' => null,
                        'id_estudiante_licencia' => null,
                        'id_estudiante_suspension' => $suspension->id_estudiante_suspension
                    ]);
            }

            DB::commit();

            return response()->json([
                'status'     => 'success',
                'message'    => 'La suspensión fue actualizada y sus asistencias sincronizadas correctamente.',
                'suspension' => $suspension
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Ocurrió un error crítico al actualizar la suspensión: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request, $id_estudiante_suspension)
    {
        $request->merge([
            'id_estudiante_suspension' => $id_estudiante_suspension,
        ]);

        $request->validate([
            'id_estudiante_suspension' => ['required', 'numeric', 'integer', 'exists:estudiantes_suspensiones,id_estudiante_suspension'],
        ]);

        $suspension = (new EstudianteSuspension())->get_estudiante_suspension($id_estudiante_suspension);

        $nombreEstudiante = $suspension->estudiante->persona->apellidos_nombres;

        $this->authorize('delete', $suspension);

        if ($suspension->estado === 0) {
            return response()->json([
                'success'    => true,
                'message'    => 'La suspensión ya se encuentra eliminada del sistema.',
                'suspension' => $suspension
            ]);
        }

        DB::beginTransaction();
        try {
            // 1. Limpieza de historial previo (Efecto Retroactivo Inverso Inteligente)
            $asistenciasAfectadas = DB::table('detalles_estudiantes_asistencias')
                ->where('id_estudiante_suspension', $suspension->id_estudiante_suspension)
                ->pluck('id_estudiante_asistencia');

            if ($asistenciasAfectadas->isNotEmpty()) {
                $asistenciasOtros = DB::table('estudiantes_asistencias')
                    ->whereIn('id_estudiante_asistencia', $asistenciasAfectadas)
                    ->whereNotNull('justificacion_otros')
                    ->pluck('id_estudiante_asistencia');

                $asistenciasFalta = $asistenciasAfectadas->diff($asistenciasOtros);

                if ($asistenciasOtros->isNotEmpty()) {
                    DB::table('detalles_estudiantes_asistencias')
                        ->where('id_estudiante_suspension', $suspension->id_estudiante_suspension)
                        ->whereIn('id_estudiante_asistencia', $asistenciasOtros)
                        ->update([
                            'tipo' => 'OTROS',
                            'tiempo_atraso' => 0,
                            'justificacion_permiso' => null,
                            'id_estudiante_suspension' => null
                        ]);
                }

                if ($asistenciasFalta->isNotEmpty()) {
                    DB::table('detalles_estudiantes_asistencias')
                        ->where('id_estudiante_suspension', $suspension->id_estudiante_suspension)
                        ->whereIn('id_estudiante_asistencia', $asistenciasFalta)
                        ->update([
                            'tipo' => 'FALTA',
                            'tiempo_atraso' => 0,
                            'justificacion_permiso' => null,
                            'id_estudiante_suspension' => null
                        ]);
                }
            }

            // 2. Ejecutar el Soft Delete
            $suspension->estado            = 0;
            $suspension->eliminado_por     = auth()->id();
            $suspension->fecha_eliminacion = now();
            $suspension->ip                = $request->ip();
            $suspension->dispositivo       = $request->userAgent();
            $suspension->save();

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => "La suspensión del estudiante <b class=\"text-primary\">{$nombreEstudiante}</b> fue eliminada y el estado de sus asistencias asociadas se revirtieron a <b class=\"text-danger\">FALTA</b> o a <b class=\"text-secondary\">OTROS</b> correctamente.",
                'suspension' => $suspension
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error crítico al eliminar la suspensión: ' . $e->getMessage()
            ], 500);
        }
    }
}
