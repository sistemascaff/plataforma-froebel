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

    protected $appends = ['nombres_apellidos', 'apellidos_nombres'];

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

    /** Relación uno a uno con docentes */
    public function docente()
    {
        return $this->hasOne(Docente::class, 'id_persona', 'id_persona');
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

    // Accessor de Laravel para mostrar el nombre completo en orden nombres - apellidos.
    public function getNombresApellidosAttribute()
    {
        return trim("{$this->nombres} {$this->apellido_paterno} {$this->apellido_materno}");
    }

    public function getApellidosNombresAttribute()
    {
        return trim("{$this->apellido_paterno} {$this->apellido_materno} {$this->nombres}");
    }

    public function get_personal()
    {
        return $this::with('usuario', 'creado:id_usuario,correo', 'modificado:id_usuario,correo', 'eliminado:id_usuario,correo')
            ->whereNotIn('tipo_perfil', ['DIRECTOR DE NIVEL', 'COORDINADOR', 'DOCENTE', 'ESTUDIANTE'])
            ->get();
    }

    public function get_all_personas_biblioteca()
    {
        return $this::query()
            ->select('personas.*')
            ->leftJoin('estudiantes', 'estudiantes.id_persona', '=', 'personas.id_persona')
            ->leftJoin('cursos', 'cursos.id_curso', '=', 'estudiantes.id_curso')
            ->leftJoin('grados', 'grados.id_grado', '=', 'cursos.id_grado')
            ->leftJoin('niveles', 'niveles.id_nivel', '=', 'grados.id_nivel')

            ->with(
                'usuario',
                'estudiante.curso',
                'creado:id_usuario,correo',
                'modificado:id_usuario,correo',
                'eliminado:id_usuario,correo'
            )

            ->withCount([
                // Cantidad total de libros prestados (todos los detalles)
                'prestamosDetalles as cantidad_total_prestamos',

                // Cantidad de libros que debe (fecha_retorno NULL)
                'prestamosDetalles as cantidad_libros_debe' => function ($query) {
                    $query->whereNull('fecha_retorno');
                }
            ])

            ->orderBy('personas.id_colegio', 'ASC')
            ->orderBy('personas.tipo_perfil', 'ASC')

            // orden académico de estudiantes
            ->orderBy('niveles.posicion_ordinal', 'ASC')
            ->orderBy('grados.posicion_ordinal', 'ASC')
            ->orderBy('cursos.id_paralelo', 'ASC')

            ->orderBy('personas.apellido_paterno', 'ASC')
            ->orderBy('personas.apellido_materno', 'ASC')
            ->orderBy('personas.nombres', 'ASC')

            ->get();
    }

    public function get_persona($id_persona)
    {
        return Persona::with(
            'usuario',
            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        )
            ->withCount([
                'prestamosDetalles as cantidad_total_prestamos',
                'prestamosDetalles as cantidad_libros_debe' => function ($query) {
                    $query->whereNull('fecha_retorno');
                }
            ])
            ->findOrFail($id_persona);
    }

    public function get_all_personas_cumpleanhos(array $filtros = [])
    {
        return $this::query()
            ->select(
                'personas.id_persona',
                'personas.id_colegio',
                'personas.nombres',
                'personas.apellido_paterno',
                'personas.apellido_materno',
                'personas.tipo_perfil',
                'personas.fecha_nacimiento'
            )
            ->leftJoin('estudiantes', 'estudiantes.id_persona', '=', 'personas.id_persona')
            ->leftJoin('cursos', 'cursos.id_curso', '=', 'estudiantes.id_curso')
            ->leftJoin('grados', 'grados.id_grado', '=', 'cursos.id_grado')
            ->leftJoin('niveles', 'niveles.id_nivel', '=', 'grados.id_nivel')
            ->with([
                'estudiante:id_estudiante,id_persona,id_curso',
                'estudiante.curso:id_curso,id_grado,id_paralelo,curso',
                'estudiante.curso.grado:id_grado,id_nivel,grado,posicion_ordinal',
                'estudiante.curso.grado.nivel:id_nivel,nivel,posicion_ordinal',
            ])
            ->where('personas.estado', 1)
            ->whereNotNull('personas.fecha_nacimiento')

            // 1. Filtro estricto de inclusión por tipo de perfil 
            ->when(
                $filtros['tipo_perfil'] ?? null,
                fn($q, $perfil) => $q->where('personas.tipo_perfil', $perfil)
            )

            // 2. Filtro estricto de EXCLUSIÓN usando un array para soportar múltiples roles
            ->when(
                isset($filtros['excluir_perfiles']),
                fn($q) => $q->whereNotIn('personas.tipo_perfil', $filtros['excluir_perfiles'])
            )

            // 3. Filtro HOY
            ->when(
                isset($filtros['dia']) && isset($filtros['mes']),
                fn($q) => $q->whereMonth('personas.fecha_nacimiento', $filtros['mes'])
                    ->whereDay('personas.fecha_nacimiento', $filtros['dia'])
            )

            // 4. Filtro ESTE MES
            ->when(
                isset($filtros['solo_mes']),
                fn($q) => $q->whereMonth('personas.fecha_nacimiento', $filtros['solo_mes'])
            )

            // 5. Filtro ESTA SEMANA
            ->when(
                isset($filtros['md_inicio']) && isset($filtros['md_fin']),
                function ($q) use ($filtros) {
                    if ($filtros['md_inicio'] <= $filtros['md_fin']) {
                        $q->whereRaw("DATE_FORMAT(personas.fecha_nacimiento, '%m-%d') BETWEEN ? AND ?", [
                            $filtros['md_inicio'],
                            $filtros['md_fin']
                        ]);
                    } else {
                        $q->where(function ($sub) use ($filtros) {
                            $sub->whereRaw("DATE_FORMAT(personas.fecha_nacimiento, '%m-%d') >= ?", [$filtros['md_inicio']])
                                ->orWhereRaw("DATE_FORMAT(personas.fecha_nacimiento, '%m-%d') <= ?", [$filtros['md_fin']]);
                        });
                    }
                }
            )

            ->orderBy('personas.id_colegio', 'ASC')

            // ========================================================
            // ORDEN CRONOLÓGICO DE CUMPLEAÑOS (Mes -> Día)
            // ========================================================
            ->orderByRaw('MONTH(personas.fecha_nacimiento) ASC')
            ->orderByRaw('DAY(personas.fecha_nacimiento) ASC')

            // Criterios secundarios (perfil, curso y alfabético)
            ->orderBy('personas.tipo_perfil', 'ASC')
            ->orderBy('niveles.posicion_ordinal', 'ASC')
            ->orderBy('grados.posicion_ordinal', 'ASC')
            ->orderBy('cursos.id_paralelo', 'ASC')
            ->orderBy('personas.apellido_paterno', 'ASC')
            ->orderBy('personas.apellido_materno', 'ASC')
            ->orderBy('personas.nombres', 'ASC')
            ->get();
    }
}
