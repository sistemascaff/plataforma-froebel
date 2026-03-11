<?php

namespace App\Http\Controllers;

use App\Models\ListaAsignatura;
use Illuminate\Http\Request;

class ListaAsignaturaController extends Controller
{
    public function actualizar_docente(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'docente' => 'required|exists:docentes,id_docente',
        ]);

        $lista_asignatura = (new ListaAsignatura())->get_lista_asignatura($request->lista_asignatura);
        $lista_asignatura->id_docente = $request->docente;
        $lista_asignatura->modificado_por = session('id_usuario');
        $lista_asignatura->ip = session('ip');
        $lista_asignatura->dispositivo = session('dispositivo');
        $lista_asignatura->save();

        return response()->json([
            'success' => true,
            'message' => 'El/la docente de la lista seleccionada se ha actualizado correctamente.',
            'lista_asignatura' => $lista_asignatura]);
    }
}