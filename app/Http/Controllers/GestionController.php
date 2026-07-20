<?php

namespace App\Http\Controllers;

use App\Http\Requests\GestionValidation;
use App\Models\Gestion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GestionController extends Controller
{
    public function view_index()
    {
        return view('gestiones.index', [
            'head_title' => 'ADMINISTRACIÓN DE GESTIONES',
        ]);
    }

    public function view_details($gestion)
    {
        $gestion = (new Gestion())->get_gestion($gestion);

        return view('gestiones.details', [
            'head_title' => 'GESTIÓN: ' . $gestion->anio,
            'gestion' => $gestion,
        ]);
    }

    public function listar()
    {
        $gestiones = (new Gestion())->get_all_gestiones();
        return response()->json([
            'data' => $gestiones
        ]);
    }

    public function mostrar(Request $request)
    {
        $gestion = (new Gestion())->get_gestion($request->gestion);
        return response()->json([
            'data' => $gestion
        ]);
    }

    public function create(GestionValidation $request)
    {
        $gestion = new Gestion();
        $gestion->anio = $request->anio;
        $gestion->creado_por = session('id_usuario');
        $gestion->ip = $request->ip();
        $gestion->dispositivo = $request->userAgent();
        $gestion->save();

        return response()->json([
            'success' => true,
            'message' => 'Gestión creada correctamente',
            'gestion' => $gestion
        ]);
    }

    public function update(GestionValidation $request, $id_gestion)
    {
        $gestion = (new Gestion())->get_gestion($id_gestion);
        $gestion->anio = $request->anio;
        $gestion->modificado_por = session('id_usuario');
        $gestion->ip = $request->ip();
        $gestion->dispositivo = $request->userAgent();
        $gestion->save();

        return response()->json([
            'success' => true,
            'message' => 'Gestión actualizada correctamente',
            'gestion' => $gestion
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id_gestion' => ['required', 'numeric', 'integer', 'exists:gestiones,id_gestion'],
        ]);

        $gestion = (new Gestion())->get_gestion($request->id_gestion);

        $nuevoEstado = $gestion->estado == '1' ? '0' : '1';

        $gestion->estado = $nuevoEstado;
        $gestion->fecha_eliminacion = $nuevoEstado == '0' ? Carbon::now() : null;
        $gestion->eliminado_por = $nuevoEstado == '0' ? session('id_usuario') : null;
        $gestion->ip = $request->ip();
        $gestion->dispositivo = $request->userAgent();
        $gestion->save();

        return response()->json([
            'success' => true,
            'message' => $nuevoEstado == '1' ? 'La gestión fue restaurada con éxito.' : 'La gestión fue archivada con éxito.',
            'gestion' => $gestion
        ]);
    }
}
