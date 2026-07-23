<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListaAsignatura extends Model
{
    use HasFactory;

    protected $table = 'listas_asignaturas';
    protected $primaryKey = 'id_lista_asignatura';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación muchos a muchos con estudiantes */
    public function estudiantes()
    {
        return $this->belongsToMany(
            Estudiante::class,
            'detalles_listas_asignaturas',
            'id_lista_asignatura',
            'id_estudiante'
        );
    }

    /** Relación FK con asignaturas */
    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'id_asignatura', 'id_asignatura');
    }

    /** Relación FK con periodos */
    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'id_periodo', 'id_periodo');
    }

    /** Relación FK con docentes */
    public function docente()
    {
        return $this->belongsTo(Docente::class, 'id_docente', 'id_docente');
    }

    /** Relación con atributo de auditoría */
    public function creado()
    {
        return $this->belongsTo(Usuario::class, 'creado_por', 'id_usuario');
    }

    /** Relación con atributo de auditoría */
    public function modificado()
    {
        return $this->belongsTo(Usuario::class, 'modificado_por', 'id_usuario');
    }

    /** Relación con atributo de auditoría */
    public function eliminado()
    {
        return $this->belongsTo(Usuario::class, 'eliminado_por', 'id_usuario');
    }

    /* Método aún no utilizado. */
    public function get_all_listas_asignaturas()
    {
        return $this->with([
            'asignatura',
            'periodo',
            'docente.persona',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])->get();
    }

    public function get_lista_asignatura($id_lista_asignatura)
    {
        return $this->with([
            'asignatura:id_asignatura,id_materia,id_area,id_aula,id_nivel,id_coordinacion,id_curso,asignatura,tipo_calificacion,tipo_bloque,estado',
            'periodo:id_periodo,id_gestion,periodo,posicion_ordinal,estado',
            'periodo.gestion:id_gestion,anio,estado',
            'docente:id_docente,id_persona,estado',
            'docente.persona:id_persona,apellido_paterno,apellido_materno,nombres,estado',
            'estudiantes:id_estudiante,id_persona,id_curso,estado',
            'estudiantes.persona:id_persona,apellido_paterno,apellido_materno,nombres,estado',
            'estudiantes.persona.usuario:id_usuario,id_persona,correo,contrasenha,url_foto_perfil,estado',
            'estudiantes.curso:id_curso,curso,estado',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])->findOrFail($id_lista_asignatura);
    }
}
