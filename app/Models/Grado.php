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
        return $this::with('nivel', 'cursos', 'mallas_curriculares.materia', 'mallas_curriculares.area', 'mallas_curriculares.gestion', 'creado', 'modificado', 'eliminado')
            ->orderBy('id_nivel', 'ASC')->orderBy('posicion_ordinal', 'ASC')->get();
    }

    public function get_grado($id_grado)
    {
        return $this::with('nivel', 'cursos', 'mallas_curriculares.materia', 'mallas_curriculares.area', 'mallas_curriculares.gestion', 'creado', 'modificado', 'eliminado')->findOrFail($id_grado);
    }
}
