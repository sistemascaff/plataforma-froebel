<?php

namespace App\Http\Controllers;

use App\Http\Requests\CoordinacionValidation;
use App\Models\Coordinacion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CoordinacionController extends Controller
{
    public function view_index()
    {
        return view('coordinaciones.index', [
            'head_title' => 'GESTIÓN DE COORDINACIONES',
        ]);
    }

    public function view_details($coordinacion)
    {
        $coordinacion = (new Coordinacion())->get_coordinacion($coordinacion);

        return view('coordinaciones.details', [
            'head_title' => 'COORDINACIÓN: ' . $coordinacion->coordinacion,
            'coordinacion' => $coordinacion,
        ]);
    }

    public function listar()
    {
        $coordinaciones = (new Coordinacion())->get_all_coordinaciones();
        return response()->json([
            'data' => $coordinaciones
        ]);
    }

    public function mostrar(Request $request)
    {
        $coordinacion = (new Coordinacion())->get_coordinacion($request->coordinacion);
        return response()->json([
            'data' => $coordinacion
        ]);
    }

    public function create(CoordinacionValidation $request)
    {
        $coordinacion = new Coordinacion();
        $coordinacion->coordinacion = $request->coordinacion;
        $coordinacion->creado_por = auth()->id();
        $coordinacion->ip = $request->ip();
        $coordinacion->dispositivo = $request->userAgent();
        $coordinacion->save();

        return response()->json([
            'success' => true,
            'message' => 'Coordinación creada correctamente',
            'coordinacion' => $coordinacion
        ]);
    }

    public function update(CoordinacionValidation $request, $id_coordinacion)
    {
        $coordinacion = (new Coordinacion())->get_coordinacion($id_coordinacion);
        $coordinacion->coordinacion = $request->coordinacion;
        $coordinacion->modificado_por = auth()->id();
        $coordinacion->ip = $request->ip();
        $coordinacion->dispositivo = $request->userAgent();
        $coordinacion->save();

        return response()->json([
            'success' => true,
            'message' => 'Coordinación actualizada correctamente',
            'coordinacion' => $coordinacion
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id_coordinacion' => ['required', 'numeric', 'integer', 'exists:coordinaciones,id_coordinacion'],
        ]);

        $coordinacion = (new Coordinacion())->get_coordinacion($request->id_coordinacion);
        $coordinacion->estado = $coordinacion->estado == '1' ? '0' : '1';
        $coordinacion->fecha_eliminacion = $coordinacion->estado == '0' ? Carbon::now() : null;
        $coordinacion->eliminado_por = $coordinacion->estado == '0' ? auth()->id() : null;
        $coordinacion->ip = $request->ip();
        $coordinacion->dispositivo = $request->userAgent();
        $coordinacion->save();

        return response()->json([
            'success' => true,
            'message' => $coordinacion->estado == '1' ? 'La coordinación fue restaurada con éxito.' : 'La coordinación fue archivada con éxito.',
            'coordinacion' => $coordinacion
        ]);
    }
}
