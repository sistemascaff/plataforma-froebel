<?php

namespace App\Policies;

use App\Models\Estudiante;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class EstudiantePolicy
{
    /**
     * Intercepta todas las peticiones antes de validar métodos individuales.
     */
    public function before(Usuario $usuario, string $ability): bool|null
    {
        $perfil = $usuario->persona?->tipo_perfil;

        // El ADMIN tiene pase libre a todas las operaciones CRUD
        if ($perfil === 'ADMIN') {
            return true;
        }

        return null; // Continuar con la evaluación normal
    }

    /**
     * Determina si el usuario puede ver la lista general de estudiantes (index, listar)
     */
    public function viewAny(Usuario $usuario): bool
    {
        $perfil = $usuario->persona?->tipo_perfil;

        // Estos roles tienen acceso de lectura (El ADMIN ya fue aprobado en before)
        return in_array($perfil, ['GERENTE', 'SECRETARIA ACADEMICA', 'DIRECTOR', 'SUBDIRECTOR', 'COORDINADOR']);
    }

    /**
     * Determina si el usuario puede ver los detalles de un estudiante específico
     */
    public function view(Usuario $usuario, Estudiante $estudiante): bool
    {
        $perfil = $usuario->persona?->tipo_perfil;

        return in_array($perfil, ['GERENTE', 'SECRETARIA ACADEMICA', 'DIRECTOR', 'SUBDIRECTOR']);
    }

    /**
     * Determina si el usuario puede crear registros de estudiantes
     */
    public function create(Usuario $usuario): Response
    {
        // Nota: también se puede retornar true o false directamente, pero Response permite personalizar el mensaje de denegación.
        return $usuario->persona?->tipo_perfil === 'SECRETARIA ACADEMICA'
            ? Response::allow()
            : Response::deny('Acceso denegado: Solo la Secretaría Académica puede registrar nuevos estudiantes.');
    }

    /**
     * Determina si el usuario puede actualizar un estudiante
     */
    public function update(Usuario $usuario, Estudiante $estudiante): Response
    {
        return $usuario->persona?->tipo_perfil === 'SECRETARIA ACADEMICA'
            ? Response::allow()
            : Response::deny('Acceso denegado: No tienes permisos para modificar los datos de este estudiante.');
    }

    /**
     * Determina si el usuario puede archivar/eliminar un estudiante
     */
    public function delete(Usuario $usuario, Estudiante $estudiante): Response
    {
        return $usuario->persona?->tipo_perfil === 'SECRETARIA ACADEMICA'
            ? Response::allow()
            : Response::deny('Acceso denegado: No cuentas con privilegios para eliminar registros de estudiantes.');
    }
}
