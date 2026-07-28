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
        return view('aulas.index', [
            'head_title' => 'GESTIÓN DE AULAS',
        ]);
    }

    public function listar()
    {
        $aulas = (new Aula())->get_all_aulas();
        return response()->json([
            'data' => $aulas
        ]);
    }

    public function mostrar(Request $request)
    {
        $aula = (new Aula())->get_aula($request->aula);
        return response()->json([
            'data' => $aula
        ]);
    }

    public function create(AulaValidation $request)
    {
        $aula = new Aula();
        $aula->aula = $request->aula;
        $aula->creado_por = auth()->id();
        $aula->ip = $request->ip();
        $aula->dispositivo = $request->userAgent();
        $aula->save();

        return response()->json([
            'success' => true,
            'message' => 'Gestión creada correctamente',
            'aula' => $aula
        ]);
    }

    public function update(AulaValidation $request, $id_aula)
    {
        $aula = (new Aula())->get_aula($id_aula);
        $aula->aula = $request->aula;
        $aula->modificado_por = auth()->id();
        $aula->ip = $request->ip();
        $aula->dispositivo = $request->userAgent();
        $aula->save();

        return response()->json([
            'success' => true,
            'message' => 'Gestión actualizada correctamente',
            'aula' => $aula
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id_aula' => ['required', 'numeric', 'integer', 'exists:aulas,id_aula'],
        ]);

        $aula = (new Aula())->get_aula($request->id_aula);
        $aula->estado = $aula->estado == '1' ? '0' : '1';
        $aula->fecha_eliminacion = $aula->estado == '0' ? Carbon::now() : null;
        $aula->eliminado_por = $aula->estado == '0' ? auth()->id() : null;
        $aula->ip = $request->ip();
        $aula->dispositivo = $request->userAgent();
        $aula->save();

        return response()->json([
            'success' => true,
            'message' => $aula->estado == '1' ? 'La gestión fue restaurada con éxito.' : 'La gestión fue archivada con éxito.',
            'aula' => $aula
        ]);
    }
}
