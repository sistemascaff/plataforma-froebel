<?php

namespace App\Http\Controllers;

use App\Models\EstudianteLicencia;
use App\Http\Requests\EstudianteLicenciaValidation;
use Illuminate\Http\Request;

class EstudianteLicenciaController extends Controller
{
    public function view_index()
    {
        return view('estudiantes_licencias.index', [
            'head_title' => 'GESTIÓN DE LICENCIAS DE ESTUDIANTES',
        ]);
    }

    public function listar()
    {
        $licencias = (new EstudianteLicencia())->get_all_estudiantes_licencias();

        return response()->json(['data' => $licencias]);
    }

    public function mostrar(Request $request)
    {
        $estudiante = (new EstudianteLicencia())->get_estudiante_licencia($request->estudiante_licencia);
        return response()->json(['data' => $estudiante]);
    }

    public function create(EstudianteLicenciaValidation $request)
    {
        $licencia = new EstudianteLicencia();
        $licencia->id_estudiante = $request->id_estudiante;
        $licencia->tipo = $request->tipo;
        $licencia->justificacion = $request->justificacion;
        $licencia->fecha_inicio = $request->fecha_inicio;
        $licencia->fecha_fin = $request->fecha_fin;
        $licencia->evidencia = $request->evidencia;

        $licencia->creado_por = session('id_usuario');
        $licencia->ip = $request->ip();
        $licencia->dispositivo    = $request->userAgent();
        $licencia->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'La licencia fue registrada exitosamente.',
            'licencia' => $licencia
        ]);
    }

    public function update(EstudianteLicenciaValidation $request, int $id_estudiante_licencia)
    {
        $licencia = (new EstudianteLicencia())->get_estudiante_licencia($id_estudiante_licencia);
        $licencia->id_estudiante = $request->id_estudiante;
        $licencia->tipo = $request->tipo;
        $licencia->justificacion = $request->justificacion;
        $licencia->fecha_inicio  = $request->fecha_inicio;
        $licencia->fecha_fin     = $request->fecha_fin;
        $licencia->evidencia     = $request->evidencia;

        $licencia->modificado_por      = session('id_usuario');
        $licencia->ip                  = $request->ip();
        $licencia->dispositivo         = $request->userAgent();
        $licencia->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'La licencia fue actualizada correctamente.',
            'licencia' => $licencia
        ]);
    }

    public function delete(Request $request, $id_estudiante_licencia)
    {
        $request->validate([
            'id_estudiante_licencia' => ['required', 'numeric', 'integer', 'exists:estudiantes_licencias,id_estudiante_licencia'],
        ]);

        $licencia = (new EstudianteLicencia())->get_estudiante_licencia($id_estudiante_licencia);

        if ($licencia->estado === 0) {
            return response()->json([
                'success' => true,
                'message' => 'La licencia ya se encuentra eliminada del sistema.',
                'licencia' => $licencia
            ]);
        }

        $licencia->estado            = 0;
        $licencia->eliminado_por     = session('id_usuario');
        $licencia->fecha_eliminacion = now();
        $licencia->ip                = $request->ip();
        $licencia->dispositivo       = $request->userAgent();
        $licencia->save();

        return response()->json([
            'success' => true,
            'message' => 'La licencia fue eliminada del sistema.',
            'licencia' => $licencia
        ]);
    }
}
