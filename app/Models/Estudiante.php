<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'estudiantes';
    protected $primaryKey = 'id_estudiante';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación FK con personas */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona', 'id_persona');
    }
    /** Relación FK con cursos */
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso', 'id_curso');
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

    public function get_all_estudiantes()
    {
        return $this::with('persona', 'curso', 'creado', 'modificado', 'eliminado')->orderBy('id_estudiante', 'ASC')->get();
    }

    public function get_estudiante($id_estudiante)
    {
        return $this::with('persona', 'curso', 'creado', 'modificado', 'eliminado')->find($id_estudiante);
    }
}
