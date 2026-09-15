<?php

namespace App\Policies;

use App\Models\EstudianteAsistencia;
use App\Models\ListaAsignatura;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class EstudianteAsistenciaPolicy
{
    /**
     * Intercepta todas las peticiones para aprobar al ADMIN automáticamente.
     */
    public function before(Usuario $usuario, string $ability): bool|null
    {
        if ($usuario->persona?->tipo_perfil === 'ADMIN') {
            return true;
        }
        return null;
    }

    /**
     * Determina si el usuario puede ver la tabla general (index y datatables)
     */
    public function viewAny(Usuario $usuario): bool
    {
        $perfil = $usuario->persona?->tipo_perfil;
        return in_array($perfil, ['GERENTE', 'DIRECTOR', 'SUBDIRECTOR', 'COORDINADOR', 'DOCENTE']);
    }

    /**
     * Determina si el usuario puede ver los detalles de una asistencia específica
     */
    public function view(Usuario $usuario, EstudianteAsistencia $asistencia): bool|Response
    {
        $perfil = $usuario->persona?->tipo_perfil;
        $lista = $asistencia->lista_asignatura;

        if (in_array($perfil, ['GERENTE', 'DIRECTOR'])) {
            return true;
        }

        if ($perfil === 'SUBDIRECTOR') {
            return $usuario->persona->docente->id_nivel === $lista->asignatura->id_nivel
                ? true
                : Response::deny('Acceso denegado: Esta asistencia no pertenece a tu nivel asignado.');
        }

        if ($perfil === 'COORDINADOR') {
            return $usuario->persona->docente->id_coordinacion === $lista->asignatura->id_coordinacion
                ? true
                : Response::deny('Acceso denegado: Esta asistencia no pertenece a tu coordinación.');
        }

        if ($perfil === 'DOCENTE') {
            return $usuario->persona->docente->id_docente === $lista->id_docente
                ? true
                : Response::deny('Acceso denegado: No figuras como el docente titular en esta lista de asistencia.');
        }

        return false;
    }

    /**
     * Determina si el usuario puede CREAR una asistencia para una Lista de Asignatura
     * Nota: Recibe un modelo ListaAsignatura en lugar de EstudianteAsistencia.
     */
    public function create(Usuario $usuario, ListaAsignatura $listaAsignatura): Response|bool
    {
        $perfil = $usuario->persona?->tipo_perfil;

        // Roles de solo lectura
        if (in_array($perfil, ['GERENTE', 'DIRECTOR'])) {
            return Response::deny('Acceso denegado: Tu rol de ' . $perfil . ' tiene permisos de solo lectura.');
        }

        if ($perfil === 'SUBDIRECTOR') {
            return $usuario->persona->docente->id_nivel === $listaAsignatura->asignatura->id_nivel
                ? true
                : Response::deny('Acceso denegado: No puedes registrar asistencias para un nivel que no te corresponde.');
        }

        if ($perfil === 'COORDINADOR') {
            return $usuario->persona->docente->id_coordinacion === $listaAsignatura->asignatura->id_coordinacion
                ? true
                : Response::deny('Acceso denegado: No puedes registrar asistencias para una coordinación ajena a la tuya.');
        }

        if ($perfil === 'DOCENTE') {
            return $usuario->persona->docente->id_docente === $listaAsignatura->id_docente
                ? true
                : Response::deny('Acceso denegado: Solo el docente titular puede registrar la asistencia de esta lista.');
        }

        return false;
    }

    /**
     * Determina si el usuario puede ACTUALIZAR una asistencia existente
     */
    public function update(Usuario $usuario, EstudianteAsistencia $asistencia): Response|bool
    {
        $perfil = $usuario->persona?->tipo_perfil;
        $lista = $asistencia->lista_asignatura;

        if (in_array($perfil, ['GERENTE', 'DIRECTOR'])) {
            return Response::deny('Acceso denegado: Tu rol de ' . $perfil . ' tiene permisos de solo lectura.');
        }

        if ($perfil === 'SUBDIRECTOR') {
            return $usuario->persona->docente->id_nivel === $lista->asignatura->id_nivel
                ? true
                : Response::deny('Acceso denegado: No puedes modificar asistencias de un nivel que no te corresponde.');
        }

        if ($perfil === 'COORDINADOR') {
            return $usuario->persona->docente->id_coordinacion === $lista->asignatura->id_coordinacion
                ? true
                : Response::deny('Acceso denegado: No puedes modificar asistencias de una coordinación ajena a la tuya.');
        }

        if ($perfil === 'DOCENTE') {
            return $usuario->persona->docente->id_docente === $lista->id_docente
                ? true
                : Response::deny('Acceso denegado: Solo el docente titular puede modificar la asistencia de esta lista.');
        }

        return false;
    }
}
