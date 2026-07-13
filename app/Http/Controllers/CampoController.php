<?php

namespace App\Http\Controllers;

use App\Http\Requests\CampoValidation;
use App\Models\Campo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CampoController extends Controller
{
    public function view_index()
    {
        return view('campos.index', [
            'head_title' => 'GESTIÓN DE CAMPOS',
        ]);
    }

    public function view_details($campo)
    {
        $campo = (new Campo())->get_campo($campo);

        return view('campos.details', [
            'head_title' => 'CAMPO: ' . $campo->campo,
            'campo' => $campo,
        ]);
    }

    public function listar()
    {
        $campos = (new Campo())->get_all_campos();
        return response()->json([
            'data' => $campos
        ]);
    }

    public function mostrar(Request $request)
    {
        $campo = (new Campo())->get_campo($request->campo);
        return response()->json([
            'data' => $campo
        ]);
    }

    public function create(CampoValidation $request)
    {

        $campo = new Campo();
        $campo->campo = $request->campo;
        $campo->posicion_ordinal = $request->posicion_ordinal;
        $campo->creado_por = session('id_usuario');
        $campo->ip = session('ip');
        $campo->dispositivo = session('dispositivo');
        $campo->save();

        return response()->json([
            'success' => true,
            'message' => 'Campo creado correctamente',
            'campo' => $campo
        ]);
    }

    public function update(CampoValidation $request, $id_campo)
    {
        $campo = (new Campo())->get_campo($id_campo);
        $campo->campo = $request->campo;
        $campo->posicion_ordinal = $request->posicion_ordinal;
        $campo->modificado_por = session('id_usuario');
        $campo->ip = session('ip');
        $campo->dispositivo = session('dispositivo');
        $campo->save();

        return response()->json([
            'success' => true,
            'message' => 'Campo actualizado correctamente',
            'campo' => $campo
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id_campo' => ['required', 'numeric', 'integer']
        ]);

        $campo = (new Campo())->get_campo($request->id_campo);
        $campo->estado = $campo->estado == '1' ? '0' : '1';
        $campo->fecha_eliminacion = $campo->estado == '0' ? Carbon::now() : null;
        $campo->eliminado_por = $campo->estado == '0' ? session('id_usuario') : null;
        $campo->ip = session('ip');
        $campo->dispositivo = session('dispositivo');
        $campo->save();

        return response()->json([
            'success' => true,
            'message' => $campo->estado == '1' ? 'El campo fue restaurado con éxito.' : 'El campo fue archivado con éxito.',
            'campo' => $campo
        ]);
    }
}
