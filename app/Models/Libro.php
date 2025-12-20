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

    /** Relación muchos a muchos con prestamos_libros */
    public function prestamos_libros()
    {
        return $this->belongsToMany(
            PrestamoLibro::class,           // Modelo relacionado
            'detalles_prestamos_libros',    // Tabla pivote
            'id_libro',                     // FK en la tabla pivote hacia ventas
            'id_prestamo_libro'             // FK en la tabla pivote hacia productos
        )->withPivot('fecha_retorno');      // Campos extras de la tabla pivote
    }

    /** Relación FK con colegios */
    public function colegio()
    {
        return $this->belongsTo(Colegio::class, 'id_colegio', 'id_colegio');
    }

    /** Relación FK con personas */
    public function prestado()
    {
        return $this->belongsTo(Persona::class, 'prestado_a', 'id_persona');
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

    public function get_all_libros()
    {
        return $this::with('prestamos_libros.persona', /*'colegio',*/ 'prestado.estudiante.curso', 'creado', 'modificado', 'eliminado')
            ->withCount('prestamos_libros')   // ← agrega la cantidad de préstamos
            ->orderBy('categoria', 'ASC')
            ->orderBy('codigo', 'ASC')
            ->get();
    }

    public function get_libro($id_libro)
    {
        return $this::with('prestamos_libros.persona', /*'colegio',*/ 'prestado.estudiante.curso', 'creado', 'modificado', 'eliminado')->find($id_libro);
    }

    public function get_all_libros_public()
    {
        return $this::select('id_libro', 'titulo', 'codigo', 'autor', 'categoria', 'editorial', 'presentacion', 'anio', 'estado')
            ->orderBy('titulo', 'ASC')
            ->get();
    }
}
