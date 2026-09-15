<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListaAsignatura extends Model
{
    use HasFactory;

    protected $table = 'listas_asignaturas';
    protected $primaryKey = 'id_lista_asignatura';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación muchos a muchos con estudiantes */
    public function estudiantes()
    {
        return $this->belongsToMany(
            Estudiante::class,
            'detalles_listas_asignaturas',
            'id_lista_asignatura',
            'id_estudiante'
        );
    }

    /** Relación FK con asignaturas */
    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'id_asignatura', 'id_asignatura');
    }

    /** Relación FK con periodos */
    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'id_periodo', 'id_periodo');
    }

    /** Relación FK con docentes */
    public function docente()
    {
        return $this->belongsTo(Docente::class, 'id_docente', 'id_docente');
    }

    /** Relación con asistencias de estudiantes */
    public function estudiantes_asistencias()
    {
        return $this->hasMany(EstudianteAsistencia::class, 'id_lista_asignatura', 'id_lista_asignatura');
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

    public function get_all_listas_asignaturas()
    {
        return $this::select('listas_asignaturas.*') // Evita la colisión de columnas con las tablas unidas
            ->join('asignaturas', 'listas_asignaturas.id_asignatura', '=', 'asignaturas.id_asignatura')
            ->join('periodos', 'listas_asignaturas.id_periodo', '=', 'periodos.id_periodo')
            ->join('gestiones', 'periodos.id_gestion', '=', 'gestiones.id_gestion')
            ->with([
                'asignatura:id_asignatura,id_materia,id_area,id_aula,id_nivel,id_coordinacion,id_curso,asignatura,tipo_calificacion,tipo_bloque,estado',

                'periodo:id_periodo,id_gestion,periodo,posicion_ordinal,estado',
                'periodo.gestion:id_gestion,anio,estado',

                // Carga mínima esencial de docente y persona para evitar sobrecarga de datos y scraping.
                'docente:id_docente,id_persona,estado',
                'docente.persona:id_persona,apellido_paterno,apellido_materno,nombres,estado',

                'creado:id_usuario,correo',
                'modificado:id_usuario,correo',
                'eliminado:id_usuario,correo'
            ])
            ->orderBy('gestiones.anio', 'DESC')
            ->orderBy('asignaturas.asignatura', 'ASC')
            ->get();
    }

    public function get_listas_asignaturas(array $filtros = [])
    {
        return $this::select('listas_asignaturas.*')
            ->join('asignaturas', 'listas_asignaturas.id_asignatura', '=', 'asignaturas.id_asignatura')
            ->join('periodos', 'listas_asignaturas.id_periodo', '=', 'periodos.id_periodo')
            ->join('gestiones', 'periodos.id_gestion', '=', 'gestiones.id_gestion')
            ->with([
                'asignatura:id_asignatura,id_materia,id_area,id_aula,id_nivel,id_coordinacion,id_curso,asignatura,tipo_calificacion,tipo_bloque,estado',

                'periodo:id_periodo,id_gestion,periodo,posicion_ordinal,estado',
                'periodo.gestion:id_gestion,anio,estado',

                // Carga mínima esencial de docente y persona para evitar sobrecarga de datos y scraping.
                'docente:id_docente,id_persona,estado',
                'docente.persona:id_persona,apellido_paterno,apellido_materno,nombres,estado',

                'creado:id_usuario,correo',
                'modificado:id_usuario,correo',
                'eliminado:id_usuario,correo'
            ])
            ->when(
                $filtros['nivel'] ?? null,
                fn($q, $valor) => $q->where('asignaturas.id_nivel', $valor)
            )
            ->when(
                $filtros['coordinacion'] ?? null,
                fn($q, $valor) => $q->where('asignaturas.id_coordinacion', $valor)
            )
            ->when(
                $filtros['docente'] ?? null,
                fn($q, $valor) => $q->where('listas_asignaturas.id_docente', $valor)
            )
            ->orderBy('gestiones.anio', 'DESC')
            ->orderBy('asignaturas.asignatura', 'ASC')
            ->get();
    }

    public function get_lista_asignatura($id_lista_asignatura)
    {
        return $this->with([
            'asignatura:id_asignatura,id_materia,id_area,id_aula,id_nivel,id_coordinacion,id_curso,asignatura,tipo_calificacion,tipo_bloque,estado',

            'periodo:id_periodo,id_gestion,periodo,posicion_ordinal,estado',
            'periodo.gestion:id_gestion,anio,estado',

            'docente:id_docente,id_persona,estado',
            'docente.persona:id_persona,apellido_paterno,apellido_materno,nombres,estado',

            'estudiantes:id_estudiante,id_persona,id_curso,estado',
            'estudiantes.persona:id_persona,apellido_paterno,apellido_materno,nombres,estado',
            'estudiantes.persona.usuario:id_usuario,id_persona,correo,url_foto_perfil,estado',
            'estudiantes.curso:id_curso,curso,estado',

            // Carga de la relación con los conteos desglosados
            'estudiantes_asistencias' => function ($query) {
                $query->with('horario_asignatura:id_horario_asignatura,id_nivel,id_gestion,denominacion,hora_inicio,hora_fin,estado') // Mantenemos la carga del horario
                    ->withCount([
                        // 1. Total de estudiantes registrados (sin filtros)
                        'detalles_estudiantes_asistencias',

                        // 2. Conteo de Presentes (P)
                        'detalles_estudiantes_asistencias as presentes_count' => function ($q) {
                            $q->where('tipo', 'P');
                        },

                        // 3. Conteo de Atrasos (A)
                        'detalles_estudiantes_asistencias as atrasos_count' => function ($q) {
                            $q->where('tipo', 'A');
                        },

                        // 4. Conteo de Faltas (F)
                        'detalles_estudiantes_asistencias as faltas_count' => function ($q) {
                            $q->where('tipo', 'F');
                        },

                        // 5. Conteo de Licencias (L)
                        'detalles_estudiantes_asistencias as licencias_count' => function ($q) {
                            $q->where('tipo', 'L');
                        }
                    ])
                    ->orderBy('fecha', 'DESC'); // Ordenar del más reciente al más antiguo
            },

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])->findOrFail($id_lista_asignatura);
    }
}
