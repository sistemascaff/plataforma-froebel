<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grado extends Model
{
    use HasFactory;

    protected $table = 'grados';
    protected $primaryKey = 'id_grado';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

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

    public function get_all_grados()
    {
        return $this::with('nivel', 'creado', 'modificado', 'eliminado')
            ->orderBy('id_nivel', 'ASC')->orderBy('posicion_ordinal', 'ASC')->get();
    }

    public function get_grado($id_grado)
    {
        return $this::with('nivel', 'creado', 'modificado', 'eliminado')->find($id_grado);
    }
}
