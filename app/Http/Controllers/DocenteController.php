<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocenteValidation;
use App\Models\Coordinacion;
use App\Models\Docente;
use App\Models\Persona;
use App\Models\Usuario;
use App\Models\Nivel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocenteController extends Controller
{
    public function view_index()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return redirect()->route('main.index');
        }

        $niveles = (new Nivel())->get_all_niveles();
        $coordinaciones = (new Coordinacion())->get_all_coordinaciones();

        return view('docentes.index', [
            'head_title' => 'GESTIÓN DE DOCENTES',
            'niveles' => $niveles,
            'coordinaciones' => $coordinaciones
        ]);
    }

    public function view_details($id_docente)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return redirect()->route('login');
        }

        $docente = (new Docente())->get_docente($id_docente);

        return view('docentes.details', [
            'head_title' => 'DOCENTE: ' . trim($docente->persona->apellido_paterno . ' ' . $docente->persona->apellido_materno . ' ' . $docente->persona->nombres),
            'docente'    => $docente,
        ]);
    }

    public function listar()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $docentes = (new Docente())->get_all_docentes();

        return response()->json(['data' => $docentes]);
    }

    public function mostrar(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $docente = (new Docente())->get_docente($request->docente);

        return response()->json(['data' => $docente]);
    }

    public function create(DocenteValidation $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        if ($request->contrasenha !== $request->confirmar_contrasenha) {
            return response()->json([
                'success' => false,
                'message' => 'Las contraseñas no coinciden',
            ], 400);
        }

        try {
            DB::beginTransaction();

            // 1. Crear la persona
            $persona = new Persona();
            $persona->id_colegio = session('id_colegio'); // o el que corresponda
            $persona->apellido_paterno = strtoupper($request->apellido_paterno);
            $persona->apellido_materno = strtoupper($request->apellido_materno);
            $persona->nombres = strtoupper($request->nombres);
            $persona->documento_identificacion = $request->documento_identificacion;
            $persona->documento_complemento = $request->documento_complemento ?? '';
            $persona->documento_expedido = strtoupper($request->documento_expedido);
            $persona->fecha_nacimiento = $request->fecha_nacimiento;
            $persona->sexo = $request->sexo;
            $persona->idioma = $request->idioma ?? 'ESPAÑOL';
            $persona->celular = $request->celular;
            $persona->telefono = $request->telefono ?? '0';
            $persona->tipo_perfil = 'DOCENTE';
            $persona->creado_por = session('id_usuario');
            $persona->ip = session('ip');
            $persona->dispositivo = session('dispositivo');
            $persona->save();

            // 2. Crear el docente vinculado a la persona
            $docente = new Docente();
            $docente->id_persona = $persona->id_persona;
            $docente->id_nivel = $request->id_nivel;
            $docente->id_coordinacion = $request->id_coordinacion;
            $docente->especialidad = strtoupper($request->especialidad ?? '');
            $docente->grado_estudios = $request->grado_estudios;
            $docente->domicilio = $request->domicilio;
            $docente->creado_por = session('id_usuario');
            $docente->ip = session('ip');
            $docente->dispositivo = session('dispositivo');
            $docente->save();

            // 3. Crear el usuario vinculado a la persona
            $usuario = new Usuario();
            $usuario->id_persona = $persona->id_persona;
            $usuario->correo = $request->correo;
            $usuario->contrasenha = helper_encrypt($request->contrasenha);

            // Si se sube una foto de perfil, se guarda el nombre del archivo en el campo correspondiente
            if ($request->hasFile('foto_perfil')) {
                $foto = $request->file('foto_perfil');
                $nombreArchivo = 'foto_perfil_docente_' . $persona->id_persona . '.' . $foto->getClientOriginalExtension();
                $foto->storeAs('public/fotos_perfil/docentes', $nombreArchivo);
                
                $usuario->url_foto_perfil = 'storage/fotos_perfil/docentes/' . $nombreArchivo;
            }

            $usuario->creado_por = session('id_usuario');
            $usuario->ip = session('ip');
            $usuario->dispositivo = session('dispositivo');
            $usuario->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Docente registrado correctamente',
                'docente' => $docente,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el docente: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(DocenteValidation $request, $id_docente)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        try {
            DB::beginTransaction();

            $docente = (new Docente())->get_docente($id_docente);

            // 1. Actualizar la persona vinculada
            $persona = (new Persona())->get_persona($docente->id_persona);
            $persona->apellido_paterno         = strtoupper($request->apellido_paterno);
            $persona->apellido_materno         = strtoupper($request->apellido_materno);
            $persona->nombres                  = strtoupper($request->nombres);
            $persona->documento_identificacion = $request->documento_identificacion;
            $persona->documento_complemento    = $request->documento_complemento ?? '';
            $persona->documento_expedido       = strtoupper($request->documento_expedido);
            $persona->fecha_nacimiento         = $request->fecha_nacimiento;
            $persona->sexo                     = $request->sexo;
            $persona->idioma                   = $request->idioma ?? 'ESPAÑOL';
            $persona->celular                  = $request->celular;
            $persona->telefono                 = $request->telefono ?? '0';
            $persona->modificado_por           = session('id_usuario');
            $persona->ip                       = session('ip');
            $persona->dispositivo              = session('dispositivo');
            $persona->save();

            // 2. Actualizar el docente
            $docente->id_nivel        = $request->id_nivel;
            $docente->id_coordinacion = $request->id_coordinacion;
            $docente->especialidad    = strtoupper($request->especialidad ?? '');
            $docente->grado_estudios  = $request->grado_estudios;
            $docente->domicilio       = $request->domicilio;
            $docente->modificado_por  = session('id_usuario');
            $docente->ip              = session('ip');
            $docente->dispositivo     = session('dispositivo');
            $docente->save();

            // 3. Actualizar el usuario vinculado
            $usuario = (new Usuario())->get_usuario_desde_persona($persona->id_persona);
            $usuario->correo = $request->correo;
            if ($request->contrasenha) {
                if ($request->contrasenha !== $request->confirmar_contrasenha) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Las contraseñas no coinciden',
                    ], 400);
                }
                $usuario->contrasenha = helper_encrypt($request->contrasenha);
            }

            // Si se sube una nueva foto de perfil, se guarda el nombre del archivo en el campo correspondiente
            if ($request->hasFile('foto_perfil')) {
                $foto = $request->file('foto_perfil');
                $nombreArchivo = 'foto_perfil_docente_' . $persona->id_persona . '.' . $foto->getClientOriginalExtension();
                $foto->storeAs('public/fotos_perfil/docentes', $nombreArchivo);
                
                $usuario->url_foto_perfil = 'storage/fotos_perfil/docentes/' . $nombreArchivo;
            }

            $usuario->modificado_por = session('id_usuario');
            $usuario->ip = session('ip');
            $usuario->dispositivo = session('dispositivo');
            $usuario->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Docente actualizado correctamente',
                'docente' => $docente,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el docente: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'id_docente' => ['required', 'numeric', 'integer']
        ]);
        try {
            DB::beginTransaction();

            $docente = (new Docente())->get_docente($request->id_docente);
            $docente->estado = $docente->estado == '1' ? '0' : '1';
            $docente->fecha_eliminacion = $docente->estado == '0' ? Carbon::now() : null;
            $docente->eliminado_por = $docente->estado == '0' ? session('id_usuario') : null;
            $docente->ip = session('ip');
            $docente->dispositivo = session('dispositivo');
            $docente->save();

            $persona = (new Persona())->get_persona($docente->id_persona);
            $persona->estado = $docente->estado == '1' ? '0' : '1';
            $persona->fecha_eliminacion = $persona->estado == '0' ? Carbon::now() : null;
            $persona->eliminado_por = $persona->estado == '0' ? session('id_usuario') : null;
            $persona->ip = session('ip');
            $persona->dispositivo = session('dispositivo');
            $persona->save();

            $usuario = (new Usuario())->get_usuario_desde_persona($persona->id_persona);
            $usuario->estado            = $docente->estado == '1' ? '0' : '1';
            // Si el docente se archiva, el usuario pierde acceso; si se restaura, recupera acceso
            $usuario->tiene_acceso = $usuario->estado == '1' ? '0' : '1';
            $usuario->fecha_eliminacion = $usuario->estado == '0' ? Carbon::now() : null;
            $usuario->eliminado_por = $usuario->estado == '0' ? session('id_usuario') : null;
            $usuario->ip = session('ip');
            $usuario->dispositivo = session('dispositivo');
            $usuario->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $docente->estado == '1'
                    ? 'El docente fue restaurado con éxito.'
                    : 'El docente fue archivado con éxito.',
                'docente' => $docente,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el docente: ' . $e->getMessage(),
            ], 500);
        }
    }
}
