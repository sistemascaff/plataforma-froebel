<?php

namespace App\Http\Controllers;

use App\Http\Requests\MateriaValidation;
use App\Models\Materia;
use App\Models\Campo;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    public function view_index()
    {
        $campos = (new Campo())->get_all_campos();

        return view('materias.index', [
            'head_title' => 'GESTIÓN DE MATERIAS',
            'campos' => $campos
        ]);
    }

    public function view_details($materia)
    {
        $materia = (new Materia())->get_materia($materia);

        return view('materias.details', [
            'head_title' => 'MATERIA: ' . $materia->materia,
            'materia' => $materia,
        ]);
    }

    public function listar()
    {
        $materias = (new Materia())->get_all_materias();
        return response()->json([
            'data' => $materias
        ]);
    }

    public function mostrar(Request $request)
    {
        $materia = (new Materia())->get_materia($request->materia);
        return response()->json([
            'data' => $materia
        ]);
    }

    public function create(MateriaValidation $request)
    {
        $materia = new Materia();
        $materia->materia = $request->materia;
        $materia->abreviatura = $request->abreviatura;
        $materia->posicion_ordinal = $request->posicion_ordinal;
        $materia->id_campo = $request->id_campo;
        $materia->creado_por = auth()->id();
        $materia->ip = $request->ip();
        $materia->dispositivo = $request->userAgent();
        $materia->save();

        return response()->json([
            'success' => true,
            'message' => 'Materia creada correctamente',
            'materia' => $materia
        ]);
    }

    public function update(MateriaValidation $request, $id_materia)
    {
        $materia = (new Materia())->get_materia($id_materia);
        $materia->materia = $request->materia;
        $materia->abreviatura = $request->abreviatura;
        $materia->posicion_ordinal = $request->posicion_ordinal;
        $materia->id_campo = $request->id_campo;
        $materia->modificado_por = auth()->id();
        $materia->ip = $request->ip();
        $materia->dispositivo = $request->userAgent();
        $materia->save();

        return response()->json([
            'success' => true,
            'message' => 'Materia actualizada correctamente',
            'materia' => $materia
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id_materia' => ['required', 'numeric', 'integer', 'exists:materias,id_materia'],
        ]);

        $materia = (new Materia())->get_materia($request->id_materia);
        $materia->estado = $materia->estado == '1' ? '0' : '1';
        $materia->fecha_eliminacion = $materia->estado == '0' ? Carbon::now() : null;
        $materia->eliminado_por = $materia->estado == '0' ? auth()->id() : null;
        $materia->ip = $request->ip();
        $materia->dispositivo = $request->userAgent();
        $materia->save();

        return response()->json([
            'success' => true,
            'message' => $materia->estado == '1' ? 'La materia fue restaurada con éxito.' : 'La materia fue archivada con éxito.',
            'materia' => $materia
        ]);
    }
}
