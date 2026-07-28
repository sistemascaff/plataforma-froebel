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
use Illuminate\Support\Facades\Cache;

class DocenteController extends Controller
{
    public function view_index()
    {
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
        $docente = (new Docente())->get_docente($id_docente);

        return view('docentes.details', [
            'head_title' => 'DOCENTE: ' . trim($docente->persona->apellido_paterno . ' ' . $docente->persona->apellido_materno . ' ' . $docente->persona->nombres),
            'docente'    => $docente,
        ]);
    }

    public function listar()
    {
        $docentes = (new Docente())->get_all_docentes();

        return response()->json(['data' => $docentes]);
    }

    public function mostrar(Request $request)
    {
        $docente = (new Docente())->get_docente($request->docente);

        return response()->json(['data' => $docente]);
    }

    public function create(DocenteValidation $request)
    {
        if ($request->contrasenha !== $request->confirmar_contrasenha) {
            return response()->json([
                'success' => false,
                'message' => 'Las contraseñas no coinciden',
            ], 400);
        }

        DB::beginTransaction();
        try {
            // 1. Crear la persona
            $persona = new Persona();
            $persona->id_colegio = Auth::user()->persona?->id_colegio; // o el que corresponda
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

            // Se prioriza el tipo de perfil de subdirector si tiene nivel asignado, coordinador si tiene coordinación asignada y sino se establece como docente
            if ($request->id_nivel) {
                $persona->tipo_perfil = 'SUBDIRECTOR';
            } elseif ($request->id_coordinacion) {
                $persona->tipo_perfil = 'COORDINADOR';
            } else {
                $persona->tipo_perfil = 'DOCENTE';
            }
            $persona->creado_por = auth()->id();
            $persona->ip = $request->ip();
            $persona->dispositivo = $request->userAgent();
            $persona->save();

            // 2. Crear el docente vinculado a la persona
            $docente = new Docente();
            $docente->id_persona = $persona->id_persona;
            $docente->id_nivel = $request->id_nivel;
            $docente->id_coordinacion = $request->id_coordinacion;
            $docente->especialidad = strtoupper($request->especialidad ?? '');
            $docente->grado_estudios = $request->grado_estudios;
            $docente->domicilio = $request->domicilio;
            $docente->creado_por = auth()->id();
            $docente->ip = $request->ip();
            $docente->dispositivo = $request->userAgent();
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

                $usuario->url_foto_perfil = 'public/storage/fotos_perfil/docentes/' . $nombreArchivo;
            } else {
                $usuario->url_foto_perfil = 'public/img/user.png';
            }

            $usuario->creado_por = auth()->id();
            $usuario->ip = $request->ip();
            $usuario->dispositivo = $request->userAgent();
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
        DB::beginTransaction();
        try {
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

            // Se prioriza el tipo de perfil de subdirector si tiene nivel asignado, coordinador si tiene coordinación asignada y sino se establece como docente
            if ($request->id_nivel) {
                $persona->tipo_perfil = 'SUBDIRECTOR';
            } elseif ($request->id_coordinacion) {
                $persona->tipo_perfil = 'COORDINADOR';
            } else {
                $persona->tipo_perfil = 'DOCENTE';
            }
            
            $persona->modificado_por           = auth()->id();
            $persona->ip                       = $request->ip();
            $persona->dispositivo              = $request->userAgent();
            $persona->save();

            // 2. Actualizar el docente
            $docente->id_nivel        = $request->id_nivel;
            $docente->id_coordinacion = $request->id_coordinacion;
            $docente->especialidad    = strtoupper($request->especialidad ?? '');
            $docente->grado_estudios  = $request->grado_estudios;
            $docente->domicilio       = $request->domicilio;
            $docente->modificado_por  = auth()->id();
            $docente->ip              = $request->ip();
            $docente->dispositivo     = $request->userAgent();
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

                $usuario->url_foto_perfil = 'public/storage/fotos_perfil/docentes/' . $nombreArchivo;
            }

            $usuario->modificado_por = auth()->id();
            $usuario->ip = $request->ip();
            $usuario->dispositivo = $request->userAgent();
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
        $request->validate([
            'id_docente' => ['required', 'numeric', 'integer', 'exists:docentes,id_docente'],
        ]);

        DB::beginTransaction();
        try {
            $docente = (new Docente())->get_docente($request->id_docente);

            $nuevoEstado = $docente->estado == '1' ? '0' : '1';
            $seArchiva   = $nuevoEstado === '0';

            $docente->estado = $nuevoEstado;
            $docente->fecha_eliminacion = $seArchiva ? Carbon::now() : null;
            $docente->eliminado_por = $seArchiva ? auth()->id() : null;
            $docente->ip = $request->ip();
            $docente->dispositivo = $request->userAgent();
            $docente->save();

            $persona = (new Persona())->get_persona($docente->id_persona);
            $persona->estado = $nuevoEstado;
            $persona->fecha_eliminacion = $seArchiva ? Carbon::now() : null;
            $persona->eliminado_por = $seArchiva ? auth()->id() : null;
            $persona->ip = $request->ip();
            $persona->dispositivo = $request->userAgent();
            $persona->save();

            $usuario = (new Usuario())->get_usuario_desde_persona($persona->id_persona);
            $usuario->estado = $nuevoEstado;
            // Si el docente se archiva, el usuario pierde acceso; si se restaura, recupera acceso
            $usuario->tiene_acceso = $seArchiva ? '0' : '1';
            $usuario->fecha_eliminacion = $seArchiva ? Carbon::now() : null;
            $usuario->eliminado_por = $seArchiva ? auth()->id() : null;
            $usuario->ip = $request->ip();
            $usuario->dispositivo = $request->userAgent();
            $usuario->save();

            DB::commit();

            // Limpiar la caché del usuario independientemente de si se archivó o desarchivó, ya que su estado ha cambiado y queremos asegurarnos de que la próxima vez que se verifique el acceso, se obtenga la información más reciente.
            Cache::forget('acceso_usuario_' . $usuario->id_usuario);

            return response()->json([
                'success' => true,
                'message' => $seArchiva
                    ? 'El docente fue archivado con éxito.'
                    : 'El docente fue restaurado con éxito.',
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
