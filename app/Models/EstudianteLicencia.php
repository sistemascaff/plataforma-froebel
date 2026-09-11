<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstudianteLicencia extends Model
{
    use HasFactory;

    protected $table = 'estudiantes_licencias';
    protected $primaryKey = 'id_estudiante_licencia';

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

    public function get_all_estudiantes_licencias()
    {
        return $this::select('estudiantes_licencias.*') // Evita colisión de columnas
            ->with([
                'estudiante:id_estudiante,id_persona,id_curso,estado',
                'estudiante.persona:id_persona,id_colegio,apellido_paterno,apellido_materno,nombres,estado',
                'estudiante.curso:id_curso,id_grado,id_paralelo,curso,estado',

                'creado:id_usuario,correo',
                'modificado:id_usuario,correo',
                'eliminado:id_usuario,correo'
            ])
            // Especificamos la tabla para evitar ambigüedad en consultas futuras
            ->orderBy('estudiantes_licencias.id_estudiante_licencia', 'DESC')
            ->get();
    }

    public function get_estudiantes_licencias(array $filtros = [])
    {
        return $this::select('estudiantes_licencias.*') // Fundamental al usar joins
            // Encadenamiento de joins para llegar a la tabla niveles
            ->join('estudiantes', 'estudiantes_licencias.id_estudiante', '=', 'estudiantes.id_estudiante')
            ->join('cursos', 'estudiantes.id_curso', '=', 'cursos.id_curso')
            ->join('grados', 'cursos.id_grado', '=', 'grados.id_grado')
            ->join('niveles', 'grados.id_nivel', '=', 'niveles.id_nivel')
            ->with([
                'estudiante:id_estudiante,id_persona,id_curso,estado',
                'estudiante.persona:id_persona,id_colegio,apellido_paterno,apellido_materno,nombres,estado',
                'estudiante.curso:id_curso,id_grado,id_paralelo,curso,estado',

                'creado:id_usuario,correo',
                'modificado:id_usuario,correo',
                'eliminado:id_usuario,correo'
            ])
            ->when(
                $filtros['nivel'] ?? null,
                // Filtramos directamente gracias a los joins
                fn($q, $valor) => $q->where('niveles.id_nivel', $valor)
            )
            ->orderBy('estudiantes_licencias.id_estudiante_licencia', 'DESC')
            ->get();
    }


    public function get_estudiante_licencia($id_estudiante_licencia)
    {
        return $this::with([
            'estudiante:id_estudiante,id_persona,id_curso,estado',
            'estudiante.persona:id_persona,id_colegio,apellido_paterno,apellido_materno,nombres,estado',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])->findOrFail($id_estudiante_licencia);
    }
}
