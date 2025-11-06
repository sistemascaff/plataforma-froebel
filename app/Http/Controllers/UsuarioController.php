<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioValidation;
use App\Models\Usuario;
use Illuminate\Http\Request;
use App\Models\Empleado;
use App\Models\Venta;

class UsuarioController extends Controller
{
    public function view_iniciar_sesion()
    {
        return view('login');
    }

    public function view_dashboard()
    {
        /*Si no tiene acceso, se redirige a la ventana de inicio de sesión.*/
        if (!session('tieneAcceso')) {
            return redirect()->route('login');
        }
        /*Al ingresar a la vista del panel de administración, se verifica si el usuario aún tiene acceso al sistema.*/
        $usuario = (new Usuario())->getUsuario(session('idUsuario'));
        if ($usuario->estado == '0') {
            session(['tieneAcceso' => false]);
        }

        $estadisticas = (new Venta())->dashboard_getEstadisticasVentas();
        $saldos_pendientes = (new Venta())->dashboard_getClientesConSaldo();
        $saldos_pendientes_detalles = (new Venta())->dashboard_getVentasConSaldo();

        return view('panel.admin', [
            'headTitle' => 'PANEL DE ADMINISTRACIÓN',
            'estadisticas' => $estadisticas,
            'saldos_pendientes' => $saldos_pendientes,
            'saldos_pendientes_detalles' => $saldos_pendientes_detalles,
        ]);
    }

    public function view_index()
    {
        if (!session('tieneAcceso')) {
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
        if (!session('tieneAcceso')) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $usuarios = (new Usuario())->getAllUsuarios();
        return response()->json([
            'data' => $usuarios
        ]);
    }

    public function mostrarUsuario(Request $request)
    {
        if (!session('tieneAcceso')) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $usuario = (new Usuario())->getUsuario($request->usuario);
        return response()->json([
            'data' => $usuario
        ]);
    }

    public function create(UsuarioValidation $request)
    {
        if (!session('tieneAcceso')) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'idEmpleado' => ['unique:usuarios'],
            'contrasenha' => ['required', 'string', 'min:8', 'max:100'],
            'recontrasenha' => ['required', 'string', 'min:8', 'max:100', 'same:contrasenha'],
        ]);

        $usuario = new Usuario();
        $usuario->idEmpleado = $request->idEmpleado;
        $usuario->nombreUsuario = strtoupper($request->nombreUsuario);
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
        if (!session('tieneAcceso')) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $usuario = (new Usuario())->getUsuario($idUsuario);
        $usuario->nombreUsuario = strtoupper($request->nombreUsuario);
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
        if (!session('tieneAcceso')) {
            return response()->json(['success' => false, 'message' => 'No tiene acceso'], 403);
        }

        $request->validate([
            'idUsuario' => ['required', 'numeric', 'integer']
        ]);

        $usuario = (new Usuario())->getUsuario($request->idUsuario);
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
            trim(strtoupper($request->nombreUsuario))
        );

        if (!$usuario) {
            return redirect()->route('login')->with([
                'mensaje' => 'EL USUARIO NO EXISTE.',
                'loginNombreUsuario' => $request->nombreUsuario,
                'loginContrasenha' => $request->contrasenha,
            ]);
        }
        if ($usuario->estado == '0') {
            return redirect()->route('login')->with([
                'mensaje' => 'EL USUARIO NO TIENE ACCESO AL SISTEMA.',
                'loginNombreUsuario' => $request->nombreUsuario,
                'loginContrasenha' => $request->contrasenha,
            ]);
        }
        if ($request->contrasenha != helper_decrypt($usuario->contrasenha)) {
            return redirect()->route('login')->with([
                'mensaje' => 'LA CONTRASEÑA ES INCORRECTA.',
                'loginNombreUsuario' => $request->nombreUsuario,
                'loginContrasenha' => $request->contrasenha,
            ]);
        }
        //Si el usuario y la contraseña son correctos, se crea la sesión y se redirige al panel de administración.
        session([
            'tieneAcceso' => true,
            'idUsuario' => $usuario->idUsuario,
            'idEmpleado' => $usuario->idEmpleado,
            'nombreUsuario' => $usuario->nombreUsuario,
            'temaPreferido' => $usuario->temaPreferido,
            'nombreEmpleado' => $usuario->empleado->nombreEmpleado,
        ]);
        return redirect()->route('dashboard');
    }

    public function cerrar_sesion()
    {
        (new Usuario())->logout();
        return redirect()->route('login');
    }
}
