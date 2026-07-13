<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    public function listar()
    {
        $personas = (new Persona())->get_all_personas();
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
