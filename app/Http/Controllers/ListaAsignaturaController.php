<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\DetalleListaAsignatura;
use App\Models\Estudiante;
use App\Models\ListaAsignatura;
use App\Models\Periodo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ListaAsignaturaController extends Controller
{
    public function view_index()
    {
        $this->authorize('viewAny', ListaAsignatura::class);

        return view('listas_asignaturas.index', [
            'head_title' => 'LISTAS DE ASIGNATURAS'
        ]);
    }

    public function view_details(int $id_lista_asignatura)
    {
        // 1. Obtener la lista y cargar los estudiantes ordenados alfabéticamente
        $lista_asignatura = (new ListaAsignatura())->get_lista_asignatura($id_lista_asignatura);

        $this->authorize('view', $lista_asignatura);

        // Forzamos la carga (o recarga) de estudiantes con ordenamiento por la tabla 'personas'
        $lista_asignatura->load([
            'estudiantes' => function ($query) {
                $query->join('personas', 'estudiantes.id_persona', '=', 'personas.id_persona')
                    ->orderBy('personas.apellido_paterno', 'asc')
                    ->orderBy('personas.apellido_materno', 'asc')
                    ->orderBy('personas.nombres', 'asc')
                    ->select('estudiantes.*'); // Evita colisión de IDs de tablas
            },
            'estudiantes.persona.usuario',
            'estudiantes.curso'
        ]);

        $asignatura = $lista_asignatura->asignatura;
        $periodo = $lista_asignatura->periodo->periodo;
        $anio_gestion = $lista_asignatura->periodo->gestion->anio;
        $anio_actual = date('Y'); // Obtiene el año actual del sistema

        $cambios = []; // Array para registrar los mensajes de cambios

        // 2. Validar que la lista sea de la gestión actual y el periodo esté activo
        if ($anio_gestion == $anio_actual && $lista_asignatura->periodo->estado == 1) {

            // ====================================================================
            // LÓGICA PARA BLOQUE: CURSO
            // ====================================================================
            if ($asignatura->tipo_bloque === 'curso' && !is_null($asignatura->id_curso)) {
                // A.1 LIMPIEZA INACTIVOS
                $inactivosAEliminar = DetalleListaAsignatura::with('estudiante.persona')
                    ->where('id_lista_asignatura', $id_lista_asignatura)
                    ->whereHas('estudiante', fn($q) => $q->where('estado', 0))
                    ->get();

                if ($inactivosAEliminar->isNotEmpty()) {
                    foreach ($inactivosAEliminar as $detalle) {
                        $persona = $detalle->estudiante->persona;
                        $cambios[] = "<p class='text-danger mb-2'><i class='fa-solid fa-user-minus me-2'></i> Se ha retirado de la lista al estudiante <b>{$persona->apellidos_nombres}</b> (Retirado/Inactivo).</p>";
                    }
                    DetalleListaAsignatura::whereIn('id_estudiante', $inactivosAEliminar->pluck('id_estudiante'))
                        ->where('id_lista_asignatura', $id_lista_asignatura)
                        ->delete();
                }

                // A.2 LIMPIEZA AJENOS
                $ajenosAEliminar = DetalleListaAsignatura::with('estudiante.persona', 'estudiante.curso')
                    ->where('id_lista_asignatura', $id_lista_asignatura)
                    ->whereHas('estudiante', fn($q) => $q->where('id_curso', '!=', $asignatura->id_curso))
                    ->get();

                if ($ajenosAEliminar->isNotEmpty()) {
                    foreach ($ajenosAEliminar as $detalle) {
                        $persona = $detalle->estudiante->persona;
                        $cursoErroneo = $detalle->estudiante->curso->curso ?? 'Otro curso';
                        $cambios[] = "<p class='text-danger mb-2'><i class='fa-solid fa-user-slash me-2'></i> Se ha retirado a <b>{$persona->apellidos_nombres}</b> porque pertenece a <b>{$cursoErroneo}</b> y la asignatura ahora es exclusiva de otro curso.</p>";
                    }
                    DetalleListaAsignatura::whereIn('id_estudiante', $ajenosAEliminar->pluck('id_estudiante'))
                        ->where('id_lista_asignatura', $id_lista_asignatura)
                        ->delete();
                }

                // B. INSERCIÓN: IDs actuales en lista (limpios de inactivos y ajenos)
                $estudiantesEnLista = DetalleListaAsignatura::where('id_lista_asignatura', $id_lista_asignatura)
                    ->pluck('id_estudiante')
                    ->toArray();

                // C. FALTANTES: Agregar los que no estén y sigan activos en el curso actual
                $estudiantesFaltantes = Estudiante::with('persona')
                    ->where('id_curso', $asignatura->id_curso)
                    ->where('estado', 1)
                    ->whereNotIn('id_estudiante', $estudiantesEnLista)
                    ->get();

                if ($estudiantesFaltantes->isNotEmpty()) {
                    $nuevosDetalles = [];
                    foreach ($estudiantesFaltantes as $estudiante) {
                        $persona = $estudiante->persona;
                        $cambios[] = "<p class='text-success mb-2'><i class='fa-solid fa-user-plus me-2'></i> Se ha incorporado a la lista al estudiante <b>{$persona->apellidos_nombres}</b>.</p>";
                        $nuevosDetalles[] = [
                            'id_lista_asignatura' => $id_lista_asignatura,
                            'id_estudiante'       => $estudiante->id_estudiante,
                        ];
                    }
                    DetalleListaAsignatura::insert($nuevosDetalles);
                }

                // ====================================================================
                // LÓGICA PARA BLOQUE: MIXTO
                // ====================================================================
            } elseif ($asignatura->tipo_bloque === 'mixto' && is_null($asignatura->id_curso)) {
                // A. LIMPIEZA ÚNICAMENTE: Retirar estudiantes inactivos
                $detallesAEliminar = DetalleListaAsignatura::with('estudiante.persona')
                    ->where('id_lista_asignatura', $id_lista_asignatura)
                    ->whereHas('estudiante', fn($q) => $q->where('estado', 0))
                    ->get();

                if ($detallesAEliminar->isNotEmpty()) {
                    foreach ($detallesAEliminar as $detalle) {
                        $persona = $detalle->estudiante->persona;
                        $cambios[] = "<p class='text-danger mb-2'><i class='fa-solid fa-user-minus me-2'></i> Se ha retirado automáticamente de la lista mixta al estudiante <b>{$persona->apellidos_nombres}</b> (Retirado/Inactivo).</p>";
                    }
                    DetalleListaAsignatura::whereIn('id_estudiante', $detallesAEliminar->pluck('id_estudiante'))
                        ->where('id_lista_asignatura', $id_lista_asignatura)
                        ->delete();
                }
            }

            // 3. Recargar las relaciones obligatoriamente aplicando SIEMPRE el orden alfabético
            $lista_asignatura->load([
                'estudiantes' => function ($query) {
                    $query->join('personas', 'estudiantes.id_persona', '=', 'personas.id_persona')
                        ->orderBy('personas.apellido_paterno', 'asc')
                        ->orderBy('personas.apellido_materno', 'asc')
                        ->orderBy('personas.nombres', 'asc')
                        ->select('estudiantes.*');
                },
                'estudiantes.persona.usuario',
                'estudiantes.curso'
            ]);
        }

        // Obtención de cursos solo si la asignatura es mixta
        $cursos = collect();
        if ($asignatura->tipo_bloque === 'mixto') {
            $tipo_perfil = Auth::user()->persona?->tipo_perfil;
            $filtros = [];
            if ($tipo_perfil === 'DIRECTOR DE NIVEL') {
                $filtros['nivel'] = Auth::user()->persona?->docente?->id_nivel;
                $cursos = (new Curso())->get_cursos($filtros);
            } else {
                $cursos = (new Curso())->get_all_cursos();
            }
        }

        $nombre_asignatura = $asignatura->asignatura;

        return view('listas_asignaturas.details', [
            'head_title' => "LISTA DE $nombre_asignatura | $anio_gestion - $periodo",
            'lista_asignatura' => $lista_asignatura,
            'cambios' => $cambios,
            'cursos' => $cursos
        ]);
    }

    public function listar()
    {
        $this->authorize('viewAny', ListaAsignatura::class);

        $tipo_perfil = Auth::user()->persona?->tipo_perfil;
        $filtros = [];
        $listas_asignaturas = null;

        if ($tipo_perfil === 'DIRECTOR DE NIVEL') {
            $filtros['nivel'] = Auth::user()->persona?->docente?->id_nivel;
            $listas_asignaturas = (new ListaAsignatura())->get_listas_asignaturas($filtros);
        } elseif ($tipo_perfil === 'COORDINADOR') {
            $filtros['coordinacion'] = Auth::user()->persona?->docente?->id_coordinacion;
            $listas_asignaturas = (new ListaAsignatura())->get_listas_asignaturas($filtros);
        } elseif ($tipo_perfil === 'DOCENTE') {
            $filtros['docente'] = Auth::user()->persona?->docente?->id_docente;
            $listas_asignaturas = (new ListaAsignatura())->get_listas_asignaturas($filtros);
        } else {
            $listas_asignaturas = (new ListaAsignatura())->get_all_listas_asignaturas();
        }

        return response()->json([
            'data' => $listas_asignaturas,
        ]);
    }

    public function mostrar(Request $request)
    {
        $lista_asignatura = (new ListaAsignatura())->get_lista_asignatura($request->lista_asignatura);

        $this->authorize('view', $lista_asignatura);

        return response()->json([
            'data' => $lista_asignatura
        ]);
    }

    public function update(Request $request)
    {
        // Permite que 'estudiantes' sea nullable por si envían la lista vacía (borraron a todos)
        $request->validate([
            'id_lista_asignatura' => 'required|exists:listas_asignaturas,id_lista_asignatura',
            'estudiantes' => 'nullable|array',
            'estudiantes.*' => 'exists:estudiantes,id_estudiante'
        ]);

        $lista_asignatura = (new ListaAsignatura())->get_lista_asignatura($request->id_lista_asignatura);

        $this->authorize('update', $lista_asignatura);

        if ($lista_asignatura->asignatura->tipo_bloque !== 'mixto') {
            return response()->json(['success' => false, 'message' => 'La lista de asignatura no es de tipo mixto.'], 400);
        }

        // 1. Extraer los IDs actuales de la BD y los nuevos del Request
        $estudiantes_actuales = DetalleListaAsignatura::where('id_lista_asignatura', $request->id_lista_asignatura)
            ->pluck('id_estudiante')
            ->toArray();

        $estudiantes_nuevos = $request->estudiantes ?? []; // Si es null, lo tratamos como array vacío

        // 2. Calcular las diferencias (Magia de PHP)
        $agregar = array_diff($estudiantes_nuevos, $estudiantes_actuales);
        $eliminar = array_diff($estudiantes_actuales, $estudiantes_nuevos);

        // 3. Ejecutar la eliminación masiva
        if (!empty($eliminar)) {
            DetalleListaAsignatura::where('id_lista_asignatura', $request->id_lista_asignatura)
                ->whereIn('id_estudiante', $eliminar)
                ->delete();
        }

        // 4. Ejecutar la inserción masiva
        if (!empty($agregar)) {
            $nuevosDetalles = [];
            foreach ($agregar as $id_estudiante) {
                $nuevosDetalles[] = [
                    'id_lista_asignatura' => $request->id_lista_asignatura,
                    'id_estudiante'       => $id_estudiante,
                ];
            }
            DetalleListaAsignatura::insert($nuevosDetalles);
        }

        // ====================================================================
        // 5. LÓGICA DE REPLICACIÓN PARA PERIODOS SUCESIVOS
        // ====================================================================
        $periodosReplicados = []; // Array para capturar los nombres de los periodos llenados

        // Solo intentamos replicar si la lista resultante no está vacía
        if (!empty($estudiantes_nuevos)) {

            // Obtenemos los parámetros del periodo actual
            $idGestionActual = $lista_asignatura->periodo->id_gestion;
            $posicionActual = $lista_asignatura->periodo->posicion_ordinal;
            $idAsignatura = $lista_asignatura->id_asignatura;

            // Buscamos listas de la misma asignatura, misma gestión, pero periodos posteriores
            $listasSucesivas = ListaAsignatura::with('periodo') // Añadimos with('periodo') para poder leer su nombre
                ->where('id_asignatura', $idAsignatura)
                ->whereHas('periodo', function ($query) use ($idGestionActual, $posicionActual) {
                    $query->where('id_gestion', $idGestionActual)
                        ->where('posicion_ordinal', '>', $posicionActual);
                })
                ->get();

            if ($listasSucesivas->isNotEmpty()) {
                foreach ($listasSucesivas as $listaSucesiva) {
                    // Verificamos si la lista sucesiva está completamente vacía
                    $cantidadInscritos = DetalleListaAsignatura::where('id_lista_asignatura', $listaSucesiva->id_lista_asignatura)->count();

                    if ($cantidadInscritos === 0) {
                        $detallesSucesivos = [];
                        foreach ($estudiantes_nuevos as $id_estudiante) {
                            $detallesSucesivos[] = [
                                'id_lista_asignatura' => $listaSucesiva->id_lista_asignatura,
                                'id_estudiante'       => $id_estudiante,
                            ];
                        }
                        // Inserción masiva optimizada para el periodo posterior
                        DetalleListaAsignatura::insert($detallesSucesivos);

                        // Registramos el periodo afectado para notificar al frontend
                        $periodosReplicados[] = "<b>{$listaSucesiva->periodo->periodo}</b>";
                    }
                }
            }
        }

        // ====================================================================
        // 6. PREPARAR EL MENSAJE FINAL
        // ====================================================================
        $mensajeFinal = 'La lista de estudiantes se ha actualizado correctamente.';

        // Si el array tiene elementos, concatenamos el mensaje adicional
        if (!empty($periodosReplicados)) {
            // Unimos los periodos con " y " (ej. "SEGUNDO TRIMESTRE y TERCER TRIMESTRE")
            $nombresPeriodos = implode(' y ', $periodosReplicados);
            $mensajeFinal .= "<br><br><i class='fa-solid fa-clone text-info me-1'></i>Además, se han llenado automáticamente las listas de: {$nombresPeriodos}.";
        }

        return response()->json([
            'success' => true,
            'message' => $mensajeFinal,
        ]);
    }

    public function actualizar_docente(Request $request)
    {
        $request->validate([
            'docente' => 'nullable|exists:docentes,id_docente',
        ]);

        // Regla especial fuera de la política: Los docentes no pueden modificar el docente asignado a la lista
        if (Auth::user()->persona?->tipo_perfil === 'DOCENTE') {
            return response()->json([
                'success' => false,
                'message' => 'Acceso denegado: Los docentes no pueden modificar el docente asignado a la lista.'
            ], 403);
        }

        $lista_asignatura = (new ListaAsignatura())->get_lista_asignatura($request->lista_asignatura);

        $this->authorize('update', $lista_asignatura);

        $lista_asignatura->id_docente = $request->docente;
        $lista_asignatura->modificado_por = auth()->id();
        $lista_asignatura->ip = $request->ip();
        $lista_asignatura->dispositivo = $request->userAgent();
        $lista_asignatura->save();

        $nuevoDocente = $lista_asignatura->docente()->with('persona')->first();
        return response()->json([
            'success' => true,
            'message' => 'El/la docente de la lista seleccionada se ha actualizado correctamente.',
            'lista_asignatura' => $lista_asignatura,
            'nuevoDocente' => $nuevoDocente
        ]);
    }

    public function importar_cursos(Request $request, int $id_lista_asignatura)
    {
        $request->validate([
            'cursos' => 'required|array|min:1',
            'cursos.*' => 'exists:cursos,id_curso'
        ], [
            'cursos.required' => 'Debe seleccionar al menos un curso.',
            'cursos.min' => 'Debe seleccionar al menos un curso.'
        ]);

        $lista_asignatura = (new ListaAsignatura())->get_lista_asignatura($id_lista_asignatura);
        $this->authorize('update', $lista_asignatura);

        if ($lista_asignatura->asignatura->tipo_bloque !== 'mixto') {
            return response()->json(['success' => false, 'message' => 'Esta acción solo está disponible para listas mixtas.'], 400);
        }

        // 1. IDs ya existentes en la lista
        $existentesIds = DetalleListaAsignatura::where('id_lista_asignatura', $id_lista_asignatura)
            ->pluck('id_estudiante')
            ->toArray();

        // 2. Buscar estudiantes activos de los cursos seleccionados que NO estén ya en la lista
        $estudiantesAInsertar = Estudiante::whereIn('id_curso', $request->cursos)
            ->where('estado', 1)
            ->whereNotIn('id_estudiante', $existentesIds)
            ->pluck('id_estudiante')
            ->toArray();

        if (empty($estudiantesAInsertar)) {
            return response()->json([
                'success' => false,
                'message' => 'Los estudiantes de los cursos seleccionados ya se encuentran agregados en esta lista.'
            ], 422);
        }

        // 3. Inserción masiva en la lista actual
        $detalles = [];
        foreach ($estudiantesAInsertar as $id_estudiante) {
            $detalles[] = [
                'id_lista_asignatura' => $id_lista_asignatura,
                'id_estudiante'       => $id_estudiante,
            ];
        }
        DetalleListaAsignatura::insert($detalles);

        // 4. Replicación en periodos posteriores si están vacíos
        $periodosReplicados = [];
        $idGestionActual = $lista_asignatura->periodo->id_gestion;
        $posicionActual = $lista_asignatura->periodo->posicion_ordinal;
        $idAsignatura = $lista_asignatura->id_asignatura;

        $listasSucesivas = ListaAsignatura::with('periodo')
            ->where('id_asignatura', $idAsignatura)
            ->whereHas('periodo', function ($query) use ($idGestionActual, $posicionActual) {
                $query->where('id_gestion', $idGestionActual)
                    ->where('posicion_ordinal', '>', $posicionActual);
            })
            ->get();

        if ($listasSucesivas->isNotEmpty()) {
            // Obtenemos todos los estudiantes que tiene ahora la lista completa
            $totalEstudiantesIds = DetalleListaAsignatura::where('id_lista_asignatura', $id_lista_asignatura)
                ->pluck('id_estudiante')
                ->toArray();

            foreach ($listasSucesivas as $listaSucesiva) {
                $cantidad = DetalleListaAsignatura::where('id_lista_asignatura', $listaSucesiva->id_lista_asignatura)->count();
                if ($cantidad === 0) {
                    $detallesSucesivos = [];
                    foreach ($totalEstudiantesIds as $id_est) {
                        $detallesSucesivos[] = [
                            'id_lista_asignatura' => $listaSucesiva->id_lista_asignatura,
                            'id_estudiante'       => $id_est,
                        ];
                    }
                    DetalleListaAsignatura::insert($detallesSucesivos);
                    $periodosReplicados[] = "<b>{$listaSucesiva->periodo->periodo}</b>";
                }
            }
        }

        $cantInsertados = count($estudiantesAInsertar);
        $mensaje = "Se han incorporado exitosamente {$cantInsertados} estudiantes a la lista.";
        if (!empty($periodosReplicados)) {
            $nombresPeriodos = implode(' y ', $periodosReplicados);
            $mensaje .= "<br><br><i class='fa-solid fa-clone text-info me-1'></i>Además, se han llenado automáticamente las listas de: {$nombresPeriodos}.";
        }

        return response()->json([
            'success' => true,
            'message' => $mensaje
        ]);
    }

    public function horario_docente(Request $request)
    {
        $tipo_perfil = Auth::user()->persona?->tipo_perfil;

        if ($tipo_perfil === 'DOCENTE') {
            $id_docente = Auth::user()->persona?->docente?->id_docente;
        } else {
            $id_docente = $request->id_docente;
        }

        if (!$id_docente) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene un perfil de docente asignado.'
            ], 403);
        }

        $now = now();
        $fecha_hoy = $now->format('Y-m-d');

        // La semana inicia en Lunes
        $inicio_semana = $now->copy()->startOfWeek()->format('Y-m-d');
        $fin_semana = $now->copy()->endOfWeek()->format('Y-m-d');

        $listas = (new ListaAsignatura())->get_horario_semanal_docente($id_docente, $fecha_hoy, $inicio_semana, $fin_semana);

        // Obtenemos el nombre del periodo si hay listas, de lo contrario mostramos un fallback
        $periodo_actual = $listas->first()?->periodo?->periodo ?? 'SIN PERIODO ACTIVO';

        return response()->json([
            'success'             => true,
            'fecha_hoy'           => $fecha_hoy,
            'fecha_inicio_semana' => $inicio_semana,
            'periodo_actual'      => $periodo_actual,
            'data'                => $listas
        ]);
    }

    public function horario_estudiante(Request $request)
    {
        $tipo_perfil = Auth::user()->persona?->tipo_perfil;

        if ($tipo_perfil === 'ESTUDIANTE') {
            $id_estudiante = Auth::user()->persona?->estudiante?->id_estudiante;
        } else {
            $id_estudiante = $request->id_estudiante;
        }

        if (!$id_estudiante) {
            return response()->json([
                'success' => false,
                'message' => 'El usuario no tiene un perfil de estudiante asignado o no se especificó un estudiante.'
            ], 403);
        }

        $now = now();
        $fecha_hoy = $now->format('Y-m-d');

        // La semana inicia en Lunes
        $inicio_semana = $now->copy()->startOfWeek()->format('Y-m-d');
        $fin_semana = $now->copy()->endOfWeek()->format('Y-m-d');

        $listas = (new ListaAsignatura())->get_horario_semanal_estudiante($id_estudiante, $fecha_hoy, $inicio_semana, $fin_semana);

        // Obtenemos el periodo actual evaluando que $fecha_hoy esté entre fecha_inicio y fecha_fin
        $periodo_actual = $listas->first()?->periodo?->periodo
            ?? Periodo::where('estado', 1)
            ->whereDate('fecha_inicio', '<=', $fecha_hoy)
            ->whereDate('fecha_fin', '>=', $fecha_hoy)
            ->value('periodo')
            ?? 'SIN PERIODO ACTIVO';

        return response()->json([
            'success'             => true,
            'fecha_hoy'           => $fecha_hoy,
            'fecha_inicio_semana' => $inicio_semana,
            'fin_semana'          => $fin_semana,
            'periodo_actual'      => $periodo_actual,
            'data'                => $listas
        ]);
    }

    public function view_planilla_asistencia(Request $request, int $id_lista_asignatura)
    {
        // 1. Obtener la lista con metadatos institucionales y asistencias dentro del periodo
        $lista_asignatura = (new ListaAsignatura())->get_datos_planilla_asistencia($id_lista_asignatura);

        $this->authorize('view', $lista_asignatura);

        $asignatura = $lista_asignatura->asignatura;
        $anio_gestion = $lista_asignatura->periodo->gestion->anio;
        $anio_actual = date('Y');

        // 2. Sincronizar el padrón actual si es la gestión vigente y el periodo está activo
        if ($anio_gestion == $anio_actual && $lista_asignatura->periodo->estado == 1) {
            if ($asignatura->tipo_bloque === 'curso' && !is_null($asignatura->id_curso)) {
                // Retirar inactivos o de otro curso de la lista actual
                DetalleListaAsignatura::where('id_lista_asignatura', $id_lista_asignatura)
                    ->whereHas('estudiante', function ($q) use ($asignatura) {
                        $q->where('estado', 0)
                            ->orWhere('id_curso', '!=', $asignatura->id_curso);
                    })
                    ->delete();

                // Incorporar estudiantes activos faltantes del curso
                $enListaIds = DetalleListaAsignatura::where('id_lista_asignatura', $id_lista_asignatura)
                    ->pluck('id_estudiante')
                    ->toArray();

                $faltantesIds = Estudiante::where('id_curso', $asignatura->id_curso)
                    ->where('estado', 1)
                    ->whereNotIn('id_estudiante', $enListaIds)
                    ->pluck('id_estudiante')
                    ->toArray();

                if (!empty($faltantesIds)) {
                    $nuevos = array_map(fn($id_est) => [
                        'id_lista_asignatura' => $id_lista_asignatura,
                        'id_estudiante'       => $id_est,
                    ], $faltantesIds);
                    DetalleListaAsignatura::insert($nuevos);
                }
            } elseif ($asignatura->tipo_bloque === 'mixto' && is_null($asignatura->id_curso)) {
                // En bloque mixto solo retiramos estudiantes inactivos (estado = 0)
                DetalleListaAsignatura::where('id_lista_asignatura', $id_lista_asignatura)
                    ->whereHas('estudiante', fn($q) => $q->where('estado', 0))
                    ->delete();
            }
        }

        // 3. Obtener IDs de estudiantes ACTUALMENTE inscritos en la lista
        $idsActuales = DetalleListaAsignatura::where('id_lista_asignatura', $id_lista_asignatura)
            ->pluck('id_estudiante')
            ->toArray();
        $mapaActuales = array_flip($idsActuales); // Búsqueda O(1)

        // 4. Obtener IDs de estudiantes HISTÓRICOS (que tuvieron asistencias en este periodo y fueron retirados)
        $asistencias = $lista_asignatura->estudiantes_asistencias;
        $idsHistoricos = $asistencias->flatMap(function ($asistencia) {
            return $asistencia->detalles_estudiantes_asistencias->pluck('id_estudiante');
        })->unique()->values()->toArray();

        // 5. Unión completa de estudiantes involucrados (Actuales + Retirados)
        $todosLosIds = array_values(array_unique(array_merge($idsActuales, $idsHistoricos)));

        // Consultar datos completos de todos los estudiantes involucrados ordenados alfabéticamente
        $estudiantesInvolucrados = collect();
        if (!empty($todosLosIds)) {
            $estudiantesInvolucrados = Estudiante::select('estudiantes.*')
                ->join('personas', 'estudiantes.id_persona', '=', 'personas.id_persona')
                ->with([
                    'persona:id_persona,apellido_paterno,apellido_materno,nombres,estado',
                    'curso:id_curso,curso,estado'
                ])
                ->whereIn('estudiantes.id_estudiante', $todosLosIds)
                ->orderBy('personas.apellido_paterno', 'ASC')
                ->orderBy('personas.apellido_materno', 'ASC')
                ->orderBy('personas.nombres', 'ASC')
                ->get();
        }

        // 6. Construir encabezados multinivel (Meses -> Días/Horarios -> Fechas) y mapa O(1) de detalles
        $mesesNombres = [
            1 => 'ENERO',
            2 => 'FEBRERO',
            3 => 'MARZO',
            4 => 'ABRIL',
            5 => 'MAYO',
            6 => 'JUNIO',
            7 => 'JULIO',
            8 => 'AGOSTO',
            9 => 'SEPTIEMBRE',
            10 => 'OCTUBRE',
            11 => 'NOVIEMBRE',
            12 => 'DICIEMBRE'
        ];
        $diasNombres = [
            1 => 'LUNES',
            2 => 'MARTES',
            3 => 'MIÉRCOLES',
            4 => 'JUEVES',
            5 => 'VIERNES',
            6 => 'SÁBADO',
            7 => 'DOMINGO'
        ];
        $simbolosMapa = [
            'PRESENTE' => '.',
            'FALTA'    => 'F',
            'ATRASO'   => 'A',
            'LICENCIA' => 'L',
            'PERMISO'  => 'P',
            'OTROS'    => 'O',
        ];

        $columnasClases = [];
        $agrupacionMeses = [];
        $mapaDetalles = []; // $mapaDetalles[$id_estudiante][$id_estudiante_asistencia] = detalle

        // Detectar si en una misma fecha hubo más de 1 asistencia para mostrar la denominación del bloque (ej. 1°, 4°)
        $conteoPorFecha = $asistencias->groupBy('fecha')->map->count();

        foreach ($asistencias as $asistencia) {
            $carbonFecha = Carbon::parse($asistencia->fecha);
            $numMes = (int) $carbonFecha->format('n');
            $nombreMes = $mesesNombres[$numMes];
            $nombreDia = $diasNombres[(int) $carbonFecha->format('N')];
            $diaNumero = $carbonFecha->format('d');

            $denominacionHorario = $asistencia->horario_asignatura?->denominacion ?? '';
            $horaInicio = $asistencia->horario_asignatura
                ? substr($asistencia->horario_asignatura->hora_inicio, 0, 5)
                : '';

            // Si hay más de una clase el mismo día, o siempre para mayor claridad del docente, adjuntamos el periodo horario
            $etiquetaDia = $denominacionHorario !== ''
                ? "{$nombreDia} ({$denominacionHorario})"
                : $nombreDia;

            // Agrupación para el colspan de meses (Nivel 1 del encabezado)
            if (!isset($agrupacionMeses[$numMes])) {
                $agrupacionMeses[$numMes] = [
                    'mes'     => $nombreMes,
                    'colspan' => 0
                ];
            }
            $agrupacionMeses[$numMes]['colspan']++;

            // Información de cada columna de clase (Niveles 2 y 3 del encabezado)
            $columnasClases[] = [
                'id_estudiante_asistencia' => $asistencia->id_estudiante_asistencia,
                'fecha_completa'           => $carbonFecha->format('d/m/Y'),
                'mes'                      => $nombreMes,
                'dia_semana'               => $nombreDia,
                'etiqueta_dia'             => $etiquetaDia,
                'dia_numero'               => (int) $diaNumero,
                'denominacion_horario'     => $denominacionHorario,
                'hora_inicio'              => $horaInicio,
                'es_multiple_en_fecha'     => ($conteoPorFecha[$asistencia->fecha] ?? 1) > 1,
                'justificacion_otros'      => $asistencia->justificacion_otros,
            ];

            // Indexar detalles en memoria para acceso instantáneo O(1)
            foreach ($asistencia->detalles_estudiantes_asistencias as $detalle) {
                $mapaDetalles[$detalle->id_estudiante][$asistencia->id_estudiante_asistencia] = [
                    'tipo'          => $detalle->tipo,
                    'simbolo'       => $simbolosMapa[$detalle->tipo] ?? '-',
                    'tiempo_atraso' => $detalle->tiempo_atraso,
                ];
            }
        }

        // 7. Construir las filas de estudiantes con sus asistencias por clase y sus 6 contadores
        $filasEstudiantes = [];
        foreach ($estudiantesInvolucrados as $index => $estudiante) {
            $idEst = $estudiante->id_estudiante;
            $estaEnListaActual = isset($mapaActuales[$idEst]) && $estudiante->estado == 1;

            $contadores = [
                'PRESENTE' => 0,
                'FALTA'    => 0,
                'ATRASO'   => 0,
                'LICENCIA' => 0,
                'PERMISO'  => 0,
                'OTROS'    => 0,
            ];

            $celdasAsistencia = [];

            foreach ($columnasClases as $col) {
                $idAsis = $col['id_estudiante_asistencia'];

                if (isset($mapaDetalles[$idEst][$idAsis])) {
                    $registro = $mapaDetalles[$idEst][$idAsis];
                    $tipo = $registro['tipo'];

                    if (isset($contadores[$tipo])) {
                        $contadores[$tipo]++;
                    }

                    $celdasAsistencia[] = [
                        'id_estudiante_asistencia' => $idAsis,
                        'registrado'               => true,
                        'tipo'                     => $tipo,
                        'simbolo'                  => $registro['simbolo'],
                        'tiempo_atraso'            => $registro['tiempo_atraso'],
                    ];
                } else {
                    // El estudiante no formaba parte de la lista en esa fecha (incorporación posterior o ya retirado)
                    $celdasAsistencia[] = [
                        'id_estudiante_asistencia' => $idAsis,
                        'registrado'               => false,
                        'tipo'                     => null,
                        'simbolo'                  => '/',
                        'tiempo_atraso'            => 0,
                    ];
                }
            }

            $filasEstudiantes[] = [
                'numero'            => $index + 1,
                'id_estudiante'     => $idEst,
                'apellidos_nombres' => $estudiante->persona->apellidos_nombres,
                'nombres_apellidos' => $estudiante->persona->nombres_apellidos,
                'curso'             => $estudiante->curso?->curso ?? 'S/C',
                'en_lista_actual'   => $estaEnListaActual, // false = pintar fila de color gris (Retirado)
                'estado_etiqueta'   => $estaEnListaActual ? 'ACTIVO' : 'RETIRADO',
                'asistencias'       => $celdasAsistencia,
                'totales'           => $contadores,
            ];
        }

        // 8. Estructurar los metadatos del Encabezado Institucional
        $encabezado = [
            'unidad_educativa' => 'FEDERICO FROEBEL',
            'gestion_periodo'  => "{$lista_asignatura->periodo->gestion->anio} - {$lista_asignatura->periodo->periodo}",
            'anio'             => $lista_asignatura->periodo->gestion->anio,
            'periodo'          => $lista_asignatura->periodo->periodo,
            'docente'          => $lista_asignatura->docente?->persona?->nombres_apellidos ?? 'SIN DOCENTE ASIGNADO',
            'area'             => $asignatura->area?->area ?? 'SIN ÁREA',
            'campo'            => $asignatura->area?->campo?->campo ?? 'SIN CAMPO',
            'curso'            => $asignatura->asignatura,
            'tipo_bloque'      => $asignatura->tipo_bloque,
        ];

        $planillaData = [
            'id_lista_asignatura' => $lista_asignatura->id_lista_asignatura,
            'encabezado'          => $encabezado,
            'meses'               => array_values($agrupacionMeses),
            'columnas'            => $columnasClases,
            'estudiantes'         => $filasEstudiantes,
        ];

        // Si la petición se hace vía AJAX (por ejemplo, para refrescar o alimentar ExcelJS bajo demanda), responde JSON
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data'    => $planillaData
            ]);
        }

        // Renderiza la vista Blade pasando toda la estructura lista tanto para la tabla HTML como para @json($planillaData) en ExcelJS
        return view('listas_asignaturas.planilla_asistencia', [
            'head_title'       => "PLANILLA DE ASISTENCIA | {$asignatura->asignatura} ({$encabezado['gestion_periodo']})",
            'lista_asignatura' => $lista_asignatura,
            'planilla'         => $planillaData,
        ]);
    }
}
