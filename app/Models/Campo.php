<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campo extends Model
{
    use HasFactory;

    protected $table = 'campos';
    protected $primaryKey = 'id_campo';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación uno a muchos con áreas */
    public function areas()
    {
        return $this->hasMany(Area::class, 'id_campo', 'id_campo')
            ->orderBy('posicion_ordinal', 'ASC');
    }

    /** Relación uno a muchos con materias */
    public function materias()
    {
        return $this->hasMany(Materia::class, 'id_campo', 'id_campo')
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

    public function get_all_campos()
    {
        return $this::with('areas', 'materias', 'creado', 'modificado', 'eliminado')->orderBy('posicion_ordinal', 'ASC')->get();
    }

    public function get_campo($id_campo)
    {
        return $this::with('areas', 'materias', 'creado', 'modificado', 'eliminado')->find($id_campo);
    }
}
