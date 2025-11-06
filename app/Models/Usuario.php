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
    protected $primaryKey = 'idUsuario';

    const CREATED_AT = 'fechaRegistro';
    const UPDATED_AT = 'fechaActualizacion';

    /** Relación FK con empleados */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'idEmpleado', 'idEmpleado');
    }

    /** Relación con atributo de auditoría */
    public function editor(){
        return $this->belongsTo(Usuario::class, 'modificadoPor', 'idUsuario');
    }

    public function getAllUsuarios()
    {
        return Usuario::with('empleado','editor')->get();
    }
    
    public function getUsuario($idUsuario)
    {
        return Usuario::with('empleado','editor')->find($idUsuario);
    }

    /**Función utilizada para verificar y crear la sesión del Usuario.*/
    public function login($nombreUsuario)
    {
        return Usuario::where('nombreUsuario', $nombreUsuario)->first();
    }

    /**Función para destruir y cerrar la sesión.*/
    public function logout()
    {
        session()->flush();
    }
}
