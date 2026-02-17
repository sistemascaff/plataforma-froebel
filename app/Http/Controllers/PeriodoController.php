<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeriodoValidation;
use App\Models\Periodo;
use App\Models\Gestion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PeriodoController extends Controller
{
    public function view_index()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return redirect()->route('main.index');
        }
        
        $gestiones = (new Gestion())->get_all_gestiones();

        return view('periodos.index', [
            'head_title' => 'GESTIÓN DE PERIODOS',
            'gestiones' => $gestiones
        ]);
    }

    public function listar()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso',], 403);
        }

        $periodos = (new Periodo())->get_all_periodos();
        return response()->json([
            'data' => $periodos
        ]);
    }

    public function mostrar(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $periodo = (new Periodo())->get_periodo($request->periodo);
        return response()->json([
            'data' => $periodo
        ]);
    }

    public function create(PeriodoValidation $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $periodo = new Periodo();
        $periodo->periodo = $request->periodo;
        $periodo->posicion_ordinal = $request->posicion_ordinal;
        $periodo->id_gestion = $request->id_gestion;
        $periodo->creado_por = session('id_usuario');
        $periodo->ip = session('ip');
        $periodo->dispositivo = session('dispositivo');
        $periodo->save();

        return response()->json([
            'success' => true,
            'message' => 'Periodo creado correctamente',
            'periodo' => $periodo
        ]);
    }

    public function update(PeriodoValidation $request, $id_periodo)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $periodo = (new Periodo())->get_periodo($id_periodo);
        $periodo->periodo = $request->periodo;
        $periodo->posicion_ordinal = $request->posicion_ordinal;
        $periodo->id_gestion = $request->id_gestion;
        $periodo->modificado_por = session('id_usuario');
        $periodo->ip = session('ip');
        $periodo->dispositivo = session('dispositivo');
        $periodo->save();

        return response()->json([
            'success' => true,
            'message' => 'Periodo actualizado correctamente',
            'periodo' => $periodo
        ]);
    }

    public function delete(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'id_periodo' => ['required', 'numeric', 'integer']
        ]);

        $periodo = (new Periodo())->get_periodo($request->id_periodo);
        $periodo->estado = $periodo->estado == '1' ? '0' : '1';
        $periodo->fecha_eliminacion = $periodo->estado == '0' ? Carbon::now() : null;
        $periodo->eliminado_por = $periodo->estado == '0' ? session('id_usuario') : null;
        $periodo->ip = session('ip');
        $periodo->dispositivo = session('dispositivo');
        $periodo->save();

        return response()->json([
            'success' => true,
            'message' => $periodo->estado == '1' ? 'El periodo fue restaurado con éxito.' : 'El periodo fue archivado con éxito.',
            'periodo' => $periodo
        ]);
    }
}
