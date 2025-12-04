<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paralelo extends Model
{
    use HasFactory;

    protected $table = 'paralelos';
    protected $primaryKey = 'id_paralelo';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

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

    public function get_all_paralelos()
    {
        return $this::with('creado', 'modificado', 'eliminado')
            ->orderBy('id_nivel', 'ASC')->orderBy('posicion_ordinal', 'ASC')->get();
    }

    public function get_paralelo($id_paralelo)
    {
        return $this::with('creado', 'modificado', 'eliminado')->find($id_paralelo);
    }
}
