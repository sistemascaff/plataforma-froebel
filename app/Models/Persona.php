<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'personas';
    protected $primaryKey = 'id_persona';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';
    
    /** Relación FK con colegios */
    public function colegio()
    {
        return $this->belongsTo(Colegio::class, 'id_colegio', 'id_colegio');
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
    
    public function get_all_personas()
    {
        return Persona::with('creado', 'modificado', 'eliminado')->orderBy('id_colegio','ASC')->get();
    }
    
    public function get_persona($id_colegio)
    {
        return Persona::with('creado', 'modificado', 'eliminado')->find($id_colegio);
    }
}