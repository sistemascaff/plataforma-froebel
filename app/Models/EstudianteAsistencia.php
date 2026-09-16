<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstudianteAsistencia extends Model
{
    use HasFactory;

    protected $table = 'estudiantes_asistencias';
    protected $primaryKey = 'id_estudiante_asistencia';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación muchos a muchos con estudiantes */
    public function estudiantes()
    {
        return $this->belongsToMany(
            Estudiante::class,
            'detalles_estudiantes_asistencias',
            'id_estudiante_asistencia',
            'id_estudiante'
        )->withPivot(['tipo', 'tiempo_atraso', 'id_estudiante_licencia']);
    }

    /** Relación FK con listas_asignaturas */
    public function lista_asignatura()
    {
        return $this->belongsTo(ListaAsignatura::class, 'id_lista_asignatura', 'id_lista_asignatura');
    }

    /** Relación FK con horarios */
    public function horario_asignatura()
    {
        return $this->belongsTo(HorarioAsignatura::class, 'id_horario_asignatura', 'id_horario_asignatura');
    }

    /** Relación uno a muchos con los detalles de asistencia */
    public function detalles_estudiantes_asistencias()
    {
        return $this->hasMany(DetalleEstudianteAsistencia::class, 'id_estudiante_asistencia', 'id_estudiante_asistencia');
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

    public function get_all_estudiantes_asistencias()
    {
        return $this::select('estudiantes_asistencias.*') // Previene colisión de columnas
            ->join('horarios_asignaturas', 'estudiantes_asistencias.id_horario_asignatura', '=', 'horarios_asignaturas.id_horario_asignatura')
            ->with([
                'lista_asignatura:id_lista_asignatura,id_asignatura,id_periodo,id_docente,estado',
                'lista_asignatura.asignatura:id_asignatura,id_materia,id_area,id_aula,id_nivel,id_coordinacion,id_curso,asignatura,tipo_calificacion,tipo_bloque,estado',
                'lista_asignatura.docente:id_docente,id_persona,estado',
                'lista_asignatura.docente.persona:id_persona,id_colegio,apellido_paterno,apellido_materno,nombres,tipo_perfil',
                'lista_asignatura.periodo:id_periodo,id_gestion,periodo,posicion_ordinal,estado',
                'lista_asignatura.periodo.gestion:id_gestion,anio,estado',
                'horario_asignatura:id_horario_asignatura,denominacion,hora_inicio,hora_fin,estado',

                'creado:id_usuario,correo',
                'modificado:id_usuario,correo',
                'eliminado:id_usuario,correo'
            ])
            ->withCount([
                // 1. Conteo total
                'detalles_estudiantes_asistencias',

                // 2. Conteo de Presentes (P)
                'detalles_estudiantes_asistencias as presentes_count' => function ($query) {
                    $query->where('tipo', 'P');
                },

                // 3. Conteo de Atrasos (A)
                'detalles_estudiantes_asistencias as atrasos_count' => function ($query) {
                    $query->where('tipo', 'A');
                },

                // 4. Conteo de Faltas (F)
                'detalles_estudiantes_asistencias as faltas_count' => function ($query) {
                    $query->where('tipo', 'F');
                },

                // 5. Conteo de Licencias (L)
                'detalles_estudiantes_asistencias as licencias_count' => function ($query) {
                    $query->where('tipo', 'L');
                }
            ])
            // Ordenamiento compuesto respetando las relaciones
            ->orderBy('estudiantes_asistencias.fecha', 'DESC')
            ->orderBy('horarios_asignaturas.hora_inicio', 'DESC')
            ->get();
    }

    public function get_estudiantes_asistencias(array $filtros = [])
    {
        return $this::select('estudiantes_asistencias.*') // Previene colisión de columnas
            ->join('horarios_asignaturas', 'estudiantes_asistencias.id_horario_asignatura', '=', 'horarios_asignaturas.id_horario_asignatura')
            ->join('listas_asignaturas', 'estudiantes_asistencias.id_lista_asignatura', '=', 'listas_asignaturas.id_lista_asignatura')
            ->join('asignaturas', 'listas_asignaturas.id_asignatura', '=', 'asignaturas.id_asignatura')
            ->with([
                'lista_asignatura:id_lista_asignatura,id_asignatura,id_periodo,id_docente,estado',
                'lista_asignatura.asignatura:id_asignatura,id_materia,id_area,id_aula,id_nivel,id_coordinacion,id_curso,asignatura,tipo_calificacion,tipo_bloque,estado',
                'lista_asignatura.docente:id_docente,id_persona,estado',
                'lista_asignatura.docente.persona:id_persona,id_colegio,apellido_paterno,apellido_materno,nombres,tipo_perfil',
                'lista_asignatura.periodo:id_periodo,id_gestion,periodo,posicion_ordinal,estado',
                'lista_asignatura.periodo.gestion:id_gestion,anio,estado',
                'horario_asignatura:id_horario_asignatura,denominacion,hora_inicio,hora_fin,estado',

                'creado:id_usuario,correo',
                'modificado:id_usuario,correo',
                'eliminado:id_usuario,correo'
            ])
            ->withCount([
                // 1. Conteo total
                'detalles_estudiantes_asistencias',

                // 2. Conteo de Presentes (P)
                'detalles_estudiantes_asistencias as presentes_count' => function ($query) {
                    $query->where('tipo', 'P');
                },

                // 3. Conteo de Atrasos (A)
                'detalles_estudiantes_asistencias as atrasos_count' => function ($query) {
                    $query->where('tipo', 'A');
                },

                // 4. Conteo de Faltas (F)
                'detalles_estudiantes_asistencias as faltas_count' => function ($query) {
                    $query->where('tipo', 'F');
                },

                // 5. Conteo de Licencias (L)
                'detalles_estudiantes_asistencias as licencias_count' => function ($query) {
                    $query->where('tipo', 'L');
                }
            ])
            ->when(
                $filtros['nivel'] ?? null,
                // Filtramos directamente gracias a los joins anidados
                fn($q, $valor) => $q->where('asignaturas.id_nivel', $valor)
            )
            ->when(
                $filtros['coordinacion'] ?? null,
                // Filtramos directamente gracias a los joins anidados
                fn($q, $valor) => $q->where('asignaturas.id_coordinacion', $valor)
            )
            ->orderBy('estudiantes_asistencias.fecha', 'DESC')
            ->orderBy('horarios_asignaturas.hora_inicio', 'ASC')
            ->get();
    }

    public function get_estudiante_asistencia($id_estudiante_asistencia)
    {
        return $this->with([
            'detalles_estudiantes_asistencias.estudiante:id_estudiante,id_persona,id_curso,estado',
            'detalles_estudiantes_asistencias.estudiante.persona:id_persona,id_colegio,apellido_paterno,apellido_materno,nombres,tipo_perfil,estado',
            'detalles_estudiantes_asistencias.estudiante.persona.usuario:id_usuario,id_persona,url_foto_perfil,estado',
            'detalles_estudiantes_asistencias.estudiante.curso:id_curso,id_grado,id_paralelo,curso,estado',
            'detalles_estudiantes_asistencias.estudiante_licencia:id_estudiante_licencia,id_estudiante,tipo,justificacion,fecha_inicio,fecha_fin,estado',

            'lista_asignatura:id_lista_asignatura,id_asignatura,id_periodo,id_docente,estado',
            'lista_asignatura.asignatura:id_asignatura,id_materia,id_area,id_aula,id_nivel,id_coordinacion,id_curso,asignatura,tipo_calificacion,tipo_bloque,estado',
            'lista_asignatura.docente:id_docente,id_persona,estado',
            'lista_asignatura.docente.persona:id_persona,id_colegio,apellido_paterno,apellido_materno,nombres,tipo_perfil',
            'lista_asignatura.periodo:id_periodo,id_gestion,periodo,posicion_ordinal,estado',
            'lista_asignatura.periodo.gestion:id_gestion,anio,estado',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])->findOrFail($id_estudiante_asistencia);
    }
}
