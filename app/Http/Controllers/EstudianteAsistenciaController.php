<?php

namespace App\Http\Controllers;

use App\Models\Asignatura;
use App\Models\EstudianteAsistencia;
use App\Models\EstudianteLicencia;
use App\Models\ListaAsignatura;
use App\Models\HorarioAsignatura;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EstudianteAsistenciaController extends Controller
{
    public function view_index()
    {
        return view('estudiantes_asistencias.index', [
            'head_title' => 'GESTIÓN DE ASISTENCIAS',
        ]);
    }

    public function view_create($lista_asignatura)
    {
        $lista_asignatura = (new ListaAsignatura())->get_lista_asignatura($lista_asignatura);
        $estudiantes_licencias = [];

        $a = Asignatura::with([
            'horarios_asignaturas:id_horario_asignatura,id_nivel,id_gestion,denominacion,hora_inicio,hora_fin,estado',
            'horarios_asignaturas.gestion:id_gestion,anio,estado'
        ])->findOrFail($lista_asignatura->id_asignatura);

        $horarios = $a->horarios_asignaturas;

        // Obtener licencias vigentes en una sola consulta
        if ($lista_asignatura->estudiantes->isNotEmpty()) {
            $estudiantesIds = $lista_asignatura->estudiantes->pluck('id_estudiante')->toArray();

            $licenciasActivas = EstudianteLicencia::whereIn('id_estudiante', $estudiantesIds)
                ->where('fecha_inicio', '<=', now())
                ->where('fecha_fin', '>=', now())
                ->where('estado', 1)
                ->get()
                ->groupBy('id_estudiante');

            foreach ($estudiantesIds as $id) {
                if ($licenciasActivas->has($id)) {
                    $estudiantes_licencias[$id] = $licenciasActivas[$id];
                }
            }
        }

        return view('estudiantes_asistencias.create', [
            'head_title' => 'CREAR ASISTENCIA',
            'lista_asignatura' => $lista_asignatura,
            'estudiantes_licencias' => $estudiantes_licencias,
            'horarios' => $horarios,
        ]);
    }

    public function view_update($estudiante_asistencia)
    {
        // 1. Obtener el registro principal con todas sus relaciones cargadas
        $asistencia = (new EstudianteAsistencia())->get_estudiante_asistencia($estudiante_asistencia);

        $estudiantes_licencias = [];

        // 2. Obtener horarios de la asignatura asociada
        $asignatura = Asignatura::with([
            'horarios_asignaturas:id_horario_asignatura,id_nivel,id_gestion,denominacion,hora_inicio,hora_fin,estado',
            'horarios_asignaturas.gestion:id_gestion,anio,estado'
        ])->findOrFail($asistencia->lista_asignatura->id_asignatura);

        $horarios = $asignatura->horarios_asignaturas;

        // 3. Obtener licencias vigentes en una sola consulta para la fecha de la asistencia
        if ($asistencia->detalles_estudiantes_asistencias->isNotEmpty()) {
            $estudiantesIds = $asistencia->detalles_estudiantes_asistencias->pluck('id_estudiante')->toArray();

            // Usamos la fecha de la asistencia para el cruce
            $fechaAsistencia = $asistencia->fecha;

            $licenciasActivas = EstudianteLicencia::whereIn('id_estudiante', $estudiantesIds)
                ->where('fecha_inicio', '<=', $fechaAsistencia . ' 23:59:59')
                ->where('fecha_fin', '>=', $fechaAsistencia . ' 00:00:00')
                ->where('estado', 1)
                ->get()
                ->groupBy('id_estudiante');

            foreach ($estudiantesIds as $id) {
                if ($licenciasActivas->has($id)) {
                    $estudiantes_licencias[$id] = $licenciasActivas[$id];
                }
            }
        }

        return view('estudiantes_asistencias.update', [
            'head_title' => 'ACTUALIZAR ASISTENCIA',
            'estudiante_asistencia' => $asistencia,
            'estudiantes_licencias' => $estudiantes_licencias,
            'horarios' => $horarios,
        ]);
    }

    public function view_details($estudiante_asistencia)
    {
        $estudiante_asistencia = (new EstudianteAsistencia())->get_estudiante_asistencia($estudiante_asistencia);

        return view('estudiantes_asistencias.details', [
            'head_title' => 'DETALLES DE ASISTENCIA',
            'estudiante_asistencia' => $estudiante_asistencia,
        ]);
    }

    public function listar()
    {
        $estudiantes_asistencias = (new EstudianteAsistencia())->get_all_estudiantes_asistencias();

        return response()->json([
            'data' => $estudiantes_asistencias
        ]);
    }

    public function mostrar(Request $request)
    {
        $estudiante_asistencia = (new EstudianteAsistencia())->get_estudiante_asistencia($request->estudiante_asistencia);

        return response()->json([
            'data' => $estudiante_asistencia
        ]);
    }

    public function create(Request $request)
    {
        $anio_actual = date('Y');

        $request->validate([
            'id_lista_asignatura' => 'required|integer|exists:listas_asignaturas,id_lista_asignatura',
            'id_horario' => 'required|integer|exists:horarios_asignaturas,id_horario_asignatura',
            'fecha' => "required|date|after_or_equal:{$anio_actual}-02-01|before_or_equal:today",
            'estudiantes' => 'required|array|min:1',
            'estudiantes.*.id_estudiante' => 'required|integer|exists:estudiantes,id_estudiante',
            'estudiantes.*.tipo' => 'required|string|in:P,F,A,L',
            'estudiantes.*.id_estudiante_licencia' => 'nullable|integer|exists:estudiantes_licencias,id_estudiante_licencia',
        ]);

        // 1. Validación de Duplicidad (uk_asistencia)
        $existeAsistencia = EstudianteAsistencia::where('id_lista_asignatura', $request->id_lista_asignatura)
            ->where('id_horario_asignatura', $request->id_horario)
            ->where('fecha', $request->fecha)
            ->exists();

        if ($existeAsistencia) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe un registro de asistencia para esta materia, horario y fecha seleccionada.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 2. Seguridad: Validar que los estudiantes pertenecen realmente a la lista
            $lista = ListaAsignatura::with('estudiantes')->findOrFail($request->id_lista_asignatura);
            $estudiantesValidos = $lista->estudiantes->pluck('id_estudiante')->toArray();

            // 3. Preparar variables para el cruce de fechas con licencias
            $horario = HorarioAsignatura::findOrFail($request->id_horario);
            $inicioClase = Carbon::parse($request->fecha . ' ' . $horario->hora_inicio);
            $finClase = Carbon::parse($request->fecha . ' ' . $horario->hora_fin);

            // 4. Inserción de la Tabla Cabecera (Maestro)
            $asistencia = new EstudianteAsistencia();
            $asistencia->id_lista_asignatura = $request->id_lista_asignatura;
            $asistencia->id_horario_asignatura = $request->id_horario; // Mapeado correctamente al esquema DB
            $asistencia->fecha = $request->fecha;
            $asistencia->creado_por = auth()->id(); // Utilizando la fachada Auth nativa
            $asistencia->ip = $request->ip();
            $asistencia->dispositivo = $request->userAgent();
            $asistencia->save();

            $detallesAsistencia = [];

            // 5. Procesamiento y Sobreescritura Automática de Detalles
            foreach ($request->estudiantes as $estudianteReq) {
                $idEstudiante = $estudianteReq['id_estudiante'];

                // Ignorar manipulaciones del frontend si inyectan un ID que no es del curso
                if (!in_array($idEstudiante, $estudiantesValidos)) {
                    continue;
                }

                $tipoAsistencia = $estudianteReq['tipo'];
                $idLicencia = $estudianteReq['id_estudiante_licencia'] ?? null;

                // Cruce exacto de fechas para licencias médicas/justificaciones
                $licenciaAprobada = EstudianteLicencia::where('id_estudiante', $idEstudiante)
                    ->where('estado', 1)
                    ->where('fecha_inicio', '<=', $finClase)
                    ->where('fecha_fin', '>=', $inicioClase)
                    ->first();

                // Regla de Negocio: La licencia anula cualquier envío manual del profesor
                if ($licenciaAprobada) {
                    $tipoAsistencia = 'L';
                    $idLicencia = $licenciaAprobada->id_estudiante_licencia;
                }

                $detallesAsistencia[] = [
                    'id_estudiante_asistencia' => $asistencia->id_estudiante_asistencia,
                    'id_estudiante' => $idEstudiante,
                    'tipo' => $tipoAsistencia,
                    'id_estudiante_licencia' => $idLicencia,
                ];
            }

            // 6. Inserción masiva de Detalles (Muchos a Muchos)
            DB::table('detalles_estudiantes_asistencias')->insert($detallesAsistencia);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'La lista de asistencia ha sido guardada correctamente.',
                'data' => $asistencia
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error crítico al guardar la asistencia: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id_estudiante_asistencia)
    {
        $anio_actual = date('Y');

        // Para que la validación funcione correctamente, agregamos el id_estudiante_asistencia al request
        $request->merge([
            'id_estudiante_asistencia' => $id_estudiante_asistencia,
        ]);

        $request->validate([
            'id_estudiante_asistencia' => 'required|integer|exists:estudiantes_asistencias,id_estudiante_asistencia',
            'id_horario' => 'required|integer|exists:horarios_asignaturas,id_horario_asignatura',
            'fecha' => "required|date|after_or_equal:{$anio_actual}-02-01|before_or_equal:today",
            'estudiantes' => 'required|array|min:1',
            'estudiantes.*.id_estudiante' => 'required|integer|exists:estudiantes,id_estudiante',
            'estudiantes.*.tipo' => 'required|string|in:P,F,A,L',
            'estudiantes.*.id_estudiante_licencia' => 'nullable|integer|exists:estudiantes_licencias,id_estudiante_licencia',
        ]);

        // 1. Obtener la asistencia actual para tener el id_lista_asignatura base
        $asistencia = EstudianteAsistencia::findOrFail($id_estudiante_asistencia);

        // 2. Validación de Duplicidad excluyendo el registro actual
        $existeAsistencia = EstudianteAsistencia::where('id_lista_asignatura', $asistencia->id_lista_asignatura)
            ->where('id_horario_asignatura', $request->id_horario)
            ->where('fecha', $request->fecha)
            ->where('id_estudiante_asistencia', '!=', $id_estudiante_asistencia) // Exclusión clave
            ->exists();

        if ($existeAsistencia) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe otro registro de asistencia para esta materia, horario y fecha.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 3. Seguridad: Validar que los estudiantes pertenecen realmente a la lista
            $lista = ListaAsignatura::with('estudiantes')->findOrFail($asistencia->id_lista_asignatura);
            $estudiantesValidos = $lista->estudiantes->pluck('id_estudiante')->toArray();

            // 4. Preparar variables para el cruce de fechas con licencias
            $horario = HorarioAsignatura::findOrFail($request->id_horario);
            $inicioClase = Carbon::parse($request->fecha . ' ' . $horario->hora_inicio);
            $finClase = Carbon::parse($request->fecha . ' ' . $horario->hora_fin);

            // 5. Actualización de la Tabla Cabecera (Maestro)
            $asistencia->id_horario_asignatura = $request->id_horario;
            $asistencia->fecha = $request->fecha;
            $asistencia->modificado_por = auth()->id(); // Campos de auditoría (modificado)
            $asistencia->ip = $request->ip();
            $asistencia->dispositivo = $request->userAgent();
            $asistencia->save();

            // 6. Eliminar detalles anteriores (para reinsertar limpios)
            DB::table('detalles_estudiantes_asistencias')
                ->where('id_estudiante_asistencia', $asistencia->id_estudiante_asistencia)
                ->delete();

            $detallesAsistencia = [];

            // 7. Procesamiento y Sobreescritura Automática de Detalles
            foreach ($request->estudiantes as $estudianteReq) {
                $idEstudiante = $estudianteReq['id_estudiante'];

                // Ignorar manipulaciones del frontend
                if (!in_array($idEstudiante, $estudiantesValidos)) {
                    continue;
                }

                $tipoAsistencia = $estudianteReq['tipo'];
                $idLicencia = $estudianteReq['id_estudiante_licencia'] ?? null;

                // Cruce exacto de fechas para licencias médicas/justificaciones
                $licenciaAprobada = EstudianteLicencia::where('id_estudiante', $idEstudiante)
                    ->where('estado', 1)
                    ->where('fecha_inicio', '<=', $finClase)
                    ->where('fecha_fin', '>=', $inicioClase)
                    ->first();

                // Regla de Negocio: La licencia anula cualquier envío manual del profesor
                if ($licenciaAprobada) {
                    $tipoAsistencia = 'L';
                    $idLicencia = $licenciaAprobada->id_estudiante_licencia;
                }

                $detallesAsistencia[] = [
                    'id_estudiante_asistencia' => $asistencia->id_estudiante_asistencia,
                    'id_estudiante' => $idEstudiante,
                    'tipo' => $tipoAsistencia,
                    'id_estudiante_licencia' => $idLicencia,
                ];
            }

            // 8. Inserción masiva de Nuevos Detalles
            DB::table('detalles_estudiantes_asistencias')->insert($detallesAsistencia);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'La lista de asistencia ha sido actualizada correctamente.',
                'data' => $asistencia
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error crítico al actualizar la asistencia: ' . $e->getMessage(),
            ], 500);
        }
    }
}
