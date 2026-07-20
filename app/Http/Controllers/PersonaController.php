<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    public function listar()
    {
        $personas = (new Persona())->get_personal();
        return response()->json([
            'data' => $personas
        ]);
    }

    public function listar_biblioteca()
    {
        $personas = (new Persona())->get_all_personas_biblioteca();
        return response()->json([
            'data' => $personas
        ]);
    }

    public function mostrar(Request $request)
    {
        $persona = (new Persona())->get_persona($request->persona);
        return response()->json([
            'data' => $persona
        ]);
    }
}
