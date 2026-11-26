<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstudianteSuspension extends Model
{
    use HasFactory;

    protected $table = 'estudiantes_suspensiones';
    protected $primaryKey = 'id_estudiante_suspension';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación FK con estudiantes */
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'id_estudiante', 'id_estudiante');
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

    public function get_all_estudiantes_suspensiones()
    {
        return $this::select('estudiantes_suspensiones.*')
            ->with([
                'estudiante:id_estudiante,id_persona,id_curso,estado',
                'estudiante.persona:id_persona,id_colegio,apellido_paterno,apellido_materno,nombres,estado',
                'estudiante.curso:id_curso,id_grado,id_paralelo,curso,estado',

                'creado:id_usuario,correo',
                'modificado:id_usuario,correo',
                'eliminado:id_usuario,correo'
            ])
            ->orderBy('estudiantes_suspensiones.id_estudiante_suspension', 'DESC')
            ->get();
    }

    public function get_estudiantes_suspensiones(array $filtros = [])
    {
        return $this::select('estudiantes_suspensiones.*')
            ->join('estudiantes', 'estudiantes_suspensiones.id_estudiante', '=', 'estudiantes.id_estudiante')
            ->join('cursos', 'estudiantes.id_curso', '=', 'cursos.id_curso')
            ->join('grados', 'cursos.id_grado', '=', 'grados.id_grado')
            ->join('niveles', 'grados.id_nivel', '=', 'niveles.id_nivel')
            ->join('paralelos', 'cursos.id_paralelo', '=', 'paralelos.id_paralelo')
            ->with([
                'estudiante:id_estudiante,id_persona,id_curso,estado',
                'estudiante.persona:id_persona,id_colegio,apellido_paterno,apellido_materno,nombres,estado',
                'estudiante.curso:id_curso,id_grado,id_paralelo,curso,estado',

                'creado:id_usuario,correo',
                'modificado:id_usuario,correo',
                'eliminado:id_usuario,correo'
            ])
            // ==========================================
            // FILTROS DE RANGO DE FECHAS
            // ==========================================
            ->when(
                $filtros['fecha_exacta'] ?? null,
                fn($q, $fecha) => $q->whereDate('estudiantes_suspensiones.fecha_registro', $fecha)
            )
            ->when(
                isset($filtros['fecha_inicio']) && isset($filtros['fecha_fin']),
                fn($q) => $q->whereBetween('estudiantes_suspensiones.fecha_registro', [
                    $filtros['fecha_inicio'],
                    $filtros['fecha_fin']
                ])
            )
            // ==========================================
            // FILTROS ACADÉMICOS Y JERÁRQUICOS
            // ==========================================
            ->when(
                $filtros['nivel'] ?? null,
                fn($q, $valor) => $q->where('niveles.id_nivel', $valor)
            )
            ->when(
                $filtros['grado'] ?? null,
                fn($q, $valor) => $q->where('grados.id_grado', $valor)
            )
            ->when(
                $filtros['curso'] ?? null,
                fn($q, $valor) => $q->where('cursos.id_curso', $valor)
            )
            ->when(
                $filtros['paralelo'] ?? null,
                fn($q, $valor) => $q->where('paralelos.id_paralelo', $valor)
            )
            ->when(
                isset($filtros['estado']),
                fn($q) => $q->where('estudiantes_suspensiones.estado', $filtros['estado'])
            )
            ->orderBy('estudiantes_suspensiones.id_estudiante_suspension', 'DESC')
            ->get();
    }

    public function get_estudiante_suspension($id_estudiante_suspension)
    {
        return $this::with([
            'estudiante:id_estudiante,id_persona,id_curso,estado',
            'estudiante.persona:id_persona,id_colegio,apellido_paterno,apellido_materno,nombres,estado',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])->findOrFail($id_estudiante_suspension);
    }
}
