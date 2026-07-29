<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Usuario;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class CheckSessionAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verificamos si NO está autenticado nativamente
        if (!Auth::check()) {
            return $this->rechazarAcceso($request, 'Su sesión ha expirado. Por favor, inicie sesión nuevamente.', 401);
        }

        // 2. Si está autenticado, verificamos rápidamente si su acceso fue bloqueado
        if (Auth::user()->tiene_acceso == 0) {
            Auth::logout();
            return $this->rechazarAcceso($request, 'No tiene acceso al sistema.', 403);
        }

        $idUsuario = Auth::id();

        // 3. Estrategia de Caché: "Recuerda" el estado del usuario en BD por 24 horas (86400 segundos)
        $estadoUsuario = Cache::remember('acceso_usuario_' . $idUsuario, 86400, function () use ($idUsuario) {

            // Esta consulta SOLO se ejecuta si el caché expiró o si un Administrador lo borró (ej. al cambiarle el rol)
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

        // 4. Evaluamos lo que nos devolvió el caché
        if (!$estadoUsuario['valido']) {
            Auth::logout(); // Destruimos sesión nativa
            session()->flush(); // Limpiamos cualquier rastro manual por seguridad
            return $this->rechazarAcceso($request, $estadoUsuario['motivo'], 403);
        }

        // Si todo está perfecto, continúa la petición
        return $next($request);
    }

    /**
     * Función auxiliar para manejar las respuestas de rechazo limpiamente
     */
    private function rechazarAcceso(Request $request, $mensaje, $codigoError = 403): Response
    {
        // Si es AJAX, devolvemos el JSON con el código correspondiente
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $mensaje
            ], $codigoError);
        }

        // Si es web, lanzamos el abort, lo que mostrará la vista 401.blade.php o 403.blade.php
        abort($codigoError, $mensaje);
    }
}
