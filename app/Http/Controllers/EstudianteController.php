<?php

namespace App\Http\Controllers;

use App\Http\Requests\EstudianteValidation;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Persona;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class EstudianteController extends Controller
{
    public function view_index()
    {
        $cursos = (new Curso())->get_all_cursos();

        return view('estudiantes.index', [
            'head_title' => 'GESTIÓN DE ESTUDIANTES',
            'cursos' => $cursos
        ]);
    }

    public function view_details(int $id_estudiante)
    {
        $estudiante = (new Estudiante())->get_estudiante($id_estudiante);

        return view('estudiantes.details', [
            'head_title' => 'ESTUDIANTE: ' . trim($estudiante->persona->apellido_paterno . ' ' . $estudiante->persona->apellido_materno . ' ' . $estudiante->persona->nombres),
            'estudiante'    => $estudiante,
        ]);
    }

    public function listar()
    {
        $estudiantes = (new Estudiante())->get_all_estudiantes();

        // Iterar sobre la colección para descifrar la contraseña
        $estudiantes->map(function ($estudiante) {
            // Verificamos que las relaciones existan para evitar errores de "null pointer"
            if ($estudiante->persona && $estudiante->persona->usuario && $estudiante->persona->usuario->contrasenha) {
                // Reemplazamos el valor de la columna 'contrasenha' por su versión descifrada
                $contrasenhaCifrada = $estudiante->persona->usuario->contrasenha;
                $estudiante->persona->usuario->contrasenha_descifrada = helper_decrypt($contrasenhaCifrada);
            }
            return $estudiante;
        });

        return response()->json(['data' => $estudiantes]);
    }

    public function mostrar(Request $request)
    {
        $estudiante = (new Estudiante())->get_estudiante($request->estudiante);
        $estudiante->persona->usuario->contrasenha_descifrada = helper_decrypt($estudiante->persona->usuario->contrasenha);

        return response()->json(['data' => $estudiante]);
    }

    public function create(EstudianteValidation $request)
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
            $persona->tipo_perfil = 'ESTUDIANTE';
            $persona->creado_por = session('id_usuario');
            $persona->ip = $request->ip();
            $persona->dispositivo = $request->userAgent();
            $persona->save();

            // 2. Crear el estudiante vinculado a la persona
            $estudiante = new Estudiante();
            $estudiante->id_persona = $persona->id_persona;
            $estudiante->id_curso = $request->id_curso;
            $estudiante->nacimiento_pais = $request->nacimiento_pais;
            $estudiante->nacimiento_departamento = $request->nacimiento_departamento;
            $estudiante->nacimiento_provincia = $request->nacimiento_provincia;
            $estudiante->nacimiento_localidad = $request->nacimiento_localidad;
            $estudiante->salud_tipo_sangre = $request->salud_tipo_sangre;
            $estudiante->salud_alergias = $request->salud_alergias;
            $estudiante->salud_datos = $request->salud_datos;
            $estudiante->creado_por = session('id_usuario');
            $estudiante->ip = $request->ip();
            $estudiante->dispositivo = $request->userAgent();
            $estudiante->save();

            // 3. Crear el usuario vinculado a la persona
            $usuario = new Usuario();
            $usuario->id_persona = $persona->id_persona;
            $usuario->correo = $request->correo;
            $usuario->contrasenha = helper_encrypt($request->contrasenha);

            // Si se sube una foto de perfil, se guarda el nombre del archivo en el campo correspondiente
            if ($request->hasFile('foto_perfil')) {
                $foto = $request->file('foto_perfil');
                $nombreArchivo = 'foto_perfil_estudiante_' . $persona->id_persona . '.' . $foto->getClientOriginalExtension();
                $foto->storeAs('public/fotos_perfil/estudiantes', $nombreArchivo);

                $usuario->url_foto_perfil = 'public/storage/fotos_perfil/estudiantes/' . $nombreArchivo;
            } else {
                $usuario->url_foto_perfil = 'public/img/user.png';
            }

            $usuario->creado_por = session('id_usuario');
            $usuario->ip = $request->ip();
            $usuario->dispositivo = $request->userAgent();
            $usuario->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Estudiante registrado correctamente',
                'estudiante' => $estudiante,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el estudiante: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update(EstudianteValidation $request, int $id_estudiante)
    {
        DB::beginTransaction();
        try {
            $estudiante = (new Estudiante())->get_estudiante($id_estudiante);

            // 1. Actualizar la persona vinculada
            $persona = (new Persona())->get_persona($estudiante->id_persona);
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
            $persona->tipo_perfil = 'ESTUDIANTE';
            $persona->modificado_por           = session('id_usuario');
            $persona->ip                       = $request->ip();
            $persona->dispositivo              = $request->userAgent();
            $persona->save();

            // 2. Actualizar el estudiante
            $estudiante->id_curso = $request->id_curso;
            $estudiante->nacimiento_pais = $request->nacimiento_pais;
            $estudiante->nacimiento_departamento = $request->nacimiento_departamento;
            $estudiante->nacimiento_provincia = $request->nacimiento_provincia;
            $estudiante->nacimiento_localidad = $request->nacimiento_localidad;
            $estudiante->salud_tipo_sangre = $request->salud_tipo_sangre;
            $estudiante->salud_alergias = $request->salud_alergias;
            $estudiante->salud_datos = $request->salud_datos;
            $estudiante->modificado_por  = session('id_usuario');
            $estudiante->ip              = $request->ip();
            $estudiante->dispositivo     = $request->userAgent();
            $estudiante->save();

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
                $nombreArchivo = 'foto_perfil_estudiante_' . $persona->id_persona . '.' . $foto->getClientOriginalExtension();
                $foto->storeAs('public/fotos_perfil/estudiantes', $nombreArchivo);

                $usuario->url_foto_perfil = 'public/storage/fotos_perfil/estudiantes/' . $nombreArchivo;
            }

            $usuario->modificado_por = session('id_usuario');
            $usuario->ip = $request->ip();
            $usuario->dispositivo = $request->userAgent();
            $usuario->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Estudiante actualizado correctamente',
                'estudiante' => $estudiante,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estudiante: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id_estudiante' => ['required', 'numeric', 'integer', 'exists:estudiantes,id_estudiante'],
        ]);

        DB::beginTransaction();
        try {
            $estudiante = (new Estudiante())->get_estudiante($request->id_estudiante);

            $nuevoEstado = $estudiante->estado == '1' ? '0' : '1';
            $seArchiva   = $nuevoEstado === '0';

            $estudiante->estado = $nuevoEstado;
            $estudiante->fecha_eliminacion = $seArchiva ? Carbon::now() : null;
            $estudiante->eliminado_por = $seArchiva ? session('id_usuario') : null;
            $estudiante->ip = $request->ip();
            $estudiante->dispositivo = $request->userAgent();
            $estudiante->save();

            $persona = (new Persona())->get_persona($estudiante->id_persona);
            $persona->estado = $nuevoEstado;
            $persona->fecha_eliminacion = $seArchiva ? Carbon::now() : null;
            $persona->eliminado_por = $seArchiva ? session('id_usuario') : null;
            $persona->ip = $request->ip();
            $persona->dispositivo = $request->userAgent();
            $persona->save();

            $usuario = (new Usuario())->get_usuario_desde_persona($persona->id_persona);
            $usuario->estado = $nuevoEstado;
            // Si el estudiante se archiva, el usuario pierde acceso; si se restaura, recupera acceso
            $usuario->tiene_acceso = $seArchiva ? '0' : '1';
            $usuario->fecha_eliminacion = $seArchiva ? Carbon::now() : null;
            $usuario->eliminado_por = $seArchiva ? session('id_usuario') : null;
            $usuario->ip = $request->ip();
            $usuario->dispositivo = $request->userAgent();
            $usuario->save();

            DB::commit();

            // Limpiar la caché del usuario independientemente de si se archivó o desarchivó, ya que su estado ha cambiado y queremos asegurarnos de que la próxima vez que se verifique el acceso, se obtenga la información más reciente.
            Cache::forget('acceso_usuario_' . $usuario->id_usuario);

            return response()->json([
                'success' => true,
                'message' => $seArchiva
                    ? 'El estudiante fue archivado con éxito.'
                    : 'El estudiante fue restaurado con éxito.',
                'estudiante' => $estudiante,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el estudiante: ' . $e->getMessage(),
            ], 500);
        }
    }
}
