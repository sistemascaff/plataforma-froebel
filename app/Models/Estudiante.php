<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'estudiantes';
    protected $primaryKey = 'id_estudiante';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación muchos a muchos con listas_asignaturas */
    public function listas_asignaturas()
    {
        return $this->belongsToMany(
            ListaAsignatura::class,
            'detalles_listas_asignaturas',
            'id_estudiante',
            'id_lista_asignatura'
        );
    }

    /** Relación muchos a muchos con estudiantes_asistencias */
    public function estudiantes_asistencias()
    {
        return $this->belongsToMany(
            EstudianteAsistencia::class,
            'detalles_estudiantes_asistencias', // Nombre de la tabla pivote
            'id_estudiante',                    // FK de este modelo (Estudiante) en la tabla pivote
            'id_estudiante_asistencia'          // FK del modelo relacionado en la tabla pivote
        )->withPivot(['tipo', 'id_estudiante_licencia']);
    }

    /** Relación FK con personas */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona', 'id_persona');
    }
    /** Relación FK con cursos */
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'id_curso', 'id_curso');
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

    public function get_all_estudiantes()
    {
        return $this::select('estudiantes.*') // Evita colisión de columnas
            ->join('personas', 'estudiantes.id_persona', '=', 'personas.id_persona')
            ->join('cursos', 'estudiantes.id_curso', '=', 'cursos.id_curso')
            ->join('grados', 'cursos.id_grado', '=', 'grados.id_grado')
            ->join('niveles', 'grados.id_nivel', '=', 'niveles.id_nivel')
            ->with([
                'persona.usuario',
                'curso',
                'creado:id_usuario,correo',
                'modificado:id_usuario,correo',
                'eliminado:id_usuario,correo'
            ])
            ->orderBy('niveles.posicion_ordinal', 'ASC')
            ->orderBy('grados.posicion_ordinal', 'ASC')
            ->orderBy('cursos.curso', 'ASC')
            ->orderBy('personas.apellido_paterno', 'ASC')
            ->orderBy('personas.nombres', 'ASC')
            ->get();
    }

    public function get_estudiantes(array $filtros = [])
    {
        return $this::select('estudiantes.*')
            ->join('personas', 'estudiantes.id_persona', '=', 'personas.id_persona')
            ->join('cursos', 'estudiantes.id_curso', '=', 'cursos.id_curso')
            ->join('grados', 'cursos.id_grado', '=', 'grados.id_grado')
            ->join('niveles', 'grados.id_nivel', '=', 'niveles.id_nivel')
            ->with([
                'persona.usuario',
                'curso:id_curso,id_grado,id_paralelo,curso,estado',
                'creado:id_usuario,correo',
                'modificado:id_usuario,correo',
                'eliminado:id_usuario,correo'
            ])
            ->when(
                $filtros['nivel'] ?? null,
                // Como ya hicimos el join con niveles, filtramos directamente en lugar de usar whereHas (es más rápido)
                fn($q, $valor) => $q->where('niveles.id_nivel', $valor)
            )
            ->orderBy('niveles.posicion_ordinal', 'ASC')
            ->orderBy('grados.posicion_ordinal', 'ASC')
            ->orderBy('cursos.curso', 'ASC')
            ->orderBy('personas.apellido_paterno', 'ASC')
            ->orderBy('personas.nombres', 'ASC')
            ->get();
    }

    public function get_estudiante($id_estudiante)
    {
        return $this::with(
            'persona:id_persona,id_colegio,apellido_paterno,apellido_materno,nombres,documento_identificacion,documento_complemento,documento_expedido,fecha_nacimiento,sexo,idioma,celular,telefono,tipo_perfil,estado',
            'persona.usuario:id_usuario,id_persona,correo,contrasenha,url_foto_perfil,codigo_recuperacion,tiene_acceso,ultima_conexion,ultimo_dispositivo,ultima_ip,estado',

            'curso:id_curso,id_grado,id_paralelo,curso,estado',

            'listas_asignaturas:id_lista_asignatura,id_asignatura,id_periodo,id_docente,estado',
            'listas_asignaturas.asignatura:id_asignatura,id_materia,id_area,id_aula,id_nivel,id_coordinacion,id_curso,asignatura,tipo_calificacion,tipo_bloque,estado',
            'listas_asignaturas.periodo:id_periodo,id_gestion,periodo,posicion_ordinal,estado',
            'listas_asignaturas.periodo.gestion:id_gestion,anio,estado',
            // para la vista de detalles del estudiante se omite información sensible del docente
            'listas_asignaturas.docente:id_docente,id_persona,id_nivel,id_coordinacion,estado',
            'listas_asignaturas.docente.persona:id_persona,id_colegio,apellido_paterno,apellido_materno,nombres',

            'estudiantes_asistencias:id_estudiante_asistencia,id_lista_asignatura,id_horario_asignatura,fecha,estado',
            'estudiantes_asistencias.lista_asignatura:id_lista_asignatura,id_asignatura,id_periodo,id_docente,estado',
            'estudiantes_asistencias.lista_asignatura.asignatura:id_asignatura,id_materia,id_area,id_aula,id_nivel,id_coordinacion,id_curso,asignatura,tipo_calificacion,tipo_bloque,estado',
            'estudiantes_asistencias.horario_asignatura',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        )
            ->findOrFail($id_estudiante);
    }
}
