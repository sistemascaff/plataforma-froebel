<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleHorarioAsignatura extends Model
{
    protected $table = 'detalles_horarios_asignaturas';

    // No tiene primary key autoincremental
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false; // No tiene created_at / updated_at

    protected $fillable = [
        'id_asignatura',
        'id_horario_asignatura',
        'dia_semana'
    ];

    public function horario_asignatura()
    {
        return $this->belongsTo(HorarioAsignatura::class, 'id_horario_asignatura', 'id_horario_asignatura');
    }

    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'id_asignatura', 'id_asignatura');
    }
}
