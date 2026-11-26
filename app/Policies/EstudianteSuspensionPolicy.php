<?php

namespace App\Policies;

use App\Models\EstudianteSuspension;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class EstudianteSuspensionPolicy
{
    /**
     * Intercepta todas las peticiones antes de validar métodos individuales.
     */
    public function before(Usuario $usuario, string $ability): bool|null
    {
        $perfil = $usuario->persona?->tipo_perfil;

        // El ADMINISTRADOR y los DIRECTOR DE NIVEL tienen pase libre a todas las operaciones CRUD
        if (in_array($perfil, ['ADMINISTRADOR', 'DIRECTOR DE NIVEL'])) {
            return true;
        }

        return null; // Para el resto de los roles, continúa con la evaluación normal
    }

    /**
     * Determina si el usuario puede ver cualquier suspensión (Ej. cargar la vista index o listar en DataTables)
     */
    public function viewAny(Usuario $usuario): bool
    {
        $perfil = $usuario->persona?->tipo_perfil;

        // Los roles de solo lectura permitidos por el middleware en web.php
        return in_array($perfil, ['GERENTE GENERAL', 'DIRECTOR', 'DIRECTOR DE NIVEL', 'REGENTE']);
    }

    /**
     * Determina si el usuario puede ver los detalles de una suspensión específica (mostrar)
     */
    public function view(Usuario $usuario, EstudianteSuspension $estudianteSuspension): bool
    {
        $perfil = $usuario->persona?->tipo_perfil;

        return in_array($perfil, ['GERENTE GENERAL', 'DIRECTOR', 'DIRECTOR DE NIVEL', 'REGENTE']);
    }

    /**
     * Determina si el usuario puede crear suspensiones
     */
    public function create(Usuario $usuario): Response
    {
        return Response::deny('Acceso denegado: No tienes permisos para registrar una nueva suspensión.');
    }

    /**
     * Determina si el usuario puede actualizar una suspensión
     */
    public function update(Usuario $usuario, EstudianteSuspension $estudianteSuspension): Response
    {
        return Response::deny('Acceso denegado: No tienes permisos para modificar esta suspensión.');
    }

    /**
     * Determina si el usuario puede eliminar (soft delete) una suspensión
     */
    public function delete(Usuario $usuario, EstudianteSuspension $estudianteSuspension): Response
    {
        return Response::deny('Acceso denegado: No cuentas con los privilegios para eliminar suspensiones del sistema.');
    }
}
