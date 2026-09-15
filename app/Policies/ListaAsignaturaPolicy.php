<?php

namespace App\Policies;

use App\Models\ListaAsignatura;
use App\Models\Usuario;
use Illuminate\Auth\Access\Response;

class ListaAsignaturaPolicy
{
    /**
     * Intercepta todas las peticiones antes de validar métodos individuales.
     */
    public function before(Usuario $usuario, string $ability): bool|null
    {
        // El ADMIN tiene pase libre a todas las operaciones
        if ($usuario->persona?->tipo_perfil === 'ADMIN') {
            return true;
        }

        return null; // Continuar con la evaluación normal
    }

    /**
     * Determina si el usuario puede ver cualquier lista (index y datatables)
     */
    public function viewAny(Usuario $usuario): bool
    {
        $perfil = $usuario->persona?->tipo_perfil;

        // Todos los roles del middleware pueden entrar al index. 
        // El controlador ya se encarga de filtrar qué ve cada uno en el método listar().
        return in_array($perfil, ['GERENTE', 'DIRECTOR', 'SUBDIRECTOR', 'COORDINADOR', 'DOCENTE']);
    }

    /**
     * Determina si el usuario puede ver los detalles de una lista específica (mostrar, view_details)
     */
    public function view(Usuario $usuario, ListaAsignatura $listaAsignatura): bool|Response
    {
        $perfil = $usuario->persona?->tipo_perfil;

        if (in_array($perfil, ['GERENTE', 'DIRECTOR'])) {
            return true;
        }

        // Subdirector: Solo ve listas de asignaturas que pertenezcan a su nivel
        if ($perfil === 'SUBDIRECTOR') {
            return $usuario->persona->docente->id_nivel === $listaAsignatura->asignatura->id_nivel;
        }

        // Coordinador: Solo ve listas de asignaturas de su coordinación
        if ($perfil === 'COORDINADOR') {
            return $usuario->persona->docente->id_coordinacion === $listaAsignatura->asignatura->id_coordinacion;
        }

        // Docente: Corroboramos estrictamente que esté asignado como titular de ESTA lista
        if ($perfil === 'DOCENTE') {
            if ($usuario->persona->docente->id_docente === $listaAsignatura->id_docente) {
                return true;
            }
            return Response::deny('Acceso denegado: No figuras como el docente titular asignado a esta lista de asignatura.');
        }

        return false;
    }

    /**
     * Determina si el usuario puede actualizar la lista (update y actualizar_docente)
     */
    public function update(Usuario $usuario, ListaAsignatura $listaAsignatura): Response|bool
    {
        $perfil = $usuario->persona?->tipo_perfil;

        // Solo Subdirector y Coordinador pueden llegar aquí (Admin ya fue aprobado en before)
        // El resto (Gerente, Director, Docente) tienen solo lectura
        if (!in_array($perfil, ['SUBDIRECTOR', 'COORDINADOR'])) {
            return Response::deny('Acceso denegado: Tu rol de ' . $perfil . ' tiene permisos de solo lectura para las listas.');
        }

        // Reutilizamos la lógica de lectura para garantizar que no modifiquen listas de otros niveles/coordinaciones
        if ($perfil === 'SUBDIRECTOR' && $usuario->persona->docente->id_nivel !== $listaAsignatura->asignatura->id_nivel) {
            return Response::deny('Acceso denegado: No puedes modificar una lista que no pertenece a tu nivel asignado.');
        }

        if ($perfil === 'COORDINADOR' && $usuario->persona->docente->id_coordinacion !== $listaAsignatura->asignatura->id_coordinacion) {
            return Response::deny('Acceso denegado: No puedes modificar una lista que no pertenece a tu coordinación.');
        }

        return true;
    }
}
