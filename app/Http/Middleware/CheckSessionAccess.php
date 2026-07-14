<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Usuario;
use Illuminate\Support\Facades\Cache;

class CheckSessionAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Validación inicial rápida: Si la sesión misma ya no tiene los datos, lo echamos.
        if (!session('tiene_acceso') || !session('id_usuario')) {
            return $this->rechazarAcceso($request, 'No tiene acceso o su sesión ha expirado.');
        }

        $idUsuario = session('id_usuario');

        // 2. Estrategia de Caché: "Recuerda" el estado del usuario por 24 horas (86400 segundos)
        // Si el caché existe, ni siquiera toca la base de datos. Si no existe, ejecuta la función.
        $estadoUsuario = Cache::remember('acceso_usuario_' . $idUsuario, 86400, function () use ($idUsuario) {

            // Esta consulta SOLO se ejecuta si el caché expiró o si un Administrador lo borró
            $usuario = Usuario::with('persona')->find($idUsuario);

            // Escenario A: El usuario ya no existe, está inactivo o le quitaron el acceso
            if (!$usuario || $usuario->estado == 0 || $usuario->tiene_acceso == 0) {
                return ['valido' => false, 'motivo' => 'Su acceso ha sido revocado por un administrador.'];
            }

            // Escenario B: Todo está bien, guardamos en caché que es válido y su rol actual
            return [
                'valido' => true,
                'tipo_perfil' => $usuario->persona->tipo_perfil ?? 'default'
            ];
        });

        // 3. Evaluamos lo que nos devolvió el caché
        if (!$estadoUsuario['valido']) {
            session()->flush(); // Destruimos su sesión en el navegador
            return $this->rechazarAcceso($request, $estadoUsuario['motivo']);
        }

        // 4. Verificamos si su rol cambió en caliente (ej. de DOCENTE a ADMIN)
        if (session('tipo_perfil') !== $estadoUsuario['tipo_perfil']) {
            session(['tipo_perfil' => $estadoUsuario['tipo_perfil']]);
        }

        // Si todo está perfecto, continúa
        return $next($request);
    }

    /**
     * Función auxiliar para manejar las respuestas de rechazo limpiamente
     */
    private function rechazarAcceso(Request $request, $mensaje): Response
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $mensaje
            ], 403);
        }

        return redirect()->route('login')->with([
            'mensaje' => $mensaje . ' Por favor, inicie sesión nuevamente.'
        ]);
    }
}
