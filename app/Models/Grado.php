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

    /** Relación uno a muchos con cursos */
    public function cursos()
    {
        return $this->hasMany(Curso::class, 'id_grado', 'id_grado')
            ->orderBy('curso', 'asc');
    }

    /** Relación uno a muchos con mallas_curriculares */
    public function mallas_curriculares()
    {
        return $this->hasMany(MallaCurricular::class, 'id_grado', 'id_grado')
            ->orderBy('id_gestion', 'DESC')
            ->orderBy('id_materia', 'ASC');
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
        return $this::with([
            'nivel:id_nivel,nivel,posicion_ordinal,estado',
            'cursos:id_curso,id_grado,id_paralelo,curso,estado',
            'mallas_curriculares:id_malla_curricular,id_grado,id_materia,id_area,id_gestion,estado',
            'mallas_curriculares.materia:id_materia,id_campo,materia,abreviatura,posicion_ordinal,estado',
            'mallas_curriculares.area:id_area,id_campo,area,abreviatura,posicion_ordinal,estado',
            'mallas_curriculares.gestion:id_gestion,anio,estado',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])->orderBy('id_nivel', 'ASC')->orderBy('posicion_ordinal', 'ASC')->get();
    }

    public function get_grado($id_grado)
    {
        return $this::with([
            'nivel:id_nivel,nivel,posicion_ordinal,estado',
            'cursos:id_curso,id_grado,id_paralelo,curso,estado',
            'mallas_curriculares:id_malla_curricular,id_grado,id_materia,id_area,id_gestion,estado',
            'mallas_curriculares.materia:id_materia,id_campo,materia,abreviatura,posicion_ordinal,estado',
            'mallas_curriculares.area:id_area,id_campo,area,abreviatura,posicion_ordinal,estado',
            'mallas_curriculares.gestion:id_gestion,anio,estado',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])->findOrFail($id_grado);
    }
}
