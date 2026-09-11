<?php

namespace App\Policies;

use App\Models\Asignatura;
use App\Models\Usuario;
use App\Models\ListaAsignatura;
use Illuminate\Auth\Access\Response;

class AsignaturaPolicy
{
    /**
     * Intercepta todas las peticiones antes de validar métodos individuales.
     */
    public function before(Usuario $usuario, string $ability): bool|null
    {
        $perfil = $usuario->persona?->tipo_perfil;

        // El ADMIN tiene pase libre a todas las operaciones
        if ($perfil === 'ADMIN') {
            return true;
        }

        return null; // Continuar con la evaluación normal
    }

    /**
     * Determina si el usuario puede ver cualquier asignatura (Ej. DataTables)
     */
    public function viewAny(Usuario $usuario): bool
    {
        $perfil = $usuario->persona?->tipo_perfil;

        // El DOCENTE opera exclusivamente desde sus Listas, no desde el catálogo general de asignaturas.
        if ($perfil === 'DOCENTE') {
            return false;
        }

        return in_array($perfil, ['GERENTE', 'DIRECTOR', 'SUBDIRECTOR', 'COORDINADOR']);
    }

    /**
     * Determina si el usuario puede ver una asignatura específica (mostrar, view_details)
     */
    public function view(Usuario $usuario, Asignatura $asignatura): bool
    {
        $perfil = $usuario->persona?->tipo_perfil;

        if (in_array($perfil, ['GERENTE', 'DIRECTOR'])) {
            return true;
        }

        if ($perfil === 'SUBDIRECTOR') {
            // Verifica que la asignatura corresponda al nivel que el subdirector gestiona
            return $usuario->persona->docente->id_nivel === $asignatura->id_nivel;
        }

        if ($perfil === 'COORDINADOR') {
            // Verifica que la asignatura corresponda a la coordinación asignada
            return $usuario->persona->docente->id_coordinacion === $asignatura->id_coordinacion;
        }

        if ($perfil === 'DOCENTE') {
            // Verifica si el docente figura en alguna de las listas de esta asignatura
            return ListaAsignatura::where('id_asignatura', $asignatura->id_asignatura)
                ->where('id_docente', $usuario->persona->docente->id_docente)
                ->exists();
        }

        return false;
    }

    /**
     * Determina si el usuario puede crear asignaturas
     */
    public function create(Usuario $usuario): Response
    {
        $perfil = $usuario->persona?->tipo_perfil;

        // El GERENTE solo tiene lectura. 
        if ($perfil === 'GERENTE') {
            return Response::deny('Acceso denegado: El rol Gerencia tiene acceso de solo lectura.');
        }

        // Permitimos la creación a estos roles
        // Nota: también se puede retornar true o false directamente, pero Response permite personalizar el mensaje de denegación.
        return in_array($perfil, ['DIRECTOR', 'SUBDIRECTOR', 'COORDINADOR'])
            ? Response::allow()
            : Response::deny('Acceso denegado: Tu perfil no cuenta con permisos para crear asignaturas.');
    }

    /**
     * Determina si el usuario puede actualizar una asignatura
     */
    public function update(Usuario $usuario, Asignatura $asignatura): Response
    {
        if ($usuario->persona?->tipo_perfil === 'GERENTE') {
            return Response::deny('Acceso denegado: El rol Gerencia tiene acceso de solo lectura.');
        }

        return $this->view($usuario, $asignatura)
            ? Response::allow()
            : Response::deny('Acceso denegado: No tienes permisos para modificar esta asignatura específica.');
    }

    /**
     * Determina si el usuario puede eliminar (soft delete) una asignatura
     */
    public function delete(Usuario $usuario, Asignatura $asignatura): Response
    {
        if ($usuario->persona?->tipo_perfil === 'GERENTE') {
            return Response::deny('Acceso denegado: El rol Gerencia tiene acceso de solo lectura.');
        }

        return $this->view($usuario, $asignatura)
            ? Response::allow()
            : Response::deny('Acceso denegado: No tienes los privilegios necesarios para eliminar esta asignatura.');
    }
}
