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
            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
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
            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])
            ->findOrFail($id_libro);
    }

    public function get_prestamos_libros_pendientes()
    {
        $prestamos = $this::select(
            'prestamos_libros.id_persona',
            'prestamos_libros.curso',
            'prestamos_libros.celular',
            DB::raw('COUNT(detalles_prestamos_libros.id_libro) AS cantidad_adeudados')
        )
            // Eager loading de la relación Persona seleccionando las columnas necesarias para los accesores
            ->with(['persona' => function ($q) {
                $q->select('id_persona', 'tipo_perfil', 'apellido_paterno', 'apellido_materno', 'nombres');
            }])
            ->join('detalles_prestamos_libros', 'detalles_prestamos_libros.id_prestamo_libro', '=', 'prestamos_libros.id_prestamo_libro')
            ->join('libros', 'libros.id_libro', '=', 'detalles_prestamos_libros.id_libro')
            ->whereNull('detalles_prestamos_libros.fecha_retorno')
            ->where('prestamos_libros.estado', 1)
            ->groupBy('prestamos_libros.id_persona', 'prestamos_libros.curso', 'prestamos_libros.celular')
            ->orderBy('cantidad_adeudados', 'DESC')
            ->get();

        // Añadir los detalles de los libros pendientes
        foreach ($prestamos as $p) {
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

        return $prestamos;
    }

    public function get_prestamos_libros_totales_y_pendientes()
    {
        return $this::select(
            'prestamos_libros.id_persona',
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
            // Eager loading de la relación Persona seleccionando las columnas necesarias para los accesores
            ->with(['persona' => function ($q) {
                $q->select('id_persona', 'tipo_perfil', 'apellido_paterno', 'apellido_materno', 'nombres');
            }])
            // Filtra solo personas activas (reemplaza el join con personas)
            ->whereHas('persona', function ($q) {
                $q->where('estado', 1);
            })
            ->join('detalles_prestamos_libros', 'detalles_prestamos_libros.id_prestamo_libro', '=', 'prestamos_libros.id_prestamo_libro')
            ->where('prestamos_libros.estado', 1)
            ->groupBy('prestamos_libros.id_persona', 'prestamos_libros.curso', 'prestamos_libros.celular')
            ->orderBy('total_libros', 'DESC')
            ->limit(100)
            ->get();
    }
}
