<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstudianteLicencia extends Model
{
    use HasFactory;

    protected $table = 'estudiantes_licencias';
    protected $primaryKey = 'id_estudiante_licencia';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación FK con estudiantes */
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante', 'id_estudiante');
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

    public function get_all_estudiantes_licencias()
    {
        return $this::with('estudiante.persona.usuario', 'estudiante.curso', 'creado:id_usuario,correo', 'modificado:id_usuario,correo', 'eliminado:id_usuario,correo')->orderBy('id_estudiante_licencia', 'DESC')->get();
    }

    public function get_estudiante_licencia($id_estudiante_licencia)
    {
        return $this::with('estudiante.persona.usuario', 'estudiante.curso', 'creado:id_usuario,correo', 'modificado:id_usuario,correo', 'eliminado:id_usuario,correo')->findOrFail($id_estudiante_licencia);
    }
}
