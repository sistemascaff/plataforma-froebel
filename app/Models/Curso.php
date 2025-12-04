<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';
    protected $primaryKey = 'id_curso';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación FK con grados */
    public function grado()
    {
        return $this->belongsTo(Grado::class, 'id_grado', 'id_grado');
    }

    /** Relación FK con paralelos */
    public function paralelo()
    {
        return $this->belongsTo(Paralelo::class, 'id_paralelo', 'id_paralelo');
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

    public function get_all_cursos()
    {
        return $this::with('grado', 'paralelo', 'creado', 'modificado', 'eliminado')
            ->orderBy('id_grado', 'ASC')->orderBy('id_paralelo', 'ASC')->get();
    }

    public function get_curso($id_curso)
    {
        return $this::with('grado', 'paralelo', 'creado', 'modificado', 'eliminado')->find($id_curso);
    }
}
