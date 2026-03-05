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
        return $this::with('grados', 'creado', 'modificado', 'eliminado')->orderBy('posicion_ordinal', 'ASC')->get();
    }

    public function get_nivel($id_nivel)
    {
        return $this::with('grados', 'creado', 'modificado', 'eliminado')->findOrFail($id_nivel);
    }
}
