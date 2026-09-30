<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPerfilAccess
{
    public function handle(Request $request, Closure $next, ...$grupos): Response
    {
        $perfilActual = Auth::user()->persona?->tipo_perfil;

        // 1. Definimos los Alias o "Grupos de Acceso"
        $mapaGrupos = [
            'SISTEMA_COMPLETO' => ['ADMINISTRADOR', 'GERENTE GENERAL'],
            'BIBLIOTECA_GRUPO' => ['ADMINISTRADOR', 'GERENTE GENERAL', 'BIBLIOTECA', 'BIBLIOTECARIA'],
            'ASIGNATURAS_GRUPO' => ['ADMINISTRADOR', 'GERENTE GENERAL', 'DIRECTOR', 'DIRECTOR DE NIVEL', 'COORDINADOR', 'DOCENTE'],
            'LICENCIAS_GRUPO'  => ['ADMINISTRADOR', 'GERENTE GENERAL', 'SECRETARIA'],
        ];

        // 2. Extraemos todos los roles permitidos según los grupos recibidos en la ruta
        $rolesPermitidos = [];
        foreach ($grupos as $grupo) {
            if (isset($mapaGrupos[$grupo])) {
                $rolesPermitidos = array_merge($rolesPermitidos, $mapaGrupos[$grupo]);
            } else {
                $rolesPermitidos[] = $grupo; // Fallback por si se pasa un rol directo
            }
        }
        $rolesPermitidos = array_unique($rolesPermitidos);

        // 3. Verificamos si el perfil actual pertenece a los roles permitidos
        if (!in_array($perfilActual, $rolesPermitidos)) {
            return $this->rechazar($request, 'Acceso denegado. Tu perfil (' . $perfilActual . ') no tiene permisos para realizar esta acción.');
        }

        // 4. Bloqueo global de escritura (CRUD) para el GERENTE GENERAL
        // Solo se le permiten peticiones GET (visualización)
        if ($perfilActual === 'GERENTE GENERAL' && !$request->isMethod('get')) {
            return $this->rechazar($request, 'Acceso de solo lectura. El rol GERENTE GENERAL no puede crear, editar ni eliminar registros.');
        }

        return $next($request);
    }

    private function rechazar(Request $request, $mensaje)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $mensaje
            ], 403);
        }

        abort(403, $mensaje);
    }
}
