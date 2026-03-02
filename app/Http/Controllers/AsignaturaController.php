<?php

namespace App\Http\Controllers;

use App\Http\Requests\AsignaturaValidation;
use App\Models\Area;
use App\Models\Asignatura;
use App\Models\Aula;
use App\Models\Coordinacion;
use App\Models\Materia;
use App\Models\Nivel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AsignaturaController extends Controller
{
    public function view_index()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return redirect()->route('main.index');
        }
        
        $materias = (new Materia())->get_all_materias();
        $areas = (new Area())->get_all_areas();
        $aulas = (new Aula())->get_all_aulas();
        $niveles = (new Nivel())->get_all_niveles();
        $coordinaciones = (new Coordinacion())->get_all_coordinaciones();

        return view('asignaturas.index', [
            'head_title' => 'GESTIÓN DE ASIGNATURAS',
            'materias' => $materias,
            'areas' => $areas,
            'aulas' => $aulas,
            'niveles' => $niveles,
            'coordinaciones' => $coordinaciones
        ]);
    }

    public function view_details($asignatura)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return redirect()->route('login');
        }

        $asignatura = (new Asignatura())->get_asignatura($asignatura);

        return view('asignaturas.details', [
            'head_title' => 'ASIGNATURA: ' . $asignatura->asignatura,
            'asignatura' => $asignatura,
        ]);
    }

    public function listar()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso',], 403);
        }

        $asignaturas = (new Asignatura())->get_all_asignaturas();
        return response()->json([
            'data' => $asignaturas
        ]);
    }

    public function mostrar(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $asignatura = (new Asignatura())->get_asignatura($request->asignatura);
        return response()->json([
            'data' => $asignatura
        ]);
    }

    public function create(AsignaturaValidation $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $asignatura = new Asignatura();
        $asignatura->asignatura = $request->asignatura;
        $asignatura->tipo_calificacion = $request->tipo_calificacion;
        $asignatura->tipo_bloque = $request->tipo_bloque;
        $asignatura->id_materia = $request->id_materia;
        $asignatura->id_area = $request->id_area;
        $asignatura->id_aula = $request->id_aula;
        $asignatura->id_nivel = $request->id_nivel;
        $asignatura->id_coordinacion = $request->id_coordinacion;
        $asignatura->creado_por = session('id_usuario');
        $asignatura->ip = session('ip');
        $asignatura->dispositivo = session('dispositivo');
        $asignatura->save();

        return response()->json([
            'success' => true,
            'message' => 'Asignatura creada correctamente',
            'asignatura' => $asignatura
        ]);
    }

    public function update(AsignaturaValidation $request, $id_asignatura)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $asignatura = (new Asignatura())->get_asignatura($id_asignatura);
        $asignatura->asignatura = $request->asignatura;
        $asignatura->tipo_calificacion = $request->tipo_calificacion;
        $asignatura->tipo_bloque = $request->tipo_bloque;
        $asignatura->id_materia = $request->id_materia;
        $asignatura->id_area = $request->id_area;
        $asignatura->id_aula = $request->id_aula;
        $asignatura->id_nivel = $request->id_nivel;
        $asignatura->id_coordinacion = $request->id_coordinacion;
        $asignatura->modificado_por = session('id_usuario');
        $asignatura->ip = session('ip');
        $asignatura->dispositivo = session('dispositivo');
        $asignatura->save();

        return response()->json([
            'success' => true,
            'message' => 'Asignatura actualizada correctamente',
            'asignatura' => $asignatura
        ]);
    }

    public function delete(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'id_asignatura' => ['required', 'numeric', 'integer']
        ]);

        $asignatura = (new Asignatura())->get_asignatura($request->id_asignatura);
        $asignatura->estado = $asignatura->estado == '1' ? '0' : '1';
        $asignatura->fecha_eliminacion = $asignatura->estado == '0' ? Carbon::now() : null;
        $asignatura->eliminado_por = $asignatura->estado == '0' ? session('id_usuario') : null;
        $asignatura->ip = session('ip');
        $asignatura->dispositivo = session('dispositivo');
        $asignatura->save();

        return response()->json([
            'success' => true,
            'message' => $asignatura->estado == '1' ? 'La asignatura fue restaurada con éxito.' : 'La asignatura fue archivada con éxito.',
            'asignatura' => $asignatura
        ]);
    }
}
