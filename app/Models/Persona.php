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

    /** Relación uno a uno con estudiantes */
    public function estudiante()
    {
        return $this->hasOne(Estudiante::class, 'id_persona', 'id_persona');
    }

    /** Relación uno a uno con usuarios */
    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'id_persona', 'id_persona');
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

    public function prestamos()
    {
        return $this->hasMany(PrestamoLibro::class, 'id_persona', 'id_persona');
    }

    public function prestamosDetalles()
    {
        return $this->hasManyThrough(
            DetallePrestamoLibro::class,
            PrestamoLibro::class,
            'id_persona',            // FK en prestamos_libros
            'id_prestamo_libro',     // FK en detalles_prestamos_libros
            'id_persona',            // PK local
            'id_prestamo_libro'      // PK en prestamos_libros
        );
    }

    public function get_all_personas()
    {
        return $this::with(
            'usuario',
            'estudiante.curso',
            'creado',
            'modificado',
            'eliminado'
        )
            ->withCount([
                // Cantidad total de libros prestados (todos los detalles)
                'prestamosDetalles as cantidad_total_prestamos',

                // Cantidad de libros que debe (fecha_retorno NULL)
                'prestamosDetalles as cantidad_libros_debe' => function ($query) {
                    $query->whereNull('fecha_retorno');
                }
            ])
            ->orderBy('id_colegio', 'ASC')
            ->get();
    }

    public function get_persona($id_persona)
    {
        return Persona::with(
            'usuario',
            'estudiante.curso',
            'creado',
            'modificado',
            'eliminado'
        )
            ->withCount([
                'prestamosDetalles as cantidad_total_prestamos',
                'prestamosDetalles as cantidad_libros_debe' => function ($query) {
                    $query->whereNull('fecha_retorno');
                }
            ])
            ->find($id_persona);
    }
}
