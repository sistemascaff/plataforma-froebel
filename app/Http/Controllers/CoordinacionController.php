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
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return redirect()->route('main.index');
        }

        return view('coordinaciones.index', [
            'head_title' => 'GESTIÓN DE COORDINACIONES',
        ]);
    }

    public function view_details($coordinacion)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return redirect()->route('login');
        }

        $coordinacion = (new Coordinacion())->get_coordinacion($coordinacion);

        return view('coordinaciones.details', [
            'head_title' => 'COORDINACIÓN: ' . $coordinacion->coordinacion,
            'coordinacion' => $coordinacion,
        ]);
    }

    public function listar()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso',], 403);
        }

        $coordinaciones = (new Coordinacion())->get_all_coordinaciones();
        return response()->json([
            'data' => $coordinaciones
        ]);
    }

    public function mostrar(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $coordinacion = (new Coordinacion())->get_coordinacion($request->coordinacion);
        return response()->json([
            'data' => $coordinacion
        ]);
    }

    public function create(CoordinacionValidation $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $coordinacion = new Coordinacion();
        $coordinacion->coordinacion = $request->coordinacion;
        $coordinacion->creado_por = session('id_usuario');
        $coordinacion->ip = session('ip');
        $coordinacion->dispositivo = session('dispositivo');
        $coordinacion->save();

        return response()->json([
            'success' => true,
            'message' => 'Coordinación creada correctamente',
            'coordinacion' => $coordinacion
        ]);
    }

    public function update(CoordinacionValidation $request, $id_coordinacion)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $coordinacion = (new Coordinacion())->get_coordinacion($id_coordinacion);
        $coordinacion->coordinacion = $request->coordinacion;
        $coordinacion->modificado_por = session('id_usuario');
        $coordinacion->ip = session('ip');
        $coordinacion->dispositivo = session('dispositivo');
        $coordinacion->save();

        return response()->json([
            'success' => true,
            'message' => 'Coordinación actualizada correctamente',
            'coordinacion' => $coordinacion
        ]);
    }

    public function delete(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'id_coordinacion' => ['required', 'numeric', 'integer']
        ]);

        $coordinacion = (new Coordinacion())->get_coordinacion($request->id_coordinacion);
        $coordinacion->estado = $coordinacion->estado == '1' ? '0' : '1';
        $coordinacion->fecha_eliminacion = $coordinacion->estado == '0' ? Carbon::now() : null;
        $coordinacion->eliminado_por = $coordinacion->estado == '0' ? session('id_usuario') : null;
        $coordinacion->ip = session('ip');
        $coordinacion->dispositivo = session('dispositivo');
        $coordinacion->save();

        return response()->json([
            'success' => true,
            'message' => $coordinacion->estado == '1' ? 'La coordinación fue restaurada con éxito.' : 'La coordinación fue archivada con éxito.',
            'coordinacion' => $coordinacion
        ]);
    }
}
