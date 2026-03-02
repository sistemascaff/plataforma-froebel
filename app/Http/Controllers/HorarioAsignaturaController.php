<?php

namespace App\Http\Controllers;

use App\Http\Requests\HorarioAsignaturaValidation;
use App\Models\Gestion;
use App\Models\HorarioAsignatura;
use App\Models\Nivel;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HorarioAsignaturaController extends Controller
{
    public function view_index()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return redirect()->route('main.index');
        }
        
        $niveles = (new Nivel())->get_all_niveles();
        $gestiones = (new Gestion())->get_all_gestiones();

        return view('horarios_asignaturas.index', [
            'head_title' => 'GESTIÓN DE HORARIOS DE ASIGNATURAS',
            'niveles' => $niveles,
            'gestiones' => $gestiones
        ]);
    }

    public function view_details($horario_asignatura)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return redirect()->route('login');
        }

        $horario_asignatura = (new HorarioAsignatura())->get_horario_asignatura($horario_asignatura);

        return view('horarios_asignaturas.details', [
            'head_title' => 'HORARIO: ' . $horario_asignatura->gestion->anio . ', ' . $horario_asignatura->nivel->nivel . ' - ' . $horario_asignatura->denominacion,
            'horario_asignatura' => $horario_asignatura,
        ]);
    }

    public function listar()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso',], 403);
        }

        $horarios_asignaturas = (new HorarioAsignatura())->get_all_horarios_asignaturas();
        return response()->json([
            'data' => $horarios_asignaturas
        ]);
    }

    public function mostrar(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $horario_asignatura = (new HorarioAsignatura())->get_horario_asignatura($request->horario_asignatura);
        return response()->json([
            'data' => $horario_asignatura
        ]);
    }

    public function create(HorarioAsignaturaValidation $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $horario_asignatura = new HorarioAsignatura();
        $horario_asignatura->denominacion = $request->denominacion;
        $horario_asignatura->hora_inicio = $request->hora_inicio;
        $horario_asignatura->hora_fin = $request->hora_fin;
        $horario_asignatura->id_gestion = $request->id_gestion;
        $horario_asignatura->id_nivel = $request->id_nivel;
        $horario_asignatura->creado_por = session('id_usuario');
        $horario_asignatura->ip = session('ip');
        $horario_asignatura->dispositivo = session('dispositivo');
        $horario_asignatura->save();

        return response()->json([
            'success' => true,
            'message' => 'Horario de asignaturas creado correctamente',
            'horario_asignatura' => $horario_asignatura
        ]);
    }

    public function update(HorarioAsignaturaValidation $request, $id_horario_asignatura)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $horario_asignatura = (new HorarioAsignatura())->get_horario_asignatura($id_horario_asignatura);
        $horario_asignatura->denominacion = $request->denominacion;
        $horario_asignatura->hora_inicio = $request->hora_inicio;
        $horario_asignatura->hora_fin = $request->hora_fin;
        $horario_asignatura->id_gestion = $request->id_gestion;
        $horario_asignatura->id_nivel = $request->id_nivel;
        $horario_asignatura->modificado_por = session('id_usuario');
        $horario_asignatura->ip = session('ip');
        $horario_asignatura->dispositivo = session('dispositivo');
        $horario_asignatura->save();

        return response()->json([
            'success' => true,
            'message' => 'Horario de asignaturas actualizado correctamente',
            'horario_asignatura' => $horario_asignatura
        ]);
    }

    public function delete(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'id_horario_asignatura' => ['required', 'numeric', 'integer']
        ]);

        $horario_asignatura = (new HorarioAsignatura())->get_horario_asignatura($request->id_horario_asignatura);
        $horario_asignatura->estado = $horario_asignatura->estado == '1' ? '0' : '1';
        $horario_asignatura->fecha_eliminacion = $horario_asignatura->estado == '0' ? Carbon::now() : null;
        $horario_asignatura->eliminado_por = $horario_asignatura->estado == '0' ? session('id_usuario') : null;
        $horario_asignatura->ip = session('ip');
        $horario_asignatura->dispositivo = session('dispositivo');
        $horario_asignatura->save();

        return response()->json([
            'success' => true,
            'message' => $horario_asignatura->estado == '1' ? 'El horario de asignaturas fue restaurado con éxito.' : 'El horario de asignaturas fue archivado con éxito.',
            'horario_asignatura' => $horario_asignatura
        ]);
    }
}
