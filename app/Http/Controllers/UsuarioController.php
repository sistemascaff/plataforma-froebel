<?php

namespace App\Http\Controllers;

use App\Models\Persona;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class UsuarioController extends Controller
{
    public function view_main_index()
    {
        return view('index', [
            'head_title' => 'Index'
        ]);
    }

    public function view_iniciar_sesion()
    {
        return view('usuarios.login');
    }

    public function view_dashboard()
    {
        if (session('tipo_perfil') === 'ADMIN') {
            return view('panel.admin_super.dashboard', [
                'head_title' => 'PANEL DE ' . session('tipo_perfil'),
            ]);
        } else if (session('tipo_perfil') === 'BIBLIOTECARIA') {
            return view('panel.biblioteca.dashboard', [
                'head_title' => 'PANEL DE ' . session('tipo_perfil'),
            ]);
        } else {
            return redirect()->route('main.index');
        }
    }

    public function view_index()
    {
        return view('usuarios.index', [
            'head_title' => 'GESTIÓN DE USUARIOS',
        ]);
    }

    public function listar()
    {
        $usuarios = (new Usuario())->get_all_usuarios();

        return response()->json([
            'data' => $usuarios
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id_usuario' => ['required', 'numeric', 'integer', 'exists:usuarios,id_usuario'],
        ]);

        $usuario = (new Usuario())->get_usuario($request->id_usuario);

        $tipo_perfil = $usuario->persona?->tipo_perfil;
        $nombreCompleto = trim("{$usuario->persona?->apellido_paterno} {$usuario->persona?->apellido_materno} {$usuario->persona?->nombres}");

        // Se valida que el usuario no sea de tipo ADMIN antes de permitir archivar o desarchivar
        if ($tipo_perfil === 'ADMIN') {
            return response()->json([
                'success' => false,
                'message' => "No se puede archivar o desarchivar al usuario <b class=\"text-primary\">{$nombreCompleto}</b> porque es <b class=\"text-info\">ADMIN</b>.",
            ], 403);
        }

        DB::beginTransaction();
        try {
            $nuevoEstado = $usuario->estado == '1' ? '0' : '1';
            // Retorna true si el nuevo estado es '0' (archivado), de lo contrario retorna false
            $seArchiva   = $nuevoEstado === '0';

            $usuario->estado = $nuevoEstado;
            $usuario->tiene_acceso = $nuevoEstado; // si "tiene_acceso" debe reflejar el estado
            $usuario->fecha_eliminacion = $seArchiva ? Carbon::now() : null;
            $usuario->eliminado_por = $seArchiva ? session('id_usuario') : null;
            $usuario->ip = $request->ip();
            $usuario->dispositivo = $request->userAgent();
            $usuario->save();

            $persona = (new Persona())->get_persona($usuario->id_persona);
            $persona->estado = $nuevoEstado;
            $persona->fecha_eliminacion = $seArchiva ? Carbon::now() : null;
            $persona->eliminado_por = $seArchiva ? session('id_usuario') : null;
            $persona->ip = $request->ip();
            $persona->dispositivo = $request->userAgent();
            $persona->save();

            if ($tipo_perfil === 'DOCENTE' && $persona->docente) {
                $docente = $persona->docente;
                $docente->estado = $nuevoEstado;
                $docente->fecha_eliminacion = $seArchiva ? Carbon::now() : null;
                $docente->eliminado_por = $seArchiva ? session('id_usuario') : null;
                $docente->ip = $request->ip();
                $docente->dispositivo = $request->userAgent();
                $docente->save();
            } elseif ($tipo_perfil === 'ESTUDIANTE' && $persona->estudiante) {
                $estudiante = $persona->estudiante;
                $estudiante->estado = $nuevoEstado;
                $estudiante->fecha_eliminacion = $seArchiva ? Carbon::now() : null;
                $estudiante->eliminado_por = $seArchiva ? session('id_usuario') : null;
                $estudiante->ip = $request->ip();
                $estudiante->dispositivo = $request->userAgent();
                $estudiante->save();
            }

            DB::commit();

            // Limpiar la caché del usuario independientemente de si se archivó o desarchivó, ya que su estado ha cambiado y queremos asegurarnos de que la próxima vez que se verifique el acceso, se obtenga la información más reciente.
            Cache::forget('acceso_usuario_' . $usuario->id_usuario);

            return response()->json([
                'success' => true,
                'message' => $usuario->estado == '1'
                    ? "El usuario <b>{$nombreCompleto}</b> con el tipo de perfil <b>{$tipo_perfil}</b> fue restaurado con éxito."
                    : "El usuario <b>{$nombreCompleto}</b> con el tipo de perfil <b>{$tipo_perfil}</b> fue archivado con éxito.",
                'usuario' => $usuario,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => "Error al eliminar el usuario: <b>{$e->getMessage()}</b>",
            ], 500);
        }
    }

    public function verificar(Request $request)
    {
        $usuario = (new Usuario())->login(
            trim(strtoupper($request->correo))
        );

        if (!$usuario) {
            return redirect()->route('login')->with([
                'mensaje' => "El usuario con el correo {$request->correo} no existe.",
                'login_correo' => $request->correo,
                'login_contrasenha' => $request->contrasenha,
            ]);
        }
        if ($usuario->tiene_acceso == '0') {
            return redirect()->route('login')->with([
                'mensaje' => "El usuario con el correo {$request->correo} no tiene acceso al sistema.",
                'login_correo' => $request->correo,
                'login_contrasenha' => $request->contrasenha,
            ]);
        }
        if ($request->contrasenha != helper_decrypt($usuario->contrasenha)) {
            return redirect()->route('login')->with([
                'mensaje' => 'Contraseña incorrecta.',
                'login_correo' => $request->correo,
                'login_contrasenha' => $request->contrasenha,
            ]);
        }

        //Si el usuario y la contraseña son correctos, se crea la sesión y se redirige al panel de administración.
        session([
            'tiene_acceso' => true,
            'id_usuario' => $usuario->id_usuario,
            'correo' => $usuario->correo,
            'id_colegio' => $usuario->persona?->id_colegio,
            'tipo_perfil' => $usuario->persona?->tipo_perfil,
            'nombres' => $usuario->persona?->nombres,
            'apellido_paterno' => $usuario->persona?->apellido_paterno,
            'apellido_materno' => $usuario->persona?->apellido_materno,
        ]);

        //Actualizar datos de la última conexión
        $usuario->timestamps = false;
        $usuario->ultima_conexion = Carbon::now();
        $usuario->ultimo_dispositivo = $request->userAgent();
        $usuario->ultima_ip = $request->ip();
        $usuario->save();

        return redirect()->route('dashboard');
    }

    public function cerrar_sesion()
    {
        session()->flush();
        return redirect()->route('main.index');
    }
}
