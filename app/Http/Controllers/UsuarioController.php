<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioValidation;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
        /*Si no tiene acceso, se redirige a la ventana de inicio de sesión.*/
        if (!session('tiene_acceso')) {
            return redirect()->route('login')->with([
                'mensaje' => '¡Para acceder al panel necesitas iniciar sesión!',
                'login_correo' => '',
                'login_contrasenha' => '',
            ]);
        }
        /*Al ingresar a la vista del panel de administración, se verifica si el usuario aún tiene acceso al sistema.*/
        $usuario = (new Usuario())->get_usuario(session('id_usuario'));
        if ($usuario->tiene_acceso == '0') {
            session(['tiene_acceso' => false]);
        }

        return view('panel.admin_super.dashboard', [
            'head_title' => 'PANEL DE ' . session('tipo_perfil'),
        ]);
    }

    public function view_index()
    {
        if (!session('tiene_acceso')) {
            return redirect()->route('login');
        }

        $empleados = (new Empleado())->getAllEmpleados();

        return view('usuarios.index', [
            'headTitle' => 'GESTIÓN DE USUARIOS',
            'empleados' => $empleados,
        ]);
    }

    public function listarUsuarios()
    {
        if (!session('tiene_acceso')) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $usuarios = (new Usuario())->getAllUsuarios();
        return response()->json([
            'data' => $usuarios
        ]);
    }

    public function mostrarUsuario(Request $request)
    {
        if (!session('tiene_acceso')) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $usuario = (new Usuario())->get_usuario($request->usuario);
        return response()->json([
            'data' => $usuario
        ]);
    }

    public function create(UsuarioValidation $request)
    {
        if (!session('tiene_acceso')) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'idEmpleado' => ['unique:usuarios'],
            'contrasenha' => ['required', 'string', 'min:8', 'max:100'],
            'recontrasenha' => ['required', 'string', 'min:8', 'max:100', 'same:contrasenha'],
        ]);

        $usuario = new Usuario();
        $usuario->idEmpleado = $request->idEmpleado;
        $usuario->correo = strtoupper($request->correo);
        $usuario->contrasenha = helper_encrypt($request->contrasenha);
        $usuario->temaPreferido = $request->temaPreferido;
        $usuario->save();

        $empleado = (new Empleado())->getEmpleado($request->idEmpleado);
        $empleado->estado = 2;
        $empleado->modificadoPor = session('idUsuario');
        $empleado->save();

        return response()->json([
            'success' => true,
            'message' => 'Usuario registrado correctamente',
            'usuario' => $usuario
        ]);
    }

    public function update(UsuarioValidation $request, $idUsuario)
    {
        if (!session('tiene_acceso')) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $usuario = (new Usuario())->get_usuario($idUsuario);
        $usuario->correo = strtoupper($request->correo);
        if ($request->contrasenha) {
            $usuario->contrasenha = helper_encrypt($request->contrasenha);
        }
        $usuario->temaPreferido = $request->temaPreferido;
        $usuario->modificadoPor = session('idUsuario');
        $usuario->save();

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado correctamente',
            'usuario' => $usuario
        ]);
    }

    public function deleteOrRestore(Request $request)
    {
        if (!session('tiene_acceso')) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'idUsuario' => ['required', 'numeric', 'integer']
        ]);

        $usuario = (new Usuario())->get_usuario($request->idUsuario);
        $usuario->estado = $usuario->estado == '1' ? '0' : '1';
        $usuario->modificadoPor = session('idUsuario');
        $usuario->save();
        return response()->json([
            'success' => true,
            'message' => $usuario->estado == '1' ? 'El usuario fue habilitado con éxito' : 'El usuario fue deshabilitado con éxito',
            'usuario' => $usuario
        ]);
    }


    public function verificar(Request $request)
    {
        $usuario = (new Usuario())->login(
            trim(strtoupper($request->correo))
        );

        if (!$usuario) {
            return redirect()->route('login')->with([
                'mensaje' => 'EL USUARIO NO EXISTE.',
                'login_correo' => $request->correo,
                'login_contrasenha' => $request->contrasenha,
            ]);
        }
        if ($usuario->tiene_acceso == '0') {
            return redirect()->route('login')->with([
                'mensaje' => 'EL USUARIO NO TIENE ACCESO AL SISTEMA.',
                'login_correo' => $request->correo,
                'login_contrasenha' => $request->contrasenha,
            ]);
        }
        if ($request->contrasenha != helper_decrypt($usuario->contrasenha)) {
            return redirect()->route('login')->with([
                'mensaje' => 'LA CONTRASEÑA ES INCORRECTA.',
                'login_correo' => $request->correo,
                'login_contrasenha' => $request->contrasenha,
            ]);
        }

        //Si el usuario y la contraseña son correctos, se crea la sesión y se redirige al panel de administración.
        session([
            'tiene_acceso' => true,
            'id_usuario' => $usuario->id_usuario,
            'correo' => $usuario->correo,
            'tipo_perfil' => $usuario->persona?->tipo_perfil,
            'nombres' => $usuario->persona?->nombres,
            'apellido_paterno' => $usuario->persona?->apellido_paterno,
            'apellido_materno' => $usuario->persona?->apellido_materno,
            'dispositivo' => gethostbyaddr($request->ip()),
            'ip' => $request->ip(),
        ]);

        //Actualizar datos de la última conexión
        $usuario->timestamps = false;
        $usuario->ultima_conexion = Carbon::now();
        $usuario->ultimo_dispositivo = gethostbyaddr($request->ip());
        $usuario->ultima_ip = $request->ip();
        $usuario->save();

        return redirect()->route('dashboard');
    }

    public function cerrar_sesion()
    {
        (new Usuario())->logout();
        return redirect()->route('main.index');
    }
}
