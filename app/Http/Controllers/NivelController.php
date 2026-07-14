<?php

namespace App\Http\Controllers;

use App\Http\Requests\NivelValidation;
use App\Models\Nivel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NivelController extends Controller
{
    public function view_index()
    {
        return view('niveles.index', [
            'head_title' => 'GESTIÓN DE NIVELES',
        ]);
    }

    public function view_details($nivel)
    {
        $nivel = (new Nivel())->get_nivel($nivel);

        return view('niveles.details', [
            'head_title' => 'NIVEL: ' . $nivel->nivel,
            'nivel' => $nivel,
        ]);
    }

    public function listar()
    {

        $niveles = (new Nivel())->get_all_niveles();
        return response()->json([
            'data' => $niveles
        ]);
    }

    public function mostrar(Request $request)
    {
        $nivel = (new Nivel())->get_nivel($request->nivel);
        return response()->json([
            'data' => $nivel
        ]);
    }

    public function create(NivelValidation $request)
    {
        $nivel = new Nivel();
        $nivel->nivel = $request->nivel;
        $nivel->posicion_ordinal = $request->posicion_ordinal;
        $nivel->creado_por = session('id_usuario');
        $nivel->ip = $request->ip();
        $nivel->dispositivo = $request->userAgent();
        $nivel->save();

        return response()->json([
            'success' => true,
            'message' => 'Nivel creado correctamente',
            'nivel' => $nivel
        ]);
    }

    public function update(NivelValidation $request, $id_nivel)
    {
        $nivel = (new Nivel())->get_nivel($id_nivel);
        $nivel->nivel = $request->nivel;
        $nivel->posicion_ordinal = $request->posicion_ordinal;
        $nivel->modificado_por = session('id_usuario');
        $nivel->ip = $request->ip();
        $nivel->dispositivo = $request->userAgent();
        $nivel->save();

        return response()->json([
            'success' => true,
            'message' => 'Nivel actualizado correctamente',
            'nivel' => $nivel
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id_nivel' => ['required', 'numeric', 'integer', 'exists:niveles,id_nivel'],
        ]);

        $nivel = (new Nivel())->get_nivel($request->id_nivel);
        $nivel->estado = $nivel->estado == '1' ? '0' : '1';
        $nivel->fecha_eliminacion = $nivel->estado == '0' ? Carbon::now() : null;
        $nivel->eliminado_por = $nivel->estado == '0' ? session('id_usuario') : null;
        $nivel->ip = $request->ip();
        $nivel->dispositivo = $request->userAgent();
        $nivel->save();

        return response()->json([
            'success' => true,
            'message' => $nivel->estado == '1' ? 'El nivel fue restaurado con éxito.' : 'El nivel fue archivado con éxito.',
            'nivel' => $nivel
        ]);
    }
}
