<?php

namespace App\Http\Controllers;

use App\Models\EstudianteLicencia;
use App\Http\Requests\EstudianteLicenciaValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EstudianteLicenciaController extends Controller
{
    public function view_index()
    {
        // Autorización estática: Verifica si puede listar/ver la vista general
        $this->authorize('viewAny', EstudianteLicencia::class);

        return view('estudiantes_licencias.index', [
            'head_title' => 'GESTIÓN DE LICENCIAS DE ESTUDIANTES',
        ]);
    }

    public function listar()
    {
        // Autorización estática para proteger el endpoint que alimenta DataTables
        $this->authorize('viewAny', EstudianteLicencia::class);

        $tipo_perfil = Auth::user()->persona?->tipo_perfil;
        $filtros = [];
        $licencias = null;

        if ($tipo_perfil === 'SUBDIRECTOR') {
            $filtros['nivel'] = Auth::user()->persona?->docente?->id_nivel;
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
                        'tipo' => 'L',
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

        $licencia->id_estudiante = $request->id_estudiante;
        $licencia->tipo = $request->tipo;
        $licencia->justificacion = $request->justificacion;
        $licencia->fecha_inicio  = $request->fecha_inicio;
        $licencia->fecha_fin     = $request->fecha_fin;
        $licencia->evidencia     = $request->evidencia;

        $licencia->modificado_por      = auth()->id();
        $licencia->ip                  = $request->ip();
        $licencia->dispositivo         = $request->userAgent();
        $licencia->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'La licencia fue actualizada correctamente.',
            'licencia' => $licencia
        ]);
    }

    public function delete(Request $request, $id_estudiante_licencia)
    {
        $request->validate([
            'id_estudiante_licencia' => ['required', 'numeric', 'integer', 'exists:estudiantes_licencias,id_estudiante_licencia'],
        ]);

        $licencia = (new EstudianteLicencia())->get_estudiante_licencia($id_estudiante_licencia);

        // Autorización dinámica antes del soft delete
        $this->authorize('delete', $licencia);

        if ($licencia->estado === 0) {
            return response()->json([
                'success' => true,
                'message' => 'La licencia ya se encuentra eliminada del sistema.',
                'licencia' => $licencia
            ]);
        }

        $licencia->estado            = 0;
        $licencia->eliminado_por     = auth()->id();
        $licencia->fecha_eliminacion = now();
        $licencia->ip                = $request->ip();
        $licencia->dispositivo       = $request->userAgent();
        $licencia->save();

        return response()->json([
            'success' => true,
            'message' => 'La licencia fue eliminada del sistema.',
            'licencia' => $licencia
        ]);
    }
}
