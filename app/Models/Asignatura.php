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
        return $this::with('materia', 'area', 'aula', 'nivel', 'coordinacion', 'creado', 'modificado', 'eliminado')->orderBy('asignatura', 'ASC')->get();
    }

    public function get_asignatura($id_asignatura)
    {
        return $this::with('horarios_asignaturas.gestion', 'listas_asignaturas.periodo.gestion', 'listas_asignaturas.docente.persona', 'materia', 'area', 'aula', 'nivel', 'coordinacion', 'creado', 'modificado', 'eliminado')->findOrFail($id_asignatura);
    }
}
