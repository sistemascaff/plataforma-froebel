<?php

namespace App\Http\Controllers;

use App\Models\EstudianteLicencia;
use App\Http\Requests\EstudianteLicenciaValidation;
use App\Models\Curso;
use App\Models\Grado;
use App\Models\Nivel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EstudianteLicenciaController extends Controller
{
    public function view_index()
    {
        // Autorización estática: Verifica si puede listar/ver la vista general
        $this->authorize('viewAny', EstudianteLicencia::class);

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

        return view('estudiantes_licencias.index', [
            'head_title' => 'GESTIÓN DE LICENCIAS DE ESTUDIANTES',
            'cursos' => $cursos,
            'niveles' => $niveles,
            'grados' => $grados
        ]);
    }

    public function listar(Request $request)
    {
        // Autorización estática para proteger el endpoint que alimenta DataTables
        $this->authorize('viewAny', EstudianteLicencia::class);

        $tipo_perfil = Auth::user()->persona?->tipo_perfil;
        $filtros = [];
        $licencias = null;

        // Filtros dinámicos (Desde la vista)
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
            $licencias = (new EstudianteLicencia())->get_estudiantes_licencias($filtros);
        } elseif ($filtros && count($filtros) > 0) {
            $licencias = (new EstudianteLicencia())->get_estudiantes_licencias($filtros);
        } else {
            $licencias = (new EstudianteLicencia())->get_all_estudiantes_licencias();
        }

        return response()->json(['data' => $licencias]);
    }

    public function mostrar(Request $request)
    {
        $estudiante = (new EstudianteLicencia())->get_estudiante_licencia($request->estudiante_licencia);

        // Autorización dinámica: Pasa la instancia específica recuperada de la BD
        $this->authorize('view', $estudiante);

        return response()->json(['data' => $estudiante]);
    }

    public function create(EstudianteLicenciaValidation $request)
    {
        // Autorización estática: Bloquea intentos de creación no autorizados
        $this->authorize('create', EstudianteLicencia::class);

        DB::beginTransaction();
        try {
            // 1. Crear la Licencia
            $licencia = new EstudianteLicencia();
            $licencia->id_estudiante = $request->id_estudiante;
            $licencia->tipo = $request->tipo;
            $licencia->justificacion = $request->justificacion;
            $licencia->fecha_inicio = $request->fecha_inicio;
            $licencia->fecha_fin = $request->fecha_fin;
            $licencia->evidencia = $request->evidencia;
            $licencia->solicitado_por = $request->solicitado_por;

            $licencia->creado_por = auth()->id();
            $licencia->ip = $request->ip();
            $licencia->dispositivo    = $request->userAgent();
            $licencia->save();

            // 2. Efecto Retroactivo: Buscar asistencias maestras que caigan dentro de la licencia
            $asistenciasAfectadas = DB::table('estudiantes_asistencias')
                ->join('horarios_asignaturas', 'estudiantes_asistencias.id_horario_asignatura', '=', 'horarios_asignaturas.id_horario_asignatura')
                ->whereRaw("TIMESTAMP(estudiantes_asistencias.fecha, horarios_asignaturas.hora_inicio) <= ?", [$licencia->fecha_fin])
                ->whereRaw("TIMESTAMP(estudiantes_asistencias.fecha, horarios_asignaturas.hora_fin) >= ?", [$licencia->fecha_inicio])
                ->pluck('estudiantes_asistencias.id_estudiante_asistencia');

            // 3. Sobreescribir automáticamente los detalles de asistencia del estudiante
            if ($asistenciasAfectadas->isNotEmpty()) {
                DB::table('detalles_estudiantes_asistencias')
                    ->whereIn('id_estudiante_asistencia', $asistenciasAfectadas)
                    ->where('id_estudiante', $licencia->id_estudiante)
                    ->update([
                        'tipo' => 'LICENCIA',
                        'tiempo_atraso' => 0,
                        'id_estudiante_licencia' => $licencia->id_estudiante_licencia
                    ]);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'La licencia fue registrada exitosamente y se sincronizó con sus asistencias.',
                'licencia' => $licencia
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Ocurrió un error crítico al registrar la licencia: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(EstudianteLicenciaValidation $request, int $id_estudiante_licencia)
    {
        $licencia = (new EstudianteLicencia())->get_estudiante_licencia($id_estudiante_licencia);

        // Autorización dinámica antes de modificar el registro
        $this->authorize('update', $licencia);

        DB::beginTransaction();
        try {
            // 1. Detectar si hubo un cambio en el rango de fechas
            $fechasCambiaron = ($licencia->fecha_inicio != $request->fecha_inicio) || ($licencia->fecha_fin != $request->fecha_fin);

            // 2. Limpieza de historial previo (Efecto Retroactivo Inverso Inteligente)
            if ($fechasCambiaron) {
                $asistenciasAfectadasInversas = DB::table('detalles_estudiantes_asistencias')
                    ->where('id_estudiante_licencia', $licencia->id_estudiante_licencia)
                    ->pluck('id_estudiante_asistencia');

                if ($asistenciasAfectadasInversas->isNotEmpty()) {
                    // Separar cuáles asistencias maestras son de tipo "OTROS" (tienen justificacion_otros)
                    $asistenciasOtros = DB::table('estudiantes_asistencias')
                        ->whereIn('id_estudiante_asistencia', $asistenciasAfectadasInversas)
                        ->whereNotNull('justificacion_otros')
                        ->pluck('id_estudiante_asistencia');

                    $asistenciasFalta = $asistenciasAfectadasInversas->diff($asistenciasOtros);

                    // Revertir a 'OTROS' las que correspondan
                    if ($asistenciasOtros->isNotEmpty()) {
                        DB::table('detalles_estudiantes_asistencias')
                            ->where('id_estudiante_licencia', $licencia->id_estudiante_licencia)
                            ->whereIn('id_estudiante_asistencia', $asistenciasOtros)
                            ->update([
                                'tipo' => 'OTROS',
                                'tiempo_atraso' => 0,
                                'id_estudiante_licencia' => null
                            ]);
                    }

                    // Revertir a 'FALTA' el resto de asistencias regulares
                    if ($asistenciasFalta->isNotEmpty()) {
                        DB::table('detalles_estudiantes_asistencias')
                            ->where('id_estudiante_licencia', $licencia->id_estudiante_licencia)
                            ->whereIn('id_estudiante_asistencia', $asistenciasFalta)
                            ->update([
                                'tipo' => 'FALTA',
                                'tiempo_atraso' => 0,
                                'id_estudiante_licencia' => null
                            ]);
                    }
                }
            }

            // 3. Actualizamos los datos del registro maestro de la Licencia
            // En este caso NO SE ACTUALIZA EL ESTUDIANTE (id_estudiante) porque una licencia es PERSONAL E INTRANSFERIBLE
            $licencia->tipo = $request->tipo;
            $licencia->justificacion = $request->justificacion;
            $licencia->fecha_inicio  = $request->fecha_inicio;
            $licencia->fecha_fin     = $request->fecha_fin;
            $licencia->evidencia     = $request->evidencia;
            $licencia->solicitado_por = $request->solicitado_por;

            $licencia->modificado_por      = auth()->id();
            $licencia->ip                  = $request->ip();
            $licencia->dispositivo         = $request->userAgent();
            $licencia->save();

            // 4. Efecto Retroactivo Normal: Buscar asistencias maestras que caigan dentro del NUEVO rango
            $asistenciasAfectadas = DB::table('estudiantes_asistencias')
                ->join('horarios_asignaturas', 'estudiantes_asistencias.id_horario_asignatura', '=', 'horarios_asignaturas.id_horario_asignatura')
                ->whereRaw("TIMESTAMP(estudiantes_asistencias.fecha, horarios_asignaturas.hora_inicio) <= ?", [$licencia->fecha_fin])
                ->whereRaw("TIMESTAMP(estudiantes_asistencias.fecha, horarios_asignaturas.hora_fin) >= ?", [$licencia->fecha_inicio])
                ->pluck('estudiantes_asistencias.id_estudiante_asistencia');

            // 5. Aplicar la Licencia a las asistencias afectadas del estudiante
            if ($asistenciasAfectadas->isNotEmpty()) {
                DB::table('detalles_estudiantes_asistencias')
                    ->whereIn('id_estudiante_asistencia', $asistenciasAfectadas)
                    ->where('id_estudiante', $licencia->id_estudiante)
                    ->update([
                        'tipo' => 'LICENCIA',
                        'tiempo_atraso' => 0,
                        'id_estudiante_licencia' => $licencia->id_estudiante_licencia
                    ]);
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'La licencia fue actualizada y sus asistencias sincronizadas correctamente.',
                'licencia' => $licencia
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'status'  => 'error',
                'message' => 'Ocurrió un error crítico al actualizar la licencia: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete(Request $request, $id_estudiante_licencia)
    {
        // Para que la validación funcione correctamente, agregamos el id_estudiante_licencia al request
        $request->merge([
            'id_estudiante_licencia' => $id_estudiante_licencia,
        ]);

        $request->validate([
            'id_estudiante_licencia' => ['required', 'numeric', 'integer', 'exists:estudiantes_licencias,id_estudiante_licencia'],
        ]);

        $licencia = (new EstudianteLicencia())->get_estudiante_licencia($id_estudiante_licencia);

        $nombreEstudiante = $licencia->estudiante->persona->apellidos_nombres;

        // Autorización dinámica antes del soft delete
        $this->authorize('delete', $licencia);

        if ($licencia->estado === 0) {
            return response()->json([
                'success' => true,
                'message' => 'La licencia ya se encuentra eliminada del sistema.',
                'licencia' => $licencia
            ]);
        }

        DB::beginTransaction();
        try {
            // 1. Limpieza de historial previo (Efecto Retroactivo Inverso Inteligente)
            $asistenciasAfectadas = DB::table('detalles_estudiantes_asistencias')
                ->where('id_estudiante_licencia', $licencia->id_estudiante_licencia)
                ->pluck('id_estudiante_asistencia');

            if ($asistenciasAfectadas->isNotEmpty()) {
                // Separar cuáles asistencias maestras son de tipo "OTROS" (tienen justificacion_otros)
                $asistenciasOtros = DB::table('estudiantes_asistencias')
                    ->whereIn('id_estudiante_asistencia', $asistenciasAfectadas)
                    ->whereNotNull('justificacion_otros')
                    ->pluck('id_estudiante_asistencia');

                $asistenciasFalta = $asistenciasAfectadas->diff($asistenciasOtros);

                // Revertir a 'OTROS' las que correspondan
                if ($asistenciasOtros->isNotEmpty()) {
                    DB::table('detalles_estudiantes_asistencias')
                        ->where('id_estudiante_licencia', $licencia->id_estudiante_licencia)
                        ->whereIn('id_estudiante_asistencia', $asistenciasOtros)
                        ->update([
                            'tipo' => 'OTROS',
                            'tiempo_atraso' => 0,
                            'id_estudiante_licencia' => null
                        ]);
                }

                // Revertir a 'FALTA' el resto de asistencias regulares
                if ($asistenciasFalta->isNotEmpty()) {
                    DB::table('detalles_estudiantes_asistencias')
                        ->where('id_estudiante_licencia', $licencia->id_estudiante_licencia)
                        ->whereIn('id_estudiante_asistencia', $asistenciasFalta)
                        ->update([
                            'tipo' => 'FALTA',
                            'tiempo_atraso' => 0,
                            'id_estudiante_licencia' => null
                        ]);
                }
            }

            // 2. Ejecutar el Soft Delete
            $licencia->estado            = 0;
            $licencia->eliminado_por     = auth()->id();
            $licencia->fecha_eliminacion = now();
            $licencia->ip                = $request->ip();
            $licencia->dispositivo       = $request->userAgent();
            $licencia->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "La licencia del estudiante <b class=\"text-primary\">{$nombreEstudiante}</b> fue eliminada y el estado de sus asistencias asociadas se revirtieron a <b class=\"text-danger\">FALTA</b> o a <b class=\"text-secondary\">OTROS</b> correctamente.",
                'licencia' => $licencia
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error crítico al eliminar la licencia: ' . $e->getMessage()
            ], 500);
        }
    }
}
