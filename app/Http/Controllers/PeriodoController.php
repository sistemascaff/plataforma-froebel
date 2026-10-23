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
        $gestiones = (new Gestion())->get_all_gestiones();

        return view('periodos.index', [
            'head_title' => 'GESTIÓN DE PERIODOS',
            'gestiones' => $gestiones
        ]);
    }

    public function listar()
    {
        $periodos = (new Periodo())->get_all_periodos();
        return response()->json([
            'data' => $periodos
        ]);
    }

    public function mostrar(Request $request)
    {
        $periodo = (new Periodo())->get_periodo($request->periodo);
        return response()->json([
            'data' => $periodo
        ]);
    }

    public function create(PeriodoValidation $request)
    {
        $gestion = (new Gestion())->get_gestion($request->id_gestion);

        // Si la cantidad de periodos de la gestión es mayor o igual al límite, cancela la operación.
        if (count($gestion->periodos) >= $gestion->limite_periodos) {
            return response()->json([
                'success' => false,
                'message' => "Ya se ha creado la cantidad máxima de periodos (<b>{$gestion->limite_periodos}</b>) de la Gestión <b class=\"text-primary\">{$gestion->anio}</b>."
            ], 422);
        }

        $periodo = new Periodo();
        $periodo->periodo = $request->periodo;
        $periodo->posicion_ordinal = $request->posicion_ordinal;
        $periodo->fecha_inicio = $request->fecha_inicio;
        $periodo->fecha_fin = $request->fecha_fin;
        $periodo->id_gestion = $request->id_gestion;
        $periodo->creado_por = auth()->id();
        $periodo->ip = $request->ip();
        $periodo->dispositivo = $request->userAgent();
        $periodo->save();

        return response()->json([
            'success' => true,
            'message' => 'Periodo creado correctamente',
            'periodo' => $periodo
        ]);
    }

    public function update(PeriodoValidation $request, $id_periodo)
    {
        $periodo = (new Periodo())->get_periodo($id_periodo);
        $periodo->periodo = $request->periodo;
        $periodo->posicion_ordinal = $request->posicion_ordinal;
        $periodo->fecha_inicio = $request->fecha_inicio;
        $periodo->fecha_fin = $request->fecha_fin;
        // No se modifica la gestión del periodo
        $periodo->modificado_por = auth()->id();
        $periodo->ip = $request->ip();
        $periodo->dispositivo = $request->userAgent();
        $periodo->save();

        return response()->json([
            'success' => true,
            'message' => 'Periodo actualizado correctamente',
            'periodo' => $periodo
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id_periodo' => ['required', 'numeric', 'integer', 'exists:periodos,id_periodo'],
        ]);

        $periodo = (new Periodo())->get_periodo($request->id_periodo);
        $periodo->estado = $periodo->estado == '1' ? '0' : '1';
        $periodo->fecha_eliminacion = $periodo->estado == '0' ? Carbon::now() : null;
        $periodo->eliminado_por = $periodo->estado == '0' ? auth()->id() : null;
        $periodo->ip = $request->ip();
        $periodo->dispositivo = $request->userAgent();
        $periodo->save();

        return response()->json([
            'success' => true,
            'message' => $periodo->estado == '1' ? 'El periodo fue restaurado con éxito.' : 'El periodo fue archivado con éxito.',
            'periodo' => $periodo
        ]);
    }
}
