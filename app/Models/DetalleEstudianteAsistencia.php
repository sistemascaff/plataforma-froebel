<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleEstudianteAsistencia extends Model
{
    protected $table = 'detalles_estudiantes_asistencias';

    // No tiene primary key autoincremental
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false; // No tiene created_at / updated_at

    protected $fillable = [
        'id_estudiante_asistencia',
        'id_estudiante',
        'tipo',
        'id_estudiante_licencia'
    ];

    // Relación hacia el estudiante
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante');
    }

    // Relación hacia la licencia
    public function estudiante_licencia()
    {
        return $this->belongsTo(EstudianteLicencia::class, 'id_estudiante_licencia');
    }
}
