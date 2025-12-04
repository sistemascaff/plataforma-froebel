<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePrestamoLibro extends Model
{
    protected $table = 'detalles_prestamos_libros';

    // No tiene primary key autoincremental
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false; // No tiene created_at / updated_at

    protected $fillable = [
        'id_prestamo_libro',
        'id_libro',
        'fecha_retorno'
    ];

    public function prestamo()
    {
        return $this->belongsTo(PrestamoLibro::class, 'id_prestamo_libro', 'id_prestamo_libro');
    }

    public function libro()
    {
        return $this->belongsTo(Libro::class, 'id_libro', 'id_libro');
    }
}
