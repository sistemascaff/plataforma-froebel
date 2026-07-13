<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPerfilAccess
{
    /**
     * Handle an incoming request.
     * El parámetro ...$perfiles recibe todos los roles permitidos separados por coma.
     */
    public function handle(Request $request, Closure $next, ...$perfiles): Response
    {
        // 1. Obtenemos el perfil actual del usuario en sesión
        $perfilActual = session('tipo_perfil');

        // 2. Verificamos si el perfil actual existe dentro del arreglo de permitidos
        if (!in_array($perfilActual, $perfiles)) {

            // 3. Si la petición es AJAX (DataTables, guardados dinámicos, etc.)
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acceso denegado. Tu perfil (' . $perfilActual . ') no tiene permisos para realizar esta acción.'
                ], 403);
            }

            // 4. Si es una petición web normal, redirigimos de forma segura
            // Puedes usar abort(403) para mostrar una pantalla de error, o redirigir al panel
            return redirect()->route('dashboard');
        }

        // Si el perfil coincide, la petición continúa
        return $next($request);
    }
}
