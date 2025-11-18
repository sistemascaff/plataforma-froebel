<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Libro extends Model
{
    use HasFactory;

    protected $table = 'libros';
    protected $primaryKey = 'id_libro';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación FK con personas */
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

    public function get_all_libros()
    {
        return Usuario::with('persona','creado', 'modificado', 'eliminado')->get();
    }
    
    public function get_libro($id_libro)
    {
        return Usuario::with('persona','creado', 'modificado', 'eliminado')->find($id_libro);
    }
}
