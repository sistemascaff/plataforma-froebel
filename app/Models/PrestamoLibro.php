<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

    public function get_prestamos_libros_pendientes()
    {
        $personas = $this::select(
            'personas.id_persona',
            'personas.tipo_perfil',
            'personas.apellido_paterno',
            'personas.apellido_materno',
            'personas.nombres',
            'prestamos_libros.curso',
            'prestamos_libros.celular',
            DB::raw('COUNT(detalles_prestamos_libros.id_libro) AS cantidad_adeudados')
        )
            ->join('personas', 'personas.id_persona', '=', 'prestamos_libros.id_persona')
            ->join('detalles_prestamos_libros', 'detalles_prestamos_libros.id_prestamo_libro', '=', 'prestamos_libros.id_prestamo_libro')
            ->join('libros', 'libros.id_libro', '=', 'detalles_prestamos_libros.id_libro')
            ->whereNull('detalles_prestamos_libros.fecha_retorno')
            ->where('prestamos_libros.estado', 1)
            ->groupBy('personas.id_persona')
            ->orderBy('cantidad_adeudados', 'DESC')
            ->get();

        // Añadir los libros pendientes con fechas y días de retraso
        foreach ($personas as $p) {
            $p->detalles = DB::table('detalles_prestamos_libros')
                ->join('prestamos_libros', 'prestamos_libros.id_prestamo_libro', '=', 'detalles_prestamos_libros.id_prestamo_libro')
                ->join('libros', 'libros.id_libro', '=', 'detalles_prestamos_libros.id_libro')
                ->where('prestamos_libros.id_persona', $p->id_persona)
                ->where('prestamos_libros.estado', 1)
                ->whereNull('detalles_prestamos_libros.fecha_retorno')
                ->select(
                    'libros.codigo',
                    'libros.titulo',
                    'prestamos_libros.fecha_registro as fecha_prestamo',
                    'prestamos_libros.fecha_devolucion as fecha_devolucion_teorica',
                    DB::raw('DATEDIFF(NOW(), prestamos_libros.fecha_devolucion) AS dias_retraso')
                )
                ->orderBy('prestamos_libros.fecha_registro', 'ASC')
                ->get();
        }

        return $personas;
    }

    public function get_prestamos_libros_totales_y_pendientes()
    {
        return $this::select(
            'personas.id_persona',
            'personas.tipo_perfil',
            'personas.apellido_paterno',
            'personas.apellido_materno',
            'personas.nombres',
            'prestamos_libros.curso',
            'prestamos_libros.celular',
            DB::raw('COUNT(detalles_prestamos_libros.id_libro) AS total_libros'),
            DB::raw('SUM(
                CASE 
                    WHEN detalles_prestamos_libros.fecha_retorno IS NULL 
                    THEN 1 ELSE 0 
                END
            ) AS libros_debe')
        )
            ->join('personas', 'personas.id_persona', '=', 'prestamos_libros.id_persona')
            ->join('detalles_prestamos_libros', 'detalles_prestamos_libros.id_prestamo_libro', '=', 'prestamos_libros.id_prestamo_libro')
            // Solo personas y préstamos activos
            ->where('personas.estado', 1)
            ->where('prestamos_libros.estado', 1)
            ->groupBy('personas.id_persona')
            ->orderBy('total_libros', 'DESC')
            ->get();
    }
}
