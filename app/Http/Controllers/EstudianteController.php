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

class EstudianteController extends Controller
{
    public function view_index()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return redirect()->route('main.index');
        }

        $cursos = (new Curso())->get_all_cursos();

        return view('estudiantes.index', [
            'head_title' => 'GESTIÓN DE ESTUDIANTES',
            'cursos' => $cursos
        ]);
    }

    public function view_details(int $id_estudiante)
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return redirect()->route('login');
        }

        $estudiante = (new Estudiante())->get_estudiante($id_estudiante);

        return view('estudiantes.details', [
            'head_title' => 'ESTUDIANTE: ' . trim($estudiante->persona->apellido_paterno . ' ' . $estudiante->persona->apellido_materno . ' ' . $estudiante->persona->nombres),
            'estudiante'    => $estudiante,
        ]);
    }

    public function listar()
    {
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

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
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $estudiante = (new Estudiante())->get_estudiante($request->estudiante);
        $estudiante->persona->usuario->contrasenha_descifrada = helper_decrypt($estudiante->persona->usuario->contrasenha);

        return response()->json(['data' => $estudiante]);
    }

    public function create(EstudianteValidation $request)
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
            $persona->tipo_perfil = 'ESTUDIANTE';
            $persona->creado_por = session('id_usuario');
            $persona->ip = session('ip');
            $persona->dispositivo = session('dispositivo');
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
            $estudiante->ip = session('ip');
            $estudiante->dispositivo = session('dispositivo');
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
            $usuario->ip = session('ip');
            $usuario->dispositivo = session('dispositivo');
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
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        try {
            DB::beginTransaction();

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
            $persona->ip                       = session('ip');
            $persona->dispositivo              = session('dispositivo');
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
            $estudiante->ip              = session('ip');
            $estudiante->dispositivo     = session('dispositivo');
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
            $usuario->ip = session('ip');
            $usuario->dispositivo = session('dispositivo');
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
        if (!session('tiene_acceso') || !in_array(session('tipo_perfil'), ['ADMIN'])) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'id_estudiante' => ['required', 'numeric', 'integer']
        ]);
        try {
            DB::beginTransaction();

            $estudiante = (new Estudiante())->get_estudiante($request->id_estudiante);
            $estudiante->estado = $estudiante->estado == '1' ? '0' : '1';
            $estudiante->fecha_eliminacion = $estudiante->estado == '0' ? Carbon::now() : null;
            $estudiante->eliminado_por = $estudiante->estado == '0' ? session('id_usuario') : null;
            $estudiante->ip = session('ip');
            $estudiante->dispositivo = session('dispositivo');
            $estudiante->save();

            $persona = (new Persona())->get_persona($estudiante->id_persona);
            $persona->estado = $estudiante->estado == '1' ? '0' : '1';
            $persona->fecha_eliminacion = $persona->estado == '0' ? Carbon::now() : null;
            $persona->eliminado_por = $persona->estado == '0' ? session('id_usuario') : null;
            $persona->ip = session('ip');
            $persona->dispositivo = session('dispositivo');
            $persona->save();

            $usuario = (new Usuario())->get_usuario_desde_persona($persona->id_persona);
            $usuario->estado            = $estudiante->estado == '1' ? '0' : '1';
            // Si el estudiante se archiva, el usuario pierde acceso; si se restaura, recupera acceso
            $usuario->tiene_acceso = $usuario->estado == '1' ? '0' : '1';
            $usuario->fecha_eliminacion = $usuario->estado == '0' ? Carbon::now() : null;
            $usuario->eliminado_por = $usuario->estado == '0' ? session('id_usuario') : null;
            $usuario->ip = session('ip');
            $usuario->dispositivo = session('dispositivo');
            $usuario->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $estudiante->estado == '1'
                    ? 'El estudiante fue restaurado con éxito.'
                    : 'El estudiante fue archivado con éxito.',
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
