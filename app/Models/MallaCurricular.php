<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MallaCurricular extends Model
{
    use HasFactory;

    protected $table = 'mallas_curriculares';
    protected $primaryKey = 'id_malla_curricular';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación FK con grados */
    public function grado()
    {
        return $this->belongsTo(Grado::class, 'id_grado', 'id_grado');
    }

    /** Relación FK con materias */
    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id_materia');
    }

    /** Relación FK con areas */
    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area', 'id_area');
    }

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

    public function get_all_mallas_curriculares()
    {
        return $this::with('grado', 'materia', 'area', 'gestion', 'creado:id_usuario,correo', 'modificado:id_usuario,correo', 'eliminado:id_usuario,correo')
        ->orderBy('id_gestion', 'DESC')
        ->orderBy('id_grado', 'ASC')
        ->orderBy('id_materia', 'ASC')
        ->get();
    }

    public function get_malla_curricular($id_malla_curricular)
    {
        return $this::with('grado', 'materia', 'area', 'gestion', 'creado:id_usuario,correo', 'modificado:id_usuario,correo', 'eliminado:id_usuario,correo')->findOrFail($id_malla_curricular);
    }
}
