<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coordinacion extends Model
{
    use HasFactory;

    protected $table = 'coordinaciones';
    protected $primaryKey = 'id_coordinacion';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación uno a muchos con asignaturas */
    public function asignaturas()
    {
        return $this->hasMany(Asignatura::class, 'id_coordinacion', 'id_coordinacion');
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

    public function get_all_coordinaciones()
    {
        return $this::with('creado', 'modificado', 'eliminado')->orderBy('coordinacion', 'ASC')->get();
    }

    public function get_coordinacion($id_coordinacion)
    {
        return $this::with('asignaturas', 'creado', 'modificado', 'eliminado')->findOrFail($id_coordinacion);
    }
}
