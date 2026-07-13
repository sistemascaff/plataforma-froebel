<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Verificar si el usuario NO tiene acceso en la sesión
        if (!session('tiene_acceso')) {

            // 2. Si la petición es AJAX (como tus DataTables), devolvemos un JSON con error 403
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene acceso o su sesión ha expirado.'
                ], 403);
            }

            // 3. Si es una petición web normal, lo redirigimos al login
            return redirect()->route('login')->with([
                'mensaje' => 'No tiene acceso o su sesión ha expirado. Por favor, inicie sesión nuevamente.'
            ]);
        }

        // Si todo está bien, la petición continúa hacia el controlador
        return $next($request);
    }
}
