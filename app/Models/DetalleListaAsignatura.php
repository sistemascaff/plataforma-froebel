<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleListaAsignatura extends Model
{
    protected $table = 'detalles_listas_asignaturas';

    // No tiene primary key autoincremental
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false; // No tiene created_at / updated_at

    protected $fillable = [
        'id_lista_asignatura',
        'id_estudiante',
    ];

    public function listaAsignatura()
    {
        return $this->belongsTo(ListaAsignatura::class, 'id_lista_asignatura', 'id_lista_asignatura');
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante', 'id_estudiante');
    }
}
