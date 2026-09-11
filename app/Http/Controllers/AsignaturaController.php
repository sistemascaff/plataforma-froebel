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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AsignaturaController extends Controller
{
    public function view_index()
    {
        // Autorización para ver el catálogo general
        $this->authorize('viewAny', Asignatura::class);

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
        // 1. Obtenemos el registro ANTES de hacer operaciones para poder autorizarlo
        $asignaturaModel = (new Asignatura())->get_asignatura($asignatura);

        // 2. Ejecutamos la política
        $this->authorize('view', $asignaturaModel);

        $tipo_perfil = Auth::user()->persona?->tipo_perfil;

        if (in_array($tipo_perfil, ['ADMIN', 'DIRECTOR', 'COORDINADOR', 'SUBDIRECTOR'])) {
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
        }

        // Recargamos el modelo para incluir las listas recién creadas
        $asignaturaModel = (new Asignatura())->get_asignatura($asignatura);

        return view('asignaturas.details', [
            'head_title' => 'ASIGNATURA: ' . $asignaturaModel->asignatura,
            'asignatura' => $asignaturaModel,
        ]);
    }

    public function listar()
    {
        // Autorización general
        $this->authorize('viewAny', Asignatura::class);

        $tipo_perfil = Auth::user()->persona?->tipo_perfil;
        $filtros = [];
        $asignaturas = null;

        if ($tipo_perfil === 'SUBDIRECTOR') {
            $filtros['nivel'] = Auth::user()->persona?->docente?->id_nivel;
            $asignaturas = (new Asignatura())->get_asignaturas($filtros);
        } elseif ($tipo_perfil === 'COORDINADOR') {
            $filtros['coordinacion'] = Auth::user()->persona?->docente?->id_coordinacion;
            $asignaturas = (new Asignatura())->get_asignaturas($filtros);
        } else {
            $asignaturas = (new Asignatura())->get_all_asignaturas();
        }

        return response()->json([
            'data' => $asignaturas,
        ]);
    }

    public function mostrar(Request $request)
    {
        $asignaturaModel = (new Asignatura())->get_asignatura($request->asignatura);

        // Autorización específica del registro
        $this->authorize('view', $asignaturaModel);

        return response()->json([
            'data' => $asignaturaModel,
        ]);
    }

    public function create(AsignaturaValidation $request)
    {
        // Autorización para crear
        $this->authorize('create', Asignatura::class);

        $asignatura = new Asignatura();
        $asignatura->asignatura = $request->asignatura;
        $asignatura->tipo_calificacion = $request->tipo_calificacion;
        $asignatura->tipo_bloque = $request->tipo_bloque;
        $asignatura->id_materia = $request->id_materia;
        $asignatura->id_area = $request->id_area;
        $asignatura->id_aula = $request->id_aula;

        // Asignaciones base según el Request
        $asignatura->id_nivel = $request->id_nivel;
        $asignatura->id_coordinacion = $request->id_coordinacion;

        // ==========================================
        // SOBREESCRITURA CERO-CONFIANZA
        // ==========================================
        $tipo_perfil = auth()->user()->persona->tipo_perfil;
        if ($tipo_perfil === 'SUBDIRECTOR') {
            $asignatura->id_nivel = auth()->user()->persona->docente->id_nivel;
        } elseif ($tipo_perfil === 'COORDINADOR') {
            $asignatura->id_coordinacion = auth()->user()->persona->docente->id_coordinacion;
        }

        // si el tipo de bloque es curso, asignamos el id_curso; de lo contrario ('mixto') se deja en null
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

        // Autorización para actualizar este registro específico
        $this->authorize('update', $asignatura);

        $asignatura->asignatura = $request->asignatura;
        $asignatura->tipo_calificacion = $request->tipo_calificacion;
        $asignatura->tipo_bloque = $request->tipo_bloque;
        $asignatura->id_materia = $request->id_materia;
        $asignatura->id_area = $request->id_area;
        $asignatura->id_aula = $request->id_aula;

        // Asignaciones base según el Request
        $asignatura->id_nivel = $request->id_nivel;
        $asignatura->id_coordinacion = $request->id_coordinacion;

        // ==========================================
        // SOBREESCRITURA CERO-CONFIANZA
        // ==========================================
        $tipo_perfil = auth()->user()->persona->tipo_perfil;
        if ($tipo_perfil === 'SUBDIRECTOR') {
            $asignatura->id_nivel = auth()->user()->persona->docente->id_nivel;
        } elseif ($tipo_perfil === 'COORDINADOR') {
            $asignatura->id_coordinacion = auth()->user()->persona->docente->id_coordinacion;
        }

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

        // Autorización para eliminar (soft delete)
        $this->authorize('delete', $asignatura);

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

        $asignaturaModel = (new Asignatura())->get_asignatura($asignatura);

        // Modificar horarios también es una forma de edición, aplicamos la política update
        $this->authorize('update', $asignaturaModel);

        $agregar = $request->input('agregar', []);
        $eliminar = $request->input('eliminar', []);

        DB::beginTransaction();
        try {
            foreach ($eliminar as $item) {
                $asignaturaModel->horarios_asignaturas()->wherePivot('dia_semana', $item['dia_semana'])
                    ->detach($item['id_horario_asignatura']);
            }

            foreach ($agregar as $item) {
                $existe = $asignaturaModel->horarios_asignaturas()
                    ->wherePivot('dia_semana', $item['dia_semana'])
                    ->where('horarios_asignaturas.id_horario_asignatura', $item['id_horario_asignatura'])
                    ->exists();

                if (! $existe) {
                    $asignaturaModel->horarios_asignaturas()->attach($item['id_horario_asignatura'], [
                        'dia_semana' => $item['dia_semana'],
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Horarios actualizados correctamente.',
                'asignatura' => $asignaturaModel->load('horarios_asignaturas'),
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
