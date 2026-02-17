<?php

namespace App\Http\Controllers;

use App\Http\Requests\AulaValidation;
use App\Models\Aula;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AulaController extends Controller
{
    public function view_index()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return redirect()->route('main.index');
        }

        return view('aulas.index', [
            'head_title' => 'GESTIÓN DE AULAS',
        ]);
    }

    public function listar()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso',], 403);
        }

        $aulas = (new Aula())->get_all_aulas();
        return response()->json([
            'data' => $aulas
        ]);
    }

    public function mostrar(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $aula = (new Aula())->get_aula($request->aula);
        return response()->json([
            'data' => $aula
        ]);
    }

    public function create(AulaValidation $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $aula = new Aula();
        $aula->aula = $request->aula;
        $aula->creado_por = session('id_usuario');
        $aula->ip = session('ip');
        $aula->dispositivo = session('dispositivo');
        $aula->save();

        return response()->json([
            'success' => true,
            'message' => 'Gestión creada correctamente',
            'aula' => $aula
        ]);
    }

    public function update(AulaValidation $request, $id_aula)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $aula = (new Aula())->get_aula($id_aula);
        $aula->aula = $request->aula;
        $aula->modificado_por = session('id_usuario');
        $aula->ip = session('ip');
        $aula->dispositivo = session('dispositivo');
        $aula->save();

        return response()->json([
            'success' => true,
            'message' => 'Gestión actualizada correctamente',
            'aula' => $aula
        ]);
    }

    public function delete(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'id_aula' => ['required', 'numeric', 'integer']
        ]);

        $aula = (new Aula())->get_aula($request->id_aula);
        $aula->estado = $aula->estado == '1' ? '0' : '1';
        $aula->fecha_eliminacion = $aula->estado == '0' ? Carbon::now() : null;
        $aula->eliminado_por = $aula->estado == '0' ? session('id_usuario') : null;
        $aula->ip = session('ip');
        $aula->dispositivo = session('dispositivo');
        $aula->save();

        return response()->json([
            'success' => true,
            'message' => $aula->estado == '1' ? 'La gestión fue restaurada con éxito.' : 'La gestión fue archivada con éxito.',
            'aula' => $aula
        ]);
    }
}
