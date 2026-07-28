<?php

namespace App\Http\Controllers;

use App\Http\Requests\CursoValidation;
use App\Models\Curso;
use App\Models\Grado;
use App\Models\Paralelo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    public function view_index()
    {
        $grados = (new Grado())->get_all_grados();
        $paralelos = (new Paralelo())->get_all_paralelos();

        return view('cursos.index', [
            'head_title' => 'GESTIÓN DE CURSOS',
            'grados' => $grados,
            'paralelos' => $paralelos,
        ]);
    }

    public function view_details(int $curso)
    {
        $curso = (new Curso())->get_curso($curso);

        return view('cursos.details', [
            'head_title' => 'CURSO: '.$curso->curso,
            'curso' => $curso,
        ]);
    }

    public function listar()
    {
        $cursos = (new Curso())->get_all_cursos();

        return response()->json([
            'data' => $cursos,
        ]);
    }

    public function mostrar(Request $request)
    {
        $curso = (new Curso())->get_curso($request->curso);

        return response()->json([
            'data' => $curso,
        ]);
    }

    public function create(CursoValidation $request)
    {
        $curso = new Curso();
        $curso->curso = $request->curso;
        $curso->id_grado = $request->id_grado;
        $curso->id_paralelo = $request->id_paralelo;
        $curso->creado_por = auth()->id();
        $curso->ip = $request->ip();
        $curso->dispositivo = $request->userAgent();
        $curso->save();

        return response()->json([
            'success' => true,
            'message' => 'Curso creado correctamente',
            'curso' => $curso,
        ]);
    }

    public function update(CursoValidation $request, int $id_curso)
    {
        $curso = (new Curso())->get_curso($id_curso);
        $curso->curso = $request->curso;
        $curso->id_grado = $request->id_grado;
        $curso->id_paralelo = $request->id_paralelo;
        $curso->modificado_por = auth()->id();
        $curso->ip = $request->ip();
        $curso->dispositivo = $request->userAgent();
        $curso->save();

        return response()->json([
            'success' => true,
            'message' => 'Curso actualizado correctamente',
            'curso' => $curso,
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id_curso' => ['required', 'numeric', 'integer', 'exists:cursos,id_curso'],
        ]);

        $curso = (new Curso())->get_curso($request->id_curso);
        $curso->estado = $curso->estado == '1' ? '0' : '1';
        $curso->fecha_eliminacion = $curso->estado == '0' ? Carbon::now() : null;
        $curso->eliminado_por = $curso->estado == '0' ? auth()->id() : null;
        $curso->ip = $request->ip();
        $curso->dispositivo = $request->userAgent();
        $curso->save();

        return response()->json([
            'success' => true,
            'message' => $curso->estado == '1' ? 'El curso fue restaurado con éxito.' : 'El curso fue archivado con éxito.',
            'curso' => $curso,
        ]);
    }
}
