<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioAsignatura extends Model
{
    use HasFactory;

    protected $table = 'horarios_asignaturas';
    protected $primaryKey = 'id_horario_asignatura';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación muchos a muchos con asignaturas */
    public function asignaturas()
    {
        return $this->belongsToMany(
            Asignatura::class,              // Modelo relacionado
            'detalles_horarios_asignaturas', // Tabla pivote
            'id_horario_asignatura',        // FK en la tabla pivote hacia asignaturas
            'id_asignatura'                 // FK en la tabla pivote hacia horarios de asignaturas
        )->withPivot('dia_semana')        // Campos extras de la tabla pivote
            ->orderByPivot('dia_semana', 'ASC');
    }

    /** Relación FK con gestiones */
    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion', 'id_gestion');
    }

    /** Relación FK con niveles */
    public function nivel()
    {
        return $this->belongsTo(Nivel::class, 'id_nivel', 'id_nivel');
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

    public function get_all_horarios_asignaturas()
    {
        /* Se realiza un join para ordenar por año de gestión, luego por nivel y finalmente por hora de inicio. */
        return $this::with([
            'gestion:id_gestion,anio,estado',
            'nivel:id_nivel,nivel,posicion_ordinal,estado',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])
            ->join('gestiones', 'horarios_asignaturas.id_gestion', '=', 'gestiones.id_gestion')
            ->join('niveles', 'horarios_asignaturas.id_nivel', '=', 'niveles.id_nivel')
            ->orderBy('gestiones.anio', 'DESC')
            ->orderBy('niveles.posicion_ordinal', 'ASC')
            ->orderBy('horarios_asignaturas.hora_inicio', 'ASC')
            ->select('horarios_asignaturas.*')
            ->get();
    }

    public function get_horario_asignatura($id_horario_asignatura)
    {
        return $this::with([
            'asignaturas:id_asignatura,id_materia,id_area,id_aula,id_nivel,id_coordinacion,asignatura,tipo_calificacion,tipo_bloque',
            'gestion:id_gestion,anio,estado',
            'nivel:id_nivel,nivel,posicion_ordinal,estado',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])->findOrFail($id_horario_asignatura);
    }
}
