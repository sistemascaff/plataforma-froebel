<?php

namespace App\Policies;

use App\Models\EstudianteLicencia;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class EstudianteLicenciaPolicy
{
    /**
     * Intercepta todas las peticiones antes de validar métodos individuales.
     */
    public function before(Usuario $usuario, string $ability): bool|null
    {
        $perfil = $usuario->persona?->tipo_perfil;

        // El ADMIN y la SECRETARIA ACADEMICA tienen pase libre a todas las operaciones CRUD
        if (in_array($perfil, ['ADMIN', 'SECRETARIA ACADEMICA'])) {
            return true;
        }

        return null; // Para el resto de los roles, continúa con la evaluación normal
    }

    /**
     * Determina si el usuario puede ver cualquier licencia (Ej. cargar la vista index o listar en DataTables)
     */
    public function viewAny(Usuario $usuario): bool
    {
        $perfil = $usuario->persona?->tipo_perfil;

        // Los roles de solo lectura permitidos por el middleware en web.php
        return in_array($perfil, ['GERENTE', 'DIRECTOR', 'SUBDIRECTOR']);
    }

    /**
     * Determina si el usuario puede ver los detalles de una licencia específica (mostrar)
     */
    public function view(Usuario $usuario, EstudianteLicencia $estudianteLicencia): bool
    {
        $perfil = $usuario->persona?->tipo_perfil;

        return in_array($perfil, ['GERENTE', 'DIRECTOR', 'SUBDIRECTOR']);
    }

    /**
     * Determina si el usuario puede crear licencias
     */
    public function create(Usuario $usuario): Response
    {
        // Nota: también se puede retornar true o false directamente, pero Response permite personalizar el mensaje de denegación.
        return Response::deny('Acceso denegado: No tienes permisos para registrar una nueva licencia.');
    }

    /**
     * Determina si el usuario puede actualizar una licencia
     */
    public function update(Usuario $usuario, EstudianteLicencia $estudianteLicencia): Response
    {
        return Response::deny('Acceso denegado: No tienes permisos para modificar esta licencia.');
    }

    /**
     * Determina si el usuario puede eliminar (soft delete) una licencia
     */
    public function delete(Usuario $usuario, EstudianteLicencia $estudianteLicencia): Response
    {
        return Response::deny('Acceso denegado: No cuentas con los privilegios para eliminar licencias del sistema.');
    }
}
