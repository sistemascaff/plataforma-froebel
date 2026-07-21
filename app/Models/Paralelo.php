<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paralelo extends Model
{
    use HasFactory;

    protected $table = 'paralelos';
    protected $primaryKey = 'id_paralelo';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación uno a muchos con cursos */
    public function cursos()
    {
        return $this->hasMany(Curso::class, 'id_paralelo', 'id_paralelo');
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

    public function get_all_paralelos()
    {
        return $this::with('cursos', 'creado:id_usuario,correo', 'modificado:id_usuario,correo', 'eliminado:id_usuario,correo')->get();
    }

    public function get_paralelo($id_paralelo)
    {
        return $this::with('cursos', 'creado:id_usuario,correo', 'modificado:id_usuario,correo', 'eliminado:id_usuario,correo')->findOrFail($id_paralelo);
    }
}
