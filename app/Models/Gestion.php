<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
    use HasFactory;

    protected $table = 'gestiones';
    protected $primaryKey = 'id_gestion';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación uno a muchos con periodos */
    public function periodos()
    {
        return $this->hasMany(Periodo::class, 'id_gestion', 'id_gestion')
            ->orderBy('posicion_ordinal', 'asc');
    }

    /** Relación uno a muchos con dimensiones */
    public function dimensiones()
    {
        return $this->hasMany(Dimension::class, 'id_gestion', 'id_gestion')
            ->orderBy('posicion_ordinal', 'asc');
    }

    /* Relación uno a muchos con mallas curriculares */
    public function mallas_curriculares()
    {
        return $this->hasMany(MallaCurricular::class, 'id_gestion', 'id_gestion');
    }

    /* Relación uno a muchos con horarios_asignaturas */
    public function horarios_asignaturas()
    {
        return $this->hasMany(HorarioAsignatura::class, 'id_gestion', 'id_gestion');
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

    public function get_all_gestiones()
    {
        return $this->with('periodos', 'dimensiones', 'creado', 'modificado', 'eliminado')
            ->orderBy('anio', 'desc')
            ->get();
    }

    public function get_gestion($id_gestion)
    {
        return $this->with('mallas_curriculares.grado', 'mallas_curriculares.materia', 'mallas_curriculares.area', 'horarios_asignaturas.nivel', 'periodos', 'dimensiones', 'creado', 'modificado', 'eliminado')
            ->findOrFail($id_gestion);
    }
}
