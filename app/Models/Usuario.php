<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'id_usuario';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación FK con personas */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona', 'id_persona');
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

    public function get_all_usuarios()
    {
        return $this::with('persona.docente', 'persona.estudiante', 'creado', 'modificado', 'eliminado')->get();
    }

    public function get_usuario($id_usuario)
    {
        return $this::with('persona', 'creado', 'modificado', 'eliminado')->findOrFail($id_usuario);
    }

    public function get_usuario_desde_persona($id_persona)
    {
        return $this::with('persona', 'creado', 'modificado', 'eliminado')->where('id_persona', $id_persona)->first();
    }

    /**Función utilizada para verificar y crear la sesión del Usuario.*/
    public function login($correo)
    {
        return $this::with('persona', 'creado', 'modificado', 'eliminado')->where('correo', $correo)->first();
    }
}
