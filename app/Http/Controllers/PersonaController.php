<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonaValidation;
use App\Models\Docente;
use App\Models\Persona;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PersonaController extends Controller
{
    public function view_index()
    {
        return view('personas.index', [
            'head_title' => 'GESTIÓN DE PERSONAL'
        ]);
    }

    public function view_details($id_persona)
    {
        $persona = (new Persona())->get_persona($id_persona);

        return view('personas.details', [
            'head_title' => "{$persona->tipo_perfil}: {$persona->nombres_apellidos}",
            'persona'    => $persona,
        ]);
    }

    public function view_perfil()
    {
        $tipo_perfil = Auth::user()->persona?->tipo_perfil;

        if (in_array($tipo_perfil, ['ADMIN', 'GERENTE', 'DIRECTOR', 'SECRETARIA ACADEMICA', 'BIBLIOTECARIA'])) {
            $persona = (new Persona())->get_persona(auth()->user()->id_persona);
            return view('personas.details', [
                'head_title' => "Mi perfil",
                'persona'    => $persona,
            ]);
        }

        if (in_array($tipo_perfil, ['SUBDIRECTOR', 'COORDINADOR', 'DOCENTE'])) {
            $docente = (new Docente())->get_docente(auth()->user()->persona->docente?->id_docente);
            return view('docentes.details', [
                'head_title' => "Mi perfil",
                'docente'    => $docente,
            ]);
        }
    }

    public function listar()
    {
        // La función get_personal() se salta la convención de nombres debido a que exceptúa los tipos de perfil que ya tienen sus módulos dedicados (SUBDIRECTOR, COORDINADOR, DOCENTE = Módulo de gestión de docentes) y ESTUDIANTE, por ende solo recupera a las demás personas que también son parte del personal administrativo y académico de la institución. 
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

    public function create(PersonaValidation $request)
    {
        if ($request->contrasenha !== $request->confirmar_contrasenha) {
            return response()->json([
                'success' => false,
                'message' => 'Las contraseñas no coinciden',
            ], 400);
        }

        if ($request->tipo_perfil === 'ADMIN' && auth()->id() !== 1 && auth()->user()->persona->tipo_perfil !== 'ADMIN') {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado: Solo el primer administrador puede crear otro administrador',
            ], 403);
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

            // Tipos de perfil existentes actualmente: ADMIN, GERENTE, DIRECTOR, SECRETARIA ACADEMICA, BIBLIOTECARIA.
            $persona->tipo_perfil = $request->tipo_perfil;
            $persona->creado_por = auth()->id();
            $persona->ip = $request->ip();
            $persona->dispositivo = $request->userAgent();
            $persona->save();

            // 2. Crear el usuario vinculado a la persona
            $usuario = new Usuario();
            $usuario->id_persona = $persona->id_persona;
            $usuario->correo = $request->correo;
            $usuario->contrasenha = helper_encrypt($request->contrasenha);

            // Si se sube una foto de perfil, se guarda el nombre del archivo en el campo correspondiente
            if ($request->hasFile('foto_perfil')) {
                $foto = $request->file('foto_perfil');
                $nombreArchivo = 'persona_' . $persona->id_persona . '_' . Str::random(32) . "." . $foto->getClientOriginalExtension();
                $foto->storeAs('public/fotos_perfil/personas', $nombreArchivo);

                $usuario->url_foto_perfil = "public/storage/fotos_perfil/personas/{$nombreArchivo}";
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
                'message' => 'Personal registrado correctamente',
                'persona' => $persona,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el personal: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(PersonaValidation $request, $id_persona)
    {
        DB::beginTransaction();
        try {
            // 1. Actualizar la persona vinculada
            $persona = (new Persona())->get_persona($id_persona);
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
            $persona->tipo_perfil              = $request->tipo_perfil;
            $persona->modificado_por           = auth()->id();
            $persona->ip                       = $request->ip();
            $persona->dispositivo              = $request->userAgent();
            $persona->save();

            // 2. Actualizar el usuario vinculado
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
                $nombreArchivo = 'persona_' . $persona->id_persona . '_' . Str::random(32) . "." . $foto->getClientOriginalExtension();
                $foto->storeAs('public/fotos_perfil/personas', $nombreArchivo);

                $usuario->url_foto_perfil = "public/storage/fotos_perfil/personas/{$nombreArchivo}";
            }

            $usuario->modificado_por = auth()->id();
            $usuario->ip = $request->ip();
            $usuario->dispositivo = $request->userAgent();
            $usuario->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Personal actualizado correctamente',
                'persona' => $persona,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el personal: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id_persona' => ['required', 'numeric', 'integer', 'exists:personas,id_persona'],
        ]);

        DB::beginTransaction();
        try {
            $persona = (new Persona())->get_persona($request->id_persona);

            $nuevoEstado = $persona->estado == '1' ? '0' : '1';
            $seArchiva   = $nuevoEstado === '0';

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
                    ? 'Personal archivado con éxito.'
                    : 'Personal restaurado con éxito.',
                'persona' => $persona,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar al personal: ' . $e->getMessage(),
            ], 500);
        }
    }
}
