<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nivel extends Model
{
    use HasFactory;

    protected $table = 'niveles';
    protected $primaryKey = 'id_nivel';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación uno a muchos con grados */
    public function grados()
    {
        return $this->hasMany(Grado::class, 'id_nivel', 'id_nivel')
        ->orderBy('posicion_ordinal', 'ASC');
    }

    /** Relación uno a muchos con asignaturas */
    public function asignaturas()
    {
        return $this->hasMany(Asignatura::class, 'id_nivel', 'id_nivel');
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

    public function get_all_niveles()
    {
        return $this::with('grados', 'asignaturas', 'creado:id_usuario,correo', 'modificado:id_usuario,correo', 'eliminado:id_usuario,correo')->orderBy('posicion_ordinal', 'ASC')->get();
    }

    public function get_nivel($id_nivel)
    {
        return $this::with('grados', 'asignaturas', 'creado:id_usuario,correo', 'modificado:id_usuario,correo', 'eliminado:id_usuario,correo')->findOrFail($id_nivel);
    }
}
