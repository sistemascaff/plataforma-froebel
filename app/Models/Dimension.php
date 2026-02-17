<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dimension extends Model
{
    use HasFactory;

    protected $table = 'dimensiones';
    protected $primaryKey = 'id_dimension';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación FK con gestiones */
    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion', 'id_gestion');
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

    public function get_all_dimensiones()
    {
        return $this->with('gestion', 'creado', 'modificado', 'eliminado')
            ->join('gestiones', 'dimensiones.id_gestion', '=', 'gestiones.id_gestion')
            ->orderBy('gestiones.anio', 'desc')
            ->orderBy('dimensiones.posicion_ordinal', 'asc')
            ->select('dimensiones.*')
            ->get();
    }

    public function get_dimension($id_dimension)
    {
        return $this->with('gestion', 'creado', 'modificado', 'eliminado')->findOrFail($id_dimension);
    }
}
