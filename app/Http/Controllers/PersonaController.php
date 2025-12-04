<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use Illuminate\Http\Request;

class PersonaController extends Controller
{
    public function listar()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso',], 403);
        }

        $personas = (new Persona())->get_all_personas();
        return response()->json([
            'data' => $personas
        ]);
    }

    public function mostrar(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN', 'BIBLIOTECA'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $persona = (new Persona())->get_persona($request->persona);
        return response()->json([
            'data' => $persona
        ]);
    }
}
