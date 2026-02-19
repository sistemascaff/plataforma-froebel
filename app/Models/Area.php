<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = 'areas';
    protected $primaryKey = 'id_area';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación FK con campos */
    public function campo()
    {
        return $this->belongsTo(Campo::class, 'id_campo', 'id_campo');
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

    public function get_all_areas()
    {
        return $this::with('campo', 'creado', 'modificado', 'eliminado')->orderBy('posicion_ordinal', 'ASC')->get();
    }

    public function get_area($id_area)
    {
        return $this::with('campo', 'creado', 'modificado', 'eliminado')->find($id_area);
    }
}
