<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleEstudianteAsistencia extends Model
{
    protected $table = 'detalles_estudiantes_asistencias';

    // No tiene primary key autoincremental
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false; // No tiene created_at / updated_at

    protected $fillable = [
        'id_estudiante_asistencia',
        'id_estudiante',
        'tipo',
        'tiempo_atraso',
        'id_estudiante_licencia'
    ];

    // Relación hacia el estudiante
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante');
    }

    // Relación hacia la licencia
    public function estudiante_licencia()
    {
        return $this->belongsTo(EstudianteLicencia::class, 'id_estudiante_licencia');
    }

    // Relación hacia la asistencia
    public function estudiante_asistencia()
    {
        return $this->belongsTo(EstudianteAsistencia::class, 'id_estudiante_asistencia');
    }

    /**
     * Obtiene el reporte plano de atrasos, faltas y licencias
     * Soporta filtrado por nivel (Subdirector) y coordinación (Coordinador)
     */
    public function get_reporte_incidencias(array $filtros = [])
    {
        return $this::select('detalles_estudiantes_asistencias.*') // Previene colisión de columnas
            // Joins estratégicos
            ->join('estudiantes_asistencias', 'detalles_estudiantes_asistencias.id_estudiante_asistencia', '=', 'estudiantes_asistencias.id_estudiante_asistencia')
            ->leftJoin('horarios_asignaturas', 'estudiantes_asistencias.id_horario_asignatura', '=', 'horarios_asignaturas.id_horario_asignatura')
            ->join('estudiantes', 'detalles_estudiantes_asistencias.id_estudiante', '=', 'estudiantes.id_estudiante')
            ->join('cursos', 'estudiantes.id_curso', '=', 'cursos.id_curso')
            ->join('personas as persona_estudiante', 'estudiantes.id_persona', '=', 'persona_estudiante.id_persona')
            ->join('listas_asignaturas', 'estudiantes_asistencias.id_lista_asignatura', '=', 'listas_asignaturas.id_lista_asignatura')
            ->join('asignaturas', 'listas_asignaturas.id_asignatura', '=', 'asignaturas.id_asignatura')
            ->leftJoin('docentes', 'listas_asignaturas.id_docente', '=', 'docentes.id_docente')
            ->leftJoin('personas as persona_docente', 'docentes.id_persona', '=', 'persona_docente.id_persona')

            // Eager Loading para la vista/Excel
            ->with([
                'estudiante_asistencia.horario_asignatura',
                'estudiante.curso',
                'estudiante.persona',
                'estudiante_asistencia.lista_asignatura.asignatura',
                'estudiante_asistencia.lista_asignatura.docente.persona',
                'estudiante_licencia'
            ])
            // Solo incidencias
            ->whereIn('detalles_estudiantes_asistencias.tipo', ['A', 'F', 'L'])

            // ==========================================
            // FILTROS DINÁMICOS POR ROL
            // ==========================================
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
            ->when(
                $filtros['fecha_inicio'] ?? null,
                fn($q, $fecha) => $q->whereDate('estudiantes_asistencias.fecha', '>=', $fecha)
            )
            ->when(
                $filtros['fecha_fin'] ?? null,
                fn($q, $fecha) => $q->whereDate('estudiantes_asistencias.fecha', '<=', $fecha)
            )
            ->when(
                $filtros['id_curso'] ?? null,
                fn($q, $valor) => $q->where('cursos.id_curso', $valor)
            )

            // ORDENAMIENTO (Fecha -> Horario -> Curso -> Estudiante)
            ->orderBy('estudiantes_asistencias.fecha', 'DESC')
            ->orderBy('horarios_asignaturas.hora_inicio', 'ASC')
            ->orderBy('cursos.curso', 'ASC')
            ->orderBy('persona_estudiante.apellido_paterno', 'ASC')
            ->orderBy('persona_estudiante.apellido_materno', 'ASC')
            ->orderBy('persona_estudiante.nombres', 'ASC')
            ->get();
    }
}
