<?php

namespace App\Http\Controllers;

use App\Models\DetalleListaAsignatura;
use App\Models\Estudiante;
use App\Models\ListaAsignatura;
use Illuminate\Http\Request;

class ListaAsignaturaController extends Controller
{
    public function view_details($id_lista_asignatura)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return redirect()->route('login');
        }

        // 1. Obtener la lista y cargar los estudiantes ordenados alfabéticamente
        $lista_asignatura = (new ListaAsignatura())->get_lista_asignatura($id_lista_asignatura);

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

                // A.1 LIMPIEZA: Identificar a los estudiantes inactivos
                $inactivosAEliminar = DetalleListaAsignatura::with('estudiante.persona')
                    ->where('id_lista_asignatura', $id_lista_asignatura)
                    ->whereHas('estudiante', function ($query) {
                        $query->where('estado', 0);
                    })
                    ->get();

                if ($inactivosAEliminar->isNotEmpty()) {
                    foreach ($inactivosAEliminar as $detalle) {
                        $persona = $detalle->estudiante->persona;
                        $nombreCompleto = trim($persona->apellido_paterno . ' ' . $persona->apellido_materno . ' ' . $persona->nombres);
                        $cambios[] = "<p class='text-danger mb-2'><i class='fa-solid fa-user-minus me-2'></i> Se ha retirado de la lista al estudiante <b>{$nombreCompleto}</b> (Retirado/Inactivo).</p>";
                    }

                    DetalleListaAsignatura::whereIn('id_estudiante', $inactivosAEliminar->pluck('id_estudiante'))
                        ->where('id_lista_asignatura', $id_lista_asignatura)
                        ->delete();
                }

                // A.2 LIMPIEZA ADICIONAL: Eliminar estudiantes de otros cursos (ej. cambio de mixto a curso)
                $ajenosAEliminar = DetalleListaAsignatura::with('estudiante.persona', 'estudiante.curso')
                    ->where('id_lista_asignatura', $id_lista_asignatura)
                    ->whereHas('estudiante', function ($query) use ($asignatura) {
                        $query->where('id_curso', '!=', $asignatura->id_curso);
                    })
                    ->get();

                if ($ajenosAEliminar->isNotEmpty()) {
                    foreach ($ajenosAEliminar as $detalle) {
                        $persona = $detalle->estudiante->persona;
                        $cursoErroneo = $detalle->estudiante->curso->curso ?? 'Otro curso';
                        $nombreCompleto = trim($persona->apellido_paterno . ' ' . $persona->apellido_materno . ' ' . $persona->nombres);
                        $cambios[] = "<p class='text-danger mb-2'><i class='fa-solid fa-user-slash me-2'></i> Se ha retirado a <b>{$nombreCompleto}</b> porque pertenece a <b>{$cursoErroneo}</b> y la asignatura ahora es exclusiva de otro curso.</p>";
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
                        $nombreCompleto = trim($persona->apellido_paterno . ' ' . $persona->apellido_materno . ' ' . $persona->nombres);
                        $cambios[] = "<p class='text-success mb-2'><i class='fa-solid fa-user-plus me-2'></i> Se ha incorporado a la lista al estudiante <b>{$nombreCompleto}</b>.</p>";

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
                    ->whereHas('estudiante', function ($query) {
                        $query->where('estado', 0);
                    })
                    ->get();

                if ($detallesAEliminar->isNotEmpty()) {
                    foreach ($detallesAEliminar as $detalle) {
                        $persona = $detalle->estudiante->persona;
                        $nombreCompleto = trim($persona->apellido_paterno . ' ' . $persona->apellido_materno . ' ' . $persona->nombres);
                        $cambios[] = "<p class='text-danger mb-2'><i class='fa-solid fa-user-minus me-2'></i> Se ha retirado automáticamente de la lista mixta al estudiante <b>{$nombreCompleto}</b> (Retirado/Inactivo).</p>";
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

        $nombre_asignatura = $asignatura->asignatura;

        return view('listas_asignaturas.details', [
            'head_title' => "LISTA DE $nombre_asignatura | $anio_gestion - $periodo",
            'lista_asignatura' => $lista_asignatura,
            'cambios' => $cambios
        ]);
    }

    public function mostrar(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $lista_asignatura = (new ListaAsignatura())->get_lista_asignatura($request->lista_asignatura);

        return response()->json([
            'data' => $lista_asignatura
        ]);
    }

    public function update(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return redirect()->route('login');
        }

        // Permite que 'estudiantes' sea nullable por si envían la lista vacía (borraron a todos)
        $request->validate([
            'id_lista_asignatura' => 'required|exists:listas_asignaturas,id_lista_asignatura',
            'estudiantes' => 'nullable|array',
            'estudiantes.*' => 'exists:estudiantes,id_estudiante'
        ]);

        $lista_asignatura = (new ListaAsignatura())->get_lista_asignatura($request->id_lista_asignatura);

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

        return response()->json([
            'success' => true,
            'message' => 'La lista de estudiantes se ha actualizado correctamente.',
        ]);
    }

    public function actualizar_docente(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'docente' => 'required|exists:docentes,id_docente',
        ]);

        $lista_asignatura = (new ListaAsignatura())->get_lista_asignatura($request->lista_asignatura);
        $lista_asignatura->id_docente = $request->docente;
        $lista_asignatura->modificado_por = session('id_usuario');
        $lista_asignatura->ip = session('ip');
        $lista_asignatura->dispositivo = session('dispositivo');
        $lista_asignatura->save();

        $nuevoDocente = $lista_asignatura->docente()->with('persona')->first();
        return response()->json([
            'success' => true,
            'message' => 'El/la docente de la lista seleccionada se ha actualizado correctamente.',
            'lista_asignatura' => $lista_asignatura,
            'nuevoDocente' => $nuevoDocente
        ]);
    }
}
