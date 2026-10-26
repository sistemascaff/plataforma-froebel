<?php

namespace App\Http\Controllers;

use App\Models\Asignatura;
use App\Models\Coordinacion;
use App\Models\Curso;
use App\Models\DetalleEstudianteAsistencia;
use App\Models\Docente;
use App\Models\EstudianteAsistencia;
use App\Models\EstudianteLicencia;
use App\Models\ListaAsignatura;
use App\Models\HorarioAsignatura;
use App\Models\Nivel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EstudianteAsistenciaController extends Controller
{
    public function view_index()
    {
        $this->authorize('viewAny', EstudianteAsistencia::class);

        $tipo_perfil = Auth::user()->persona?->tipo_perfil;

        $niveles = (new Nivel())->get_all_niveles();
        $coordinaciones = (new Coordinacion())->get_all_coordinaciones();
        $docentes = (new Docente())->get_all_docentes();

        return view('estudiantes_asistencias.index', [
            'head_title' => 'GESTIÓN DE ASISTENCIAS',
            'tipo_perfil' => $tipo_perfil,
            'niveles' => $niveles,
            'coordinaciones' => $coordinaciones,
            'docentes' => $docentes,
        ]);
    }

    public function view_create($lista_asignatura)
    {
        $lista_asignatura = (new ListaAsignatura())->get_lista_asignatura($lista_asignatura);

        $this->authorize('create', [EstudianteAsistencia::class, $lista_asignatura]);

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

        $this->authorize('update', $asistencia);

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

    public function view_reportes()
    {
        $this->authorize('viewAny', EstudianteAsistencia::class);

        $niveles = (new Nivel())->get_all_niveles();
        $cursos = (new Curso())->get_all_cursos();
        $coordinaciones = (new Coordinacion())->get_all_coordinaciones();
        $docentes = (new Docente())->get_all_docentes();

        return view('estudiantes_asistencias.reportes', [
            'head_title' => 'REPORTES DE ASISTENCIAS',
            'niveles' => $niveles,
            'coordinaciones' => $coordinaciones,
            'cursos' => $cursos,
            'docentes' => $docentes,
        ]);
    }

    public function view_details($estudiante_asistencia)
    {
        $estudiante_asistencia = (new EstudianteAsistencia())->get_estudiante_asistencia($estudiante_asistencia);

        $this->authorize('view', $estudiante_asistencia);

        return view('estudiantes_asistencias.details', [
            'head_title' => 'DETALLES DE ASISTENCIA',
            'estudiante_asistencia' => $estudiante_asistencia,
        ]);
    }

    public function listar(Request $request)
    {
        $this->authorize('viewAny', EstudianteAsistencia::class);

        $tipo_perfil = Auth::user()->persona?->tipo_perfil;
        $filtros = [];
        $estudiantes_asistencias = null;

        // Filtros dinámicos (Desde la vista)
        if ($request->filled('id_nivel')) {
            $filtros['nivel'] = $request->id_nivel;
        }
        if ($request->filled('id_coordinacion')) {
            $filtros['coordinacion'] = $request->id_coordinacion;
        }
        if ($request->filled('id_docente')) {
            $filtros['docente'] = $request->id_docente;
        }

        // Si el usuario tiene un perfil que requiere filtrado automático, aplicamos esos filtros
        if ($tipo_perfil === 'DIRECTOR DE NIVEL') {
            $filtros['nivel'] = Auth::user()->persona?->docente?->id_nivel;
            $estudiantes_asistencias = (new EstudianteAsistencia())->get_estudiantes_asistencias($filtros);
        } else if ($tipo_perfil === 'COORDINADOR') {
            $filtros['coordinacion'] = Auth::user()->persona?->docente?->id_coordinacion;
            $estudiantes_asistencias = (new EstudianteAsistencia())->get_estudiantes_asistencias($filtros);
        } else if ($tipo_perfil === 'DOCENTE') {
            $filtros['docente'] = Auth::user()->persona?->docente?->id_docente;
            $estudiantes_asistencias = (new EstudianteAsistencia())->get_estudiantes_asistencias($filtros);
        } elseif ($filtros && count($filtros) > 0) {
            $estudiantes_asistencias = (new EstudianteAsistencia())->get_estudiantes_asistencias($filtros);
        } else {
            $estudiantes_asistencias = (new EstudianteAsistencia())->get_all_estudiantes_asistencias();
        }

        return response()->json([
            'data' => $estudiantes_asistencias
        ]);
    }

    public function listar_reporte_incidencias(Request $request)
    {
        $this->authorize('viewAny', EstudianteAsistencia::class);

        $tipo_perfil = Auth::user()->persona?->tipo_perfil;
        $filtros = [];

        // Filtros dinámicos (Desde la vista)
        if ($request->filled('fecha_inicio')) {
            $filtros['fecha_inicio'] = $request->fecha_inicio;
        }
        if ($request->filled('fecha_fin')) {
            $filtros['fecha_fin'] = $request->fecha_fin;
        }
        if ($request->filled('id_nivel')) {
            $filtros['nivel'] = $request->id_nivel;
        }
        if ($request->filled('id_coordinacion')) {
            $filtros['coordinacion'] = $request->id_coordinacion;
        }
        if ($request->filled('id_docente')) {
            $filtros['docente'] = $request->id_docente;
        }
        if ($request->filled('id_curso')) {
            $filtros['id_curso'] = $request->id_curso;
        }

        // Filtros inmutables (Según el perfil del usuario)
        if ($tipo_perfil === 'DIRECTOR DE NIVEL') {
            $filtros['nivel'] = Auth::user()->persona?->docente?->id_nivel;
        } else if ($tipo_perfil === 'COORDINADOR') {
            $filtros['coordinacion'] = Auth::user()->persona?->docente?->id_coordinacion;
        } else if ($tipo_perfil === 'DOCENTE') {
            $filtros['docente'] = Auth::user()->persona?->docente?->id_docente;
        }

        $reporte_incidencias = (new DetalleEstudianteAsistencia())->get_reporte_incidencias($filtros);

        return response()->json([
            'data' => $reporte_incidencias
        ]);
    }

    public function mostrar(Request $request)
    {
        $estudiante_asistencia = (new EstudianteAsistencia())->get_estudiante_asistencia($request->estudiante_asistencia);

        $this->authorize('view', $estudiante_asistencia);

        return response()->json([
            'data' => $estudiante_asistencia
        ]);
    }

    public function create(Request $request)
    {
        // Precargamos la relación 'periodo' y 'asignatura' para validaciones de integridad
        $lista = ListaAsignatura::with(['periodo', 'asignatura'])->findOrFail($request->id_lista_asignatura);

        $this->authorize('create', [EstudianteAsistencia::class, $lista]);

        $periodo = $lista->periodo;

        if ($periodo->estado !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede crear asistencias cuyo periodo no está activo.'
            ], 422);
        }

        // Validación de integridad: el periodo debe contar con fechas configuradas
        if (!$periodo || !$periodo->fecha_inicio || !$periodo->fecha_fin) {
            return response()->json([
                'success' => false,
                'message' => 'El periodo académico asociado a esta lista no tiene configuradas sus fechas de inicio y fin.'
            ], 422);
        }

        $request->validate([
            'id_lista_asignatura' => 'required|integer|exists:listas_asignaturas,id_lista_asignatura',
            'id_horario' => 'required|integer|exists:horarios_asignaturas,id_horario_asignatura',
            'es_horario_no_regular' => 'nullable|boolean',
            'fecha' => [
                'required',
                'date',
                'before_or_equal:today',
                'after_or_equal:' . $periodo->fecha_inicio,
                'before_or_equal:' . $periodo->fecha_fin,
            ],
            'justificacion_otros' => 'nullable|string|in:ACTIVIDAD CURRICULAR,ACTIVIDAD EXTRACURRICULAR',
            'estudiantes' => 'required|array|min:1',
            'estudiantes.*.id_estudiante' => 'required|integer|exists:estudiantes,id_estudiante',
            'estudiantes.*.tipo' => 'required|string|in:PRESENTE,FALTA,ATRASO,LICENCIA,PERMISO,OTROS',
            'estudiantes.*.tiempo_atraso' => 'nullable|integer|min:0|max:60',
            'estudiantes.*.id_estudiante_licencia' => 'nullable|integer|exists:estudiantes_licencias,id_estudiante_licencia',
        ], [
            'fecha.after_or_equal' => "La fecha seleccionada no puede ser anterior al inicio del periodo ({$periodo->periodo}: {$periodo->fecha_inicio}).",
            'fecha.before_or_equal' => "La fecha seleccionada debe estar dentro del periodo ({$periodo->periodo}: hasta {$periodo->fecha_fin}) y no superar la fecha actual.",
        ]);

        $esHorarioNoRegular = $request->boolean('es_horario_no_regular');
        $horario = HorarioAsignatura::findOrFail($request->id_horario);

        // Validación de integridad del horario seleccionado (Regular vs No Regular)
        if ($esHorarioNoRegular) {
            if ((int) $horario->id_nivel !== (int) $lista->asignatura->id_nivel) {
                return response()->json([
                    'success' => false,
                    'message' => 'El horario no regular seleccionado no pertenece al nivel académico de la asignatura.'
                ], 422);
            }
        } else {
            $diaSemanaFecha = Carbon::parse($request->fecha)->dayOfWeekIso; // 1 = Lunes, 7 = Domingo
            $perteneceHorarioRegular = DB::table('detalles_horarios_asignaturas')
                ->where('id_asignatura', $lista->id_asignatura)
                ->where('id_horario_asignatura', $request->id_horario)
                ->where('dia_semana', $diaSemanaFecha)
                ->exists();

            if (!$perteneceHorarioRegular) {
                return response()->json([
                    'success' => false,
                    'message' => 'El horario seleccionado no corresponde al horario regular de la asignatura para el día de la fecha indicada.'
                ], 422);
            }
        }

        // Validación de Regla de Negocio para "OTROS"
        $tiposEnviados = collect($request->estudiantes)->pluck('tipo');
        $esModoOtros = $tiposEnviados->contains('OTROS') || $request->filled('justificacion_otros');

        if ($esModoOtros) {
            if (!$request->filled('justificacion_otros')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe seleccionar una justificación obligatoria (ACTIVIDAD CURRICULAR o ACTIVIDAD EXTRACURRICULAR) cuando el tipo de asistencia es OTROS.'
                ], 422);
            }

            // En modo OTROS no pueden existir PRESENTES, FALTAS, ATRASOS ni PERMISOS mezclados (solo OTROS y LICENCIA por prioridad)
            $tiposInvalidosEnOtros = $tiposEnviados->diff(['OTROS', 'LICENCIA']);
            if ($tiposInvalidosEnOtros->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuando la asistencia es de tipo OTROS, engloba a toda la clase y no puede combinarse con Presentes, Faltas, Atrasos o Permisos.'
                ], 422);
            }
        }

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
            $lista->loadMissing('estudiantes');
            $estudiantesValidos = $lista->estudiantes->pluck('id_estudiante')->toArray();

            // 3. Preparar variables para el cruce de fechas con licencias
            $inicioClase = Carbon::parse($request->fecha . ' ' . $horario->hora_inicio);
            $finClase = Carbon::parse($request->fecha . ' ' . $horario->hora_fin);

            // 4. Inserción de la Tabla Cabecera (Maestro)
            $asistencia = new EstudianteAsistencia();
            $asistencia->id_lista_asignatura = $request->id_lista_asignatura;
            $asistencia->id_horario_asignatura = $request->id_horario;
            $asistencia->es_horario_no_regular = $esHorarioNoRegular ? 1 : null;
            $asistencia->fecha = $request->fecha;
            $asistencia->justificacion_otros = $esModoOtros ? $request->justificacion_otros : null;
            $asistencia->creado_por = auth()->id();
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

                // Si la clase fue declarada como OTROS, por defecto el alumno es OTROS (salvo que tenga licencia)
                $tipoAsistencia = $esModoOtros ? 'OTROS' : $estudianteReq['tipo'];
                $idLicencia = $estudianteReq['id_estudiante_licencia'] ?? null;
                $tiempoAtraso = $estudianteReq['tiempo_atraso'] ?? 0;

                // Cruce exacto de fechas para licencias médicas/justificaciones
                $licenciaAprobada = EstudianteLicencia::where('id_estudiante', $idEstudiante)
                    ->where('estado', 1)
                    ->where('fecha_inicio', '<=', $finClase)
                    ->where('fecha_fin', '>=', $inicioClase)
                    ->first();

                // Regla de Negocio: LICENCIA tiene prioridad absoluta sobre cualquier otro estado (incluido OTROS y PERMISO)
                if ($licenciaAprobada) {
                    $tipoAsistencia = 'LICENCIA';
                    $idLicencia = $licenciaAprobada->id_estudiante_licencia;
                    $tiempoAtraso = 0;
                } else {
                    // Si no tiene licencia activa, nos aseguramos de limpiar cualquier id_estudiante_licencia residual
                    $idLicencia = null;
                }

                // Consistencia de datos: Forzar 0 si el tipo final no es un Atraso ('ATRASO')
                if ($tipoAsistencia !== 'ATRASO') {
                    $tiempoAtraso = 0;
                }

                $detallesAsistencia[] = [
                    'id_estudiante_asistencia' => $asistencia->id_estudiante_asistencia,
                    'id_estudiante' => $idEstudiante,
                    'tipo' => $tipoAsistencia,
                    'tiempo_atraso' => $tiempoAtraso,
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
        // Cargamos la asistencia junto con su lista, asignatura y el periodo correspondiente
        $asistencia = EstudianteAsistencia::with(['lista_asignatura.periodo', 'lista_asignatura.asignatura'])->findOrFail($id_estudiante_asistencia);

        $this->authorize('update', $asistencia);

        $periodo = $asistencia->lista_asignatura?->periodo;

        if ($periodo->estado !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede modificar asistencias cuyo periodo no está activo.'
            ], 422);
        }

        if (!$periodo || !$periodo->fecha_inicio || !$periodo->fecha_fin) {
            return response()->json([
                'success' => false,
                'message' => 'El periodo académico asociado a esta asistencia no tiene configuradas sus fechas de inicio y fin.'
            ], 422);
        }

        // Para que la validación funcione correctamente, agregamos el id_estudiante_asistencia al request
        $request->merge([
            'id_estudiante_asistencia' => $id_estudiante_asistencia,
        ]);

        $request->validate([
            'id_estudiante_asistencia' => 'required|integer|exists:estudiantes_asistencias,id_estudiante_asistencia',
            'id_horario' => 'required|integer|exists:horarios_asignaturas,id_horario_asignatura',
            'es_horario_no_regular' => 'nullable|boolean',
            'fecha' => [
                'required',
                'date',
                'before_or_equal:today',
                'after_or_equal:' . $periodo->fecha_inicio,
                'before_or_equal:' . $periodo->fecha_fin,
            ],
            'justificacion_otros' => 'nullable|string|in:ACTIVIDAD CURRICULAR,ACTIVIDAD EXTRACURRICULAR',
            'estudiantes' => 'required|array|min:1',
            'estudiantes.*.id_estudiante' => 'required|integer|exists:estudiantes,id_estudiante',
            'estudiantes.*.tipo' => 'required|string|in:PRESENTE,FALTA,ATRASO,LICENCIA,PERMISO,OTROS',
            'estudiantes.*.tiempo_atraso' => 'nullable|integer|min:0|max:60',
            'estudiantes.*.id_estudiante_licencia' => 'nullable|integer|exists:estudiantes_licencias,id_estudiante_licencia',
        ], [
            'fecha.after_or_equal' => "La fecha seleccionada no puede ser anterior al inicio del periodo ({$periodo->periodo}: {$periodo->fecha_inicio}).",
            'fecha.before_or_equal' => "La fecha seleccionada debe estar dentro del periodo ({$periodo->periodo}: hasta {$periodo->fecha_fin}) y no superar la fecha actual.",
        ]);

        $esHorarioNoRegular = $request->boolean('es_horario_no_regular');
        $horario = HorarioAsignatura::findOrFail($request->id_horario);
        $asignatura = $asistencia->lista_asignatura->asignatura;

        // Validación de integridad del horario seleccionado (Regular vs No Regular)
        if ($esHorarioNoRegular) {
            if ((int) $horario->id_nivel !== (int) $asignatura->id_nivel) {
                return response()->json([
                    'success' => false,
                    'message' => 'El horario no regular seleccionado no pertenece al nivel académico de la asignatura.'
                ], 422);
            }
        } else {
            $diaSemanaFecha = Carbon::parse($request->fecha)->dayOfWeekIso;
            $perteneceHorarioRegular = DB::table('detalles_horarios_asignaturas')
                ->where('id_asignatura', $asignatura->id_asignatura)
                ->where('id_horario_asignatura', $request->id_horario)
                ->where('dia_semana', $diaSemanaFecha)
                ->exists();

            if (!$perteneceHorarioRegular) {
                return response()->json([
                    'success' => false,
                    'message' => 'El horario seleccionado no corresponde al horario regular de la asignatura para el día de la fecha indicada.'
                ], 422);
            }
        }

        // Validación de Regla de Negocio para "OTROS"
        $tiposEnviados = collect($request->estudiantes)->pluck('tipo');
        $esModoOtros = $tiposEnviados->contains('OTROS') || $request->filled('justificacion_otros');

        if ($esModoOtros) {
            if (!$request->filled('justificacion_otros')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Debe seleccionar una justificación obligatoria (ACTIVIDAD CURRICULAR o ACTIVIDAD EXTRACURRICULAR) cuando el tipo de asistencia es OTROS.'
                ], 422);
            }

            $tiposInvalidosEnOtros = $tiposEnviados->diff(['OTROS', 'LICENCIA']);
            if ($tiposInvalidosEnOtros->isNotEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cuando la asistencia es de tipo OTROS, engloba a toda la clase y no puede combinarse con Presentes, Faltas, Atrasos o Permisos.'
                ], 422);
            }
        }

        // 1. Validación de Duplicidad excluyendo el registro actual
        $existeAsistencia = EstudianteAsistencia::where('id_lista_asignatura', $asistencia->id_lista_asignatura)
            ->where('id_horario_asignatura', $request->id_horario)
            ->where('fecha', $request->fecha)
            ->where('id_estudiante_asistencia', '!=', $id_estudiante_asistencia)
            ->exists();

        if ($existeAsistencia) {
            return response()->json([
                'success' => false,
                'message' => 'Ya existe otro registro de asistencia para esta materia, horario y fecha.'
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 2. Seguridad: Validar que los estudiantes pertenecen realmente a la lista
            $lista = ListaAsignatura::with('estudiantes')->findOrFail($asistencia->id_lista_asignatura);
            $estudiantesValidos = $lista->estudiantes->pluck('id_estudiante')->toArray();

            // 3. Preparar variables para el cruce de fechas con licencias
            $inicioClase = Carbon::parse($request->fecha . ' ' . $horario->hora_inicio);
            $finClase = Carbon::parse($request->fecha . ' ' . $horario->hora_fin);

            // 4. Actualización de la Tabla Cabecera (Maestro)
            $asistencia->id_horario_asignatura = $request->id_horario;
            $asistencia->es_horario_no_regular = $esHorarioNoRegular ? 1 : null;
            $asistencia->fecha = $request->fecha;
            $asistencia->justificacion_otros = $esModoOtros ? $request->justificacion_otros : null;
            $asistencia->modificado_por = auth()->id();
            $asistencia->ip = $request->ip();
            $asistencia->dispositivo = $request->userAgent();
            $asistencia->save();

            // 5. Eliminar detalles anteriores (para reinsertar limpios)
            DB::table('detalles_estudiantes_asistencias')
                ->where('id_estudiante_asistencia', $asistencia->id_estudiante_asistencia)
                ->delete();

            $detallesAsistencia = [];

            // 6. Procesamiento y Sobreescritura Automática de Detalles
            foreach ($request->estudiantes as $estudianteReq) {
                $idEstudiante = $estudianteReq['id_estudiante'];

                // Ignorar manipulaciones del frontend
                if (!in_array($idEstudiante, $estudiantesValidos)) {
                    continue;
                }

                $tipoAsistencia = $esModoOtros ? 'OTROS' : $estudianteReq['tipo'];
                $idLicencia = $estudianteReq['id_estudiante_licencia'] ?? null;
                $tiempoAtraso = $estudianteReq['tiempo_atraso'] ?? 0;

                // Cruce exacto de fechas para licencias médicas/justificaciones
                $licenciaAprobada = EstudianteLicencia::where('id_estudiante', $idEstudiante)
                    ->where('estado', 1)
                    ->where('fecha_inicio', '<=', $finClase)
                    ->where('fecha_fin', '>=', $inicioClase)
                    ->first();

                // Regla de Negocio: LICENCIA tiene prioridad absoluta sobre cualquier otro estado
                if ($licenciaAprobada) {
                    $tipoAsistencia = 'LICENCIA';
                    $idLicencia = $licenciaAprobada->id_estudiante_licencia;
                    $tiempoAtraso = 0;
                } else {
                    $idLicencia = null;
                }

                // Consistencia de datos: Forzar 0 si el tipo final no es un Atraso ('ATRASO')
                if ($tipoAsistencia !== 'ATRASO') {
                    $tiempoAtraso = 0;
                }

                $detallesAsistencia[] = [
                    'id_estudiante_asistencia' => $asistencia->id_estudiante_asistencia,
                    'id_estudiante' => $idEstudiante,
                    'tipo' => $tipoAsistencia,
                    'tiempo_atraso' => $tiempoAtraso,
                    'id_estudiante_licencia' => $idLicencia,
                ];
            }

            // 7. Inserción masiva de Nuevos Detalles
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
