<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asignatura extends Model
{
    use HasFactory;

    protected $table = 'asignaturas';
    protected $primaryKey = 'id_asignatura';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación muchos a muchos con horarios_asignaturas */
    public function horarios_asignaturas()
    {
        return $this->belongsToMany(
            HorarioAsignatura::class,       // Modelo relacionado
            'detalles_horarios_asignaturas', // Tabla pivote
            'id_asignatura',                // FK en la tabla pivote hacia asignaturas
            'id_horario_asignatura'         // FK en la tabla pivote hacia horarios de asignaturas
        )->withPivot('dia_semana')        // Campos extras de la tabla pivote
            ->orderByPivot('dia_semana', 'ASC')
            ->orderBy('hora_inicio', 'ASC');
    }

    /** Relación uno a muchos con listas_asignaturas */
    public function listas_asignaturas()
    {
        return $this->hasMany(ListaAsignatura::class, 'id_asignatura', 'id_asignatura');
    }

    /** Relación FK con materias */
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id_materia');
    }

    /** Relación FK con areas */
    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area', 'id_area');
    }

    /** Relación FK con aulas */
    public function aula()
    {
        return $this->belongsTo(Aula::class, 'id_aula', 'id_aula');
    }

    /** Relación FK con niveles */
    public function nivel()
    {
        return $this->belongsTo(Nivel::class, 'id_nivel', 'id_nivel');
    }

    /** Relación FK con coordinaciones */
    public function coordinacion()
    {
        return $this->belongsTo(Coordinacion::class, 'id_coordinacion', 'id_coordinacion');
    }

    /** Relación FK con cursos */
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso', 'id_curso');
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

    public function get_all_asignaturas()
    {
        return $this::with([
            'materia:id_materia,materia',
            'area:id_area,area',
            'aula:id_aula,aula',
            'nivel:id_nivel,nivel',
            'curso:id_curso,curso',
            'coordinacion:id_coordinacion,coordinacion',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])->orderBy('asignatura', 'ASC')->get();
    }

    public function get_asignatura($id_asignatura)
    {
        return $this::with([
            'horarios_asignaturas:id_horario_asignatura,id_nivel,id_gestion,denominacion,hora_inicio,hora_fin,estado',
            'horarios_asignaturas.gestion:id_gestion,anio,estado',

            'listas_asignaturas:id_lista_asignatura,id_asignatura,id_periodo,id_docente,estado',
            'listas_asignaturas.periodo:id_periodo,id_gestion,periodo,posicion_ordinal,estado',
            'listas_asignaturas.periodo.gestion:id_gestion,anio,estado',
            'listas_asignaturas.docente:id_docente,id_persona,id_nivel,id_coordinacion,especialidad,grado_estudios,domicilio,estado',
            /* en este caso por practicidad y sobretodo confidencialidad de información sensible solo es esencial cargar el nombre completo del docente. */
            'listas_asignaturas.docente.persona:id_persona,id_colegio,apellido_paterno,apellido_materno,nombres,estado',

            'materia:id_materia,id_campo,materia,abreviatura,posicion_ordinal,estado',
            'area:id_area,id_campo,area,abreviatura,posicion_ordinal,estado',
            'aula:id_aula,aula,estado',
            'nivel:id_nivel,nivel,posicion_ordinal,estado',
            'curso:id_curso,id_grado,id_paralelo,curso,estado',
            'coordinacion:id_coordinacion,coordinacion,estado',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])->findOrFail($id_asignatura);
    }
}
