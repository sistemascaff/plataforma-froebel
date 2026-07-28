<?php

namespace App\Http\Controllers;

use App\Http\Requests\MallaCurricularValidation;
use App\Models\Area;
use App\Models\Gestion;
use App\Models\Grado;
use App\Models\Materia;
use App\Models\MallaCurricular;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MallaCurricularController extends Controller
{
    public function view_index()
    {        
        $grados = (new Grado())->get_all_grados();
        $materias = (new Materia())->get_all_materias();
        $areas = (new Area())->get_all_areas();
        $gestiones = (new Gestion())->get_all_gestiones();

        return view('mallas_curriculares.index', [
            'head_title' => 'GESTIÓN DE MALLA CURRICULAR',
            'grados' => $grados,
            'materias' => $materias,
            'areas' => $areas,
            'gestiones' => $gestiones,
        ]);
    }

    public function view_details($malla_curricular)
    {
        $malla_curricular = (new MallaCurricular())->get_malla_curricular($malla_curricular);

        return view('mallas_curriculares.details', [
            'head_title' => 'MALLA CURRICULAR',
            'malla_curricular' => $malla_curricular,
        ]);
    }

    public function listar()
    {
        $mallas_curriculares = (new MallaCurricular())->get_all_mallas_curriculares();
        return response()->json([
            'data' => $mallas_curriculares
        ]);
    }

    public function mostrar(Request $request)
    {
        $malla_curricular = (new MallaCurricular())->get_malla_curricular($request->malla_curricular);
        return response()->json([
            'data' => $malla_curricular
        ]);
    }

    public function create(MallaCurricularValidation $request)
    {
        $malla_curricular = new MallaCurricular();
        $malla_curricular->id_grado = $request->id_grado;
        $malla_curricular->id_materia = $request->id_materia;
        $malla_curricular->id_area = $request->id_area;
        $malla_curricular->id_gestion = $request->id_gestion;
        $malla_curricular->creado_por = auth()->id();
        $malla_curricular->ip = $request->ip();
        $malla_curricular->dispositivo = $request->userAgent();
        $malla_curricular->save();

        return response()->json([
            'success' => true,
            'message' => 'Malla curricular creada correctamente',
            'malla_curricular' => $malla_curricular
        ]);
    }

    public function update(MallaCurricularValidation $request, $id_malla_curricular)
    {
        $malla_curricular = (new MallaCurricular())->get_malla_curricular($id_malla_curricular);
        $malla_curricular->id_grado = $request->id_grado;
        $malla_curricular->id_materia = $request->id_materia;
        $malla_curricular->id_area = $request->id_area;
        $malla_curricular->id_gestion = $request->id_gestion;
        $malla_curricular->modificado_por = auth()->id();
        $malla_curricular->ip = $request->ip();
        $malla_curricular->dispositivo = $request->userAgent();
        $malla_curricular->save();

        return response()->json([
            'success' => true,
            'message' => 'Malla curricular actualizada correctamente',
            'malla_curricular' => $malla_curricular
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id_malla_curricular' => ['required', 'numeric', 'integer', 'exists:mallas_curriculares,id_malla_curricular'],
        ]);

        $malla_curricular = (new MallaCurricular())->get_malla_curricular($request->id_malla_curricular);
        $malla_curricular->estado = $malla_curricular->estado == '1' ? '0' : '1';
        $malla_curricular->fecha_eliminacion = $malla_curricular->estado == '0' ? Carbon::now() : null;
        $malla_curricular->eliminado_por = $malla_curricular->estado == '0' ? auth()->id() : null;
        $malla_curricular->ip = $request->ip();
        $malla_curricular->dispositivo = $request->userAgent();
        $malla_curricular->save();

        return response()->json([
            'success' => true,
            'message' => $malla_curricular->estado == '1' ? 'La malla curricular fue restaurada con éxito.' : 'La malla curricular fue archivada con éxito.',
            'malla_curricular' => $malla_curricular
        ]);
    }
}
