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

    /** Relación FK con empleados */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona', 'id_persona');
    }

    /** Relación con atributo de auditoría */
    public function creado(){
        return $this->belongsTo(Usuario::class, 'creado_por', 'id_usuario');
    }

    /** Relación con atributo de auditoría */
    public function modificado(){
        return $this->belongsTo(Usuario::class, 'modificado_por', 'id_usuario');
    }

    /** Relación con atributo de auditoría */
    public function eliminado(){
        return $this->belongsTo(Usuario::class, 'eliminado_por', 'id_usuario');
    }

    public function get_all_usuarios()
    {
        return Usuario::with('empleado','creado', 'modificado', 'eliminado')->get();
    }
    
    public function get_usuario($id_usuario)
    {
        return Usuario::with('empleado','creado', 'modificado', 'eliminado')->find($id_usuario);
    }

    /**Función utilizada para verificar y crear la sesión del Usuario.*/
    public function login($correo)
    {
        return Usuario::where('correo', $correo)->first();
    }

    /**Función para destruir y cerrar la sesión.*/
    public function logout()
    {
        session()->flush();
    }
}
