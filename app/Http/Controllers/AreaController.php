<?php

namespace App\Http\Controllers;

use App\Http\Requests\AreaValidation;
use App\Models\Area;
use App\Models\Campo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function view_index()
    {
        $campos = (new Campo())->get_all_campos();

        return view('areas.index', [
            'head_title' => 'GESTIÓN DE ÁREAS',
            'campos' => $campos
        ]);
    }

    public function view_details($area)
    {
        $area = (new Area())->get_area($area);

        return view('areas.details', [
            'head_title' => 'ÁREA: ' . $area->area,
            'area' => $area,
        ]);
    }

    public function listar()
    {
        $areas = (new Area())->get_all_areas();
        return response()->json([
            'data' => $areas
        ]);
    }

    public function mostrar(Request $request)
    {
        $area = (new Area())->get_area($request->area);
        return response()->json([
            'data' => $area
        ]);
    }

    public function create(AreaValidation $request)
    {
        $area = new Area();
        $area->area = $request->area;
        $area->abreviatura = $request->abreviatura;
        $area->posicion_ordinal = $request->posicion_ordinal;
        $area->id_campo = $request->id_campo;
        $area->creado_por = auth()->id();
        $area->ip = $request->ip();
        $area->dispositivo = $request->userAgent();
        $area->save();

        return response()->json([
            'success' => true,
            'message' => 'Área creada correctamente',
            'area' => $area
        ]);
    }

    public function update(AreaValidation $request, $id_area)
    {
        $area = (new Area())->get_area($id_area);
        $area->area = $request->area;
        $area->abreviatura = $request->abreviatura;
        $area->posicion_ordinal = $request->posicion_ordinal;
        $area->id_campo = $request->id_campo;
        $area->modificado_por = auth()->id();
        $area->ip = $request->ip();
        $area->dispositivo = $request->userAgent();
        $area->save();

        return response()->json([
            'success' => true,
            'message' => 'Área actualizada correctamente',
            'area' => $area
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id_area' => ['required', 'numeric', 'integer', 'exists:areas,id_area'],
        ]);

        $area = (new Area())->get_area($request->id_area);
        $area->estado = $area->estado == '1' ? '0' : '1';
        $area->fecha_eliminacion = $area->estado == '0' ? Carbon::now() : null;
        $area->eliminado_por = $area->estado == '0' ? auth()->id() : null;
        $area->ip = $request->ip();
        $area->dispositivo = $request->userAgent();
        $area->save();

        return response()->json([
            'success' => true,
            'message' => $area->estado == '1' ? 'El área fue restaurada con éxito.' : 'El área fue archivada con éxito.',
            'area' => $area
        ]);
    }
}
