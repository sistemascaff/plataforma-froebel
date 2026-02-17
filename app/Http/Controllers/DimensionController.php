<?php

namespace App\Http\Controllers;

use App\Http\Requests\DimensionValidation;
use App\Models\Dimension;
use App\Models\Gestion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DimensionController extends Controller
{
    public function view_index()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return redirect()->route('main.index');
        }
        
        $gestiones = (new Gestion())->get_all_gestiones();

        return view('dimensiones.index', [
            'head_title' => 'GESTIÓN DE DIMENSIONES',
            'gestiones' => $gestiones
        ]);
    }

    public function listar()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso',], 403);
        }

        $dimensiones = (new Dimension())->get_all_dimensiones();
        return response()->json([
            'data' => $dimensiones
        ]);
    }

    public function mostrar(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $dimension = (new Dimension())->get_dimension($request->dimension);
        return response()->json([
            'data' => $dimension
        ]);
    }

    public function create(DimensionValidation $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $dimension = new Dimension();
        $dimension->dimension = $request->dimension;
        $dimension->posicion_ordinal = $request->posicion_ordinal;
        $dimension->puntaje_maximo = $request->puntaje_maximo;
        $dimension->tipo_calculo = $request->tipo_calculo;
        $dimension->id_gestion = $request->id_gestion;
        $dimension->creado_por = session('id_usuario');
        $dimension->ip = session('ip');
        $dimension->dispositivo = session('dispositivo');
        $dimension->save();

        return response()->json([
            'success' => true,
            'message' => 'Dimensión creada correctamente',
            'dimension' => $dimension
        ]);
    }

    public function update(DimensionValidation $request, $id_dimension)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $dimension = (new Dimension())->get_dimension($id_dimension);
        $dimension->dimension = $request->dimension;
        $dimension->posicion_ordinal = $request->posicion_ordinal;
        $dimension->puntaje_maximo = $request->puntaje_maximo;
        $dimension->tipo_calculo = $request->tipo_calculo;
        $dimension->id_gestion = $request->id_gestion;
        $dimension->modificado_por = session('id_usuario');
        $dimension->ip = session('ip');
        $dimension->dispositivo = session('dispositivo');
        $dimension->save();

        return response()->json([
            'success' => true,
            'message' => 'Dimension actualizada correctamente',
            'dimension' => $dimension
        ]);
    }

    public function delete(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'id_dimension' => ['required', 'numeric', 'integer']
        ]);

        $dimension = (new Dimension())->get_dimension($request->id_dimension);
        $dimension->estado = $dimension->estado == '1' ? '0' : '1';
        $dimension->fecha_eliminacion = $dimension->estado == '0' ? Carbon::now() : null;
        $dimension->eliminado_por = $dimension->estado == '0' ? session('id_usuario') : null;
        $dimension->ip = session('ip');
        $dimension->dispositivo = session('dispositivo');
        $dimension->save();

        return response()->json([
            'success' => true,
            'message' => $dimension->estado == '1' ? 'La dimensión fue restaurada con éxito.' : 'La dimensión fue archivada con éxito.',
            'dimension' => $dimension
        ]);
    }
}
