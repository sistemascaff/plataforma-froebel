<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrestamoLibro extends Model
{
    use HasFactory;

    protected $table = 'prestamos_libros';
    protected $primaryKey = 'id_prestamo_libro';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación muchos a muchos con libros */
    public function libros()
    {
        return $this->belongsToMany(
            Libro::class,                       // Modelo relacionado
            'detalles_prestamos_libros',        // Tabla pivote
            'id_prestamo_libro',                // FK en la tabla pivote hacia prestamos de libros
            'id_libro'                          // FK en la tabla pivote hacia libros
        )->withPivot('fecha_retorno');          // Campos extras de la tabla pivote
    }

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

    public function get_all_prestamos_libros()
    {
        return $this::with([
            'libros' => function ($q) {
                $q->orderBy('codigo', 'ASC'); // ordenar los libros por código
            },
            'libros.prestado',
            'persona',
            'creado',
            'modificado',
            'eliminado'
        ])
            ->orderBy('id_prestamo_libro', 'DESC')
            ->get();
    }


    public function get_prestamo_libro($id_libro)
    {
        return $this::with([
            'libros' => function ($q) {
                $q->orderBy('codigo', 'ASC');
            },
            'libros.prestado',
            'persona',
            'creado',
            'modificado',
            'eliminado'
        ])
            ->find($id_libro);
    }
}
