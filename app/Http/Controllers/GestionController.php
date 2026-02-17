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
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return redirect()->route('main.index');
        }

        return view('gestiones.index', [
            'head_title' => 'ADMINISTRACIÓN DE GESTIONES',
        ]);
    }

    public function view_details($gestion)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return redirect()->route('login');
        }

        $gestion = (new Gestion())->get_gestion($gestion);

        return view('gestiones.details', [
            'head_title' => 'GESTIÓN: ' . $gestion->anio,
            'gestion' => $gestion,
        ]);
    }

    public function listar()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso',], 403);
        }

        $gestiones = (new Gestion())->get_all_gestiones();
        return response()->json([
            'data' => $gestiones
        ]);
    }

    public function mostrar(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $gestion = (new Gestion())->get_gestion($request->gestion);
        return response()->json([
            'data' => $gestion
        ]);
    }

    public function create(GestionValidation $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $gestion = new Gestion();
        $gestion->anio = $request->anio;
        $gestion->creado_por = session('id_usuario');
        $gestion->ip = session('ip');
        $gestion->dispositivo = session('dispositivo');
        $gestion->save();

        return response()->json([
            'success' => true,
            'message' => 'Gestión creada correctamente',
            'gestion' => $gestion
        ]);
    }

    public function update(GestionValidation $request, $id_gestion)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $gestion = (new Gestion())->get_gestion($id_gestion);
        $gestion->anio = $request->anio;
        $gestion->modificado_por = session('id_usuario');
        $gestion->ip = session('ip');
        $gestion->dispositivo = session('dispositivo');
        $gestion->save();

        return response()->json([
            'success' => true,
            'message' => 'Gestión actualizada correctamente',
            'gestion' => $gestion
        ]);
    }

    public function delete(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'id_gestion' => ['required', 'numeric', 'integer']
        ]);

        $gestion = (new Gestion())->get_gestion($request->id_gestion);
        $gestion->estado = $gestion->estado == '1' ? '0' : '1';
        $gestion->fecha_eliminacion = $gestion->estado == '0' ? Carbon::now() : null;
        $gestion->eliminado_por = $gestion->estado == '0' ? session('id_usuario') : null;
        $gestion->ip = session('ip');
        $gestion->dispositivo = session('dispositivo');
        $gestion->save();

        return response()->json([
            'success' => true,
            'message' => $gestion->estado == '1' ? 'La gestión fue restaurada con éxito.' : 'La gestión fue archivada con éxito.',
            'gestion' => $gestion
        ]);
    }
}
