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

        // 1. Obtener la lista y sus relaciones básicas
        $lista_asignatura = (new ListaAsignatura())->get_lista_asignatura($id_lista_asignatura);
        $asignatura = $lista_asignatura->asignatura;

        $periodo = $lista_asignatura->periodo->periodo;
        $anio_gestion = $lista_asignatura->periodo->gestion->anio;
        $anio_actual = date('Y'); // Obtiene el año actual del sistema

        $cambios = []; // Array para registrar los mensajes de cambios

        // 2. Verificar que la lista sea de la gestión actual, que sea por "curso" y tenga un id_curso y el periodo esté activo
        if ($anio_gestion == $anio_actual && $asignatura->tipo_bloque === 'curso' && !is_null($asignatura->id_curso) && $lista_asignatura->periodo->estado == 1) {

            // A. LIMPIEZA: Identificar a los estudiantes inactivos antes de borrarlos para capturar sus nombres
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
                    $cambios[] = "<p class='text-danger mb-2'><i class='fa-solid fa-user-minus me-2'></i> Se ha retirado de la lista al estudiante <b>{$nombreCompleto}</b> (Retirado/Inactivo).</p>";
                }

                // Proceder con la eliminación masiva
                DetalleListaAsignatura::whereIn('id_estudiante', $detallesAEliminar->pluck('id_estudiante'))
                    ->where('id_lista_asignatura', $id_lista_asignatura)
                    ->delete();
            }

            // B. INSERCIÓN: IDs de los estudiantes que (tras la limpieza) SÍ están en la lista
            $estudiantesEnLista = DetalleListaAsignatura::where('id_lista_asignatura', $id_lista_asignatura)
                ->pluck('id_estudiante')
                ->toArray();

            // C. FALTANTES: Obtener los modelos completos de los activos faltantes para tener sus nombres
            $estudiantesFaltantes = Estudiante::with('persona')
                ->where('id_curso', $asignatura->id_curso)
                ->where('estado', 1)
                ->whereNotIn('id_estudiante', $estudiantesEnLista)
                ->get();

            // Preparar el array para inserción masiva y los mensajes si hay faltantes
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

                // Insertar todos los registros faltantes en una sola consulta
                DetalleListaAsignatura::insert($nuevosDetalles);
            }

            // 3. Recargar las relaciones obligatoriamente si hubo algún cambio
            if (!empty($cambios)) {
                $lista_asignatura->load([
                    'estudiantes.persona.usuario',
                    'estudiantes.curso'
                ]);
            }
        }

        $nombre_asignatura = $asignatura->asignatura;

        return view('listas_asignaturas.details', [
            'head_title' => "LISTA DE $nombre_asignatura | $anio_gestion - $periodo",
            'lista_asignatura' => $lista_asignatura,
            'cambios' => $cambios // Pasamos la variable a la vista
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
