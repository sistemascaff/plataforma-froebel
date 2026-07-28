<?php

namespace App\Http\Controllers;

use App\Http\Requests\AsignaturaValidation;
use App\Models\Area;
use App\Models\Asignatura;
use App\Models\Aula;
use App\Models\Coordinacion;
use App\Models\Curso;
use App\Models\ListaAsignatura;
use App\Models\Materia;
use App\Models\Nivel;
use App\Models\Periodo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsignaturaController extends Controller
{
    public function view_index()
    {
        $materias = (new Materia())->get_all_materias();
        $areas = (new Area())->get_all_areas();
        $aulas = (new Aula())->get_all_aulas();
        $niveles = (new Nivel())->get_all_niveles();
        $coordinaciones = (new Coordinacion())->get_all_coordinaciones();
        $cursos = (new Curso())->get_all_cursos();

        return view('asignaturas.index', [
            'head_title' => 'GESTIÓN DE ASIGNATURAS',
            'materias' => $materias,
            'areas' => $areas,
            'aulas' => $aulas,
            'niveles' => $niveles,
            'coordinaciones' => $coordinaciones,
            'cursos' => $cursos,
        ]);
    }

    public function view_details(Request $request, int $asignatura)
    {
        // IDs de periodos activos que YA tienen lista para esta asignatura
        $periodosConLista = ListaAsignatura::where('id_asignatura', $asignatura)
            ->pluck('id_periodo')
            ->toArray();

        // Periodos activos que AÚN NO tienen lista → crearlos
        Periodo::where('estado', 1)
            ->whereNotIn('id_periodo', $periodosConLista)
            ->each(function ($periodo) use ($asignatura, $request) {
                $lista = new ListaAsignatura();
                $lista->id_asignatura = $asignatura;
                $lista->id_periodo = $periodo->id_periodo;
                $lista->creado_por = auth()->id() ?? 0;
                $lista->ip = $request->ip();
                $lista->dispositivo = $request->userAgent();
                $lista->save();
            });

        // Se carga DESPUÉS de generar las listas para que el eager loading las incluya todas
        $asignatura = (new Asignatura())->get_asignatura($asignatura);

        return view('asignaturas.details', [
            'head_title' => 'ASIGNATURA: '.$asignatura->asignatura,
            'asignatura' => $asignatura,
        ]);
    }

    public function listar()
    {
        $asignaturas = (new Asignatura())->get_all_asignaturas();

        return response()->json([
            'data' => $asignaturas,
        ]);
    }

    public function mostrar(Request $request)
    {
        $asignatura = (new Asignatura())->get_asignatura($request->asignatura);

        return response()->json([
            'data' => $asignatura,
        ]);
    }

    public function create(AsignaturaValidation $request)
    {
        $asignatura = new Asignatura();
        $asignatura->asignatura = $request->asignatura;
        $asignatura->tipo_calificacion = $request->tipo_calificacion;
        $asignatura->tipo_bloque = $request->tipo_bloque;
        $asignatura->id_materia = $request->id_materia;
        $asignatura->id_area = $request->id_area;
        $asignatura->id_aula = $request->id_aula;
        $asignatura->id_nivel = $request->id_nivel;
        $asignatura->id_coordinacion = $request->id_coordinacion;
        $asignatura->id_curso = $request->tipo_bloque === 'curso' ? $request->id_curso : null;
        $asignatura->creado_por = auth()->id();
        $asignatura->ip = $request->ip();
        $asignatura->dispositivo = $request->userAgent();
        $asignatura->save();

        return response()->json([
            'success' => true,
            'message' => 'Asignatura creada correctamente',
            'asignatura' => $asignatura,
        ]);
    }

    public function update(AsignaturaValidation $request, int $id_asignatura)
    {
        $asignatura = (new Asignatura())->get_asignatura($id_asignatura);
        $asignatura->asignatura = $request->asignatura;
        $asignatura->tipo_calificacion = $request->tipo_calificacion;
        $asignatura->tipo_bloque = $request->tipo_bloque;
        $asignatura->id_materia = $request->id_materia;
        $asignatura->id_area = $request->id_area;
        $asignatura->id_aula = $request->id_aula;
        $asignatura->id_nivel = $request->id_nivel;
        $asignatura->id_coordinacion = $request->id_coordinacion;
        $asignatura->id_curso = $request->tipo_bloque === 'curso' ? $request->id_curso : null;
        $asignatura->modificado_por = auth()->id();
        $asignatura->ip = $request->ip();
        $asignatura->dispositivo = $request->userAgent();
        $asignatura->save();

        return response()->json([
            'success' => true,
            'message' => 'Asignatura actualizada correctamente',
            'asignatura' => $asignatura,
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id_asignatura' => ['required', 'numeric', 'integer', 'exists:asignaturas,id_asignatura'],
        ]);

        $asignatura = (new Asignatura())->get_asignatura($request->id_asignatura);
        $asignatura->estado = $asignatura->estado == '1' ? '0' : '1';
        $asignatura->fecha_eliminacion = $asignatura->estado == '0' ? Carbon::now() : null;
        $asignatura->eliminado_por = $asignatura->estado == '0' ? auth()->id() : null;
        $asignatura->ip = $request->ip();
        $asignatura->dispositivo = $request->userAgent();
        $asignatura->save();

        return response()->json([
            'success' => true,
            'message' => $asignatura->estado == '1' ? 'La asignatura fue restaurada con éxito.' : 'La asignatura fue archivada con éxito.',
            'asignatura' => $asignatura,
        ]);
    }

    public function sync_horarios(Request $request, int $asignatura)
    {
        $request->validate([
            'agregar' => ['sometimes', 'array'],
            'agregar.*.id_horario_asignatura' => ['required_with:agregar', 'integer', 'exists:horarios_asignaturas,id_horario_asignatura'],
            'agregar.*.dia_semana' => ['required_with:agregar', 'integer', 'between:1,6'],

            'eliminar' => ['sometimes', 'array'],
            'eliminar.*.id_horario_asignatura' => ['required_with:eliminar', 'integer', 'exists:horarios_asignaturas,id_horario_asignatura'],
            'eliminar.*.dia_semana' => ['required_with:eliminar', 'integer', 'between:1,6'],
        ]);

        $asignatura = (new Asignatura())->get_asignatura($asignatura);

        $agregar = $request->input('agregar', []);
        $eliminar = $request->input('eliminar', []);

        DB::beginTransaction();
        try {
            foreach ($eliminar as $item) {
                $asignatura->horarios_asignaturas()->wherePivot('dia_semana', $item['dia_semana'])
                    ->detach($item['id_horario_asignatura']);
            }

            foreach ($agregar as $item) {
                // Evitar duplicados: solo adjuntar si la combinación no existe aún
                $existe = $asignatura->horarios_asignaturas()
                    ->wherePivot('dia_semana', $item['dia_semana'])
                    ->where('horarios_asignaturas.id_horario_asignatura', $item['id_horario_asignatura'])
                    ->exists();

                if (! $existe) {
                    $asignatura->horarios_asignaturas()->attach($item['id_horario_asignatura'], [
                        'dia_semana' => $item['dia_semana'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Horarios actualizados correctamente.',
                'asignatura' => $asignatura->load('horarios_asignaturas'),
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
