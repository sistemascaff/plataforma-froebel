<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;

    protected $table = 'materias';
    protected $primaryKey = 'id_materia';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /* Relación uno a muchos con mallas curriculares */
    public function mallas_curriculares()
    {
        return $this->hasMany(MallaCurricular::class, 'id_materia', 'id_materia');
    }

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

    public function get_all_materias()
    {
        return $this::with([
            'campo:id_campo,campo,posicion_ordinal,estado',
            
            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])->orderBy('posicion_ordinal', 'ASC')->get();
    }

    public function get_materia($id_materia)
    {
        return $this::with([
            'mallas_curriculares:id_malla_curricular,id_grado,id_materia,id_area,id_gestion,estado',
            'mallas_curriculares.grado:id_grado,id_nivel,grado,posicion_ordinal,estado',
            'mallas_curriculares.area:id_area,id_campo,area,abreviatura,posicion_ordinal,estado',
            'mallas_curriculares.gestion:id_gestion,anio,estado',
            'campo:id_campo,campo,posicion_ordinal,estado',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])->findOrFail($id_materia);
    }
}
