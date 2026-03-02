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
        return $this::with('gestion', 'nivel', 'creado', 'modificado', 'eliminado')
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
        return $this::with('gestion', 'nivel', 'creado', 'modificado', 'eliminado')->find($id_horario_asignatura);
    }
}
