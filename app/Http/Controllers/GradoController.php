<?php

namespace App\Http\Controllers;

use App\Http\Requests\GradoValidation;
use App\Models\Grado;
use App\Models\Nivel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GradoController extends Controller
{
    public function view_index()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return redirect()->route('main.index');
        }
        
        $niveles = (new Nivel())->get_all_niveles();

        return view('grados.index', [
            'head_title' => 'GESTIÓN DE GRADOS',
            'niveles' => $niveles
        ]);
    }

    public function listar()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso',], 403);
        }

        $grados = (new Grado())->get_all_grados();
        return response()->json([
            'data' => $grados
        ]);
    }

    public function mostrar(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $grado = (new Grado())->get_grado($request->grado);
        return response()->json([
            'data' => $grado
        ]);
    }

    public function create(GradoValidation $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $grado = new Grado();
        $grado->grado = $request->grado;
        $grado->posicion_ordinal = $request->posicion_ordinal;
        $grado->id_nivel = $request->id_nivel;
        $grado->creado_por = session('id_usuario');
        $grado->ip = session('ip');
        $grado->dispositivo = session('dispositivo');
        $grado->save();

        return response()->json([
            'success' => true,
            'message' => 'Grado creado correctamente',
            'grado' => $grado
        ]);
    }

    public function update(GradoValidation $request, $id_grado)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $grado = (new Grado())->get_grado($id_grado);
        $grado->grado = $request->grado;
        $grado->posicion_ordinal = $request->posicion_ordinal;
        $grado->id_nivel = $request->id_nivel;
        $grado->modificado_por = session('id_usuario');
        $grado->ip = session('ip');
        $grado->dispositivo = session('dispositivo');
        $grado->save();

        return response()->json([
            'success' => true,
            'message' => 'Grado actualizado correctamente',
            'grado' => $grado
        ]);
    }

    public function delete(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'id_grado' => ['required', 'numeric', 'integer']
        ]);

        $grado = (new Grado())->get_grado($request->id_grado);
        $grado->estado = $grado->estado == '1' ? '0' : '1';
        $grado->fecha_eliminacion = $grado->estado == '0' ? Carbon::now() : null;
        $grado->eliminado_por = $grado->estado == '0' ? session('id_usuario') : null;
        $grado->ip = session('ip');
        $grado->dispositivo = session('dispositivo');
        $grado->save();

        return response()->json([
            'success' => true,
            'message' => $grado->estado == '1' ? 'El grado fue restaurado con éxito.' : 'El grado fue archivado con éxito.',
            'grado' => $grado
        ]);
    }
}
