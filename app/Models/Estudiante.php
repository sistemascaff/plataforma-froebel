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
        )->withPivot(['tipo', 'id_estudiante_licencia'])
            ->orderBy('fecha', 'DESC');
    }

    /** Relación uno a muchos con estudiantes_licencias */
    public function estudiantes_licencias()
    {
        return $this->hasMany(EstudianteLicencia::class, 'id_estudiante', 'id_estudiante')
            ->orderBy('fecha_inicio', 'DESC');
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
        return $this::with([
            'persona:id_persona,id_colegio,apellido_paterno,apellido_materno,nombres,documento_identificacion,documento_complemento,documento_expedido,fecha_nacimiento,sexo,idioma,celular,telefono,tipo_perfil,estado',
            'persona.usuario:id_usuario,id_persona,correo,contrasenha,url_foto_perfil,codigo_recuperacion,tiene_acceso,ultima_conexion,ultimo_dispositivo,ultima_ip,estado',

            'persona.prestamos:id_prestamo_libro,id_persona,fecha_devolucion,estado,fecha_registro',
            'persona.prestamos.libros:id_libro,codigo,titulo,estado',

            'curso:id_curso,id_grado,id_paralelo,curso,estado',

            // 1. Interceptamos la relación principal para aplicar los joins y el orden jerárquico
            'listas_asignaturas' => function ($query) {
                $query->select(
                    'listas_asignaturas.id_lista_asignatura',
                    'listas_asignaturas.id_asignatura',
                    'listas_asignaturas.id_periodo',
                    'listas_asignaturas.id_docente',
                    'listas_asignaturas.estado'
                ) // Select explícito para evitar colisiones
                    ->join('asignaturas', 'listas_asignaturas.id_asignatura', '=', 'asignaturas.id_asignatura')
                    ->join('periodos', 'listas_asignaturas.id_periodo', '=', 'periodos.id_periodo')
                    ->join('gestiones', 'periodos.id_gestion', '=', 'gestiones.id_gestion')
                    ->orderBy('gestiones.anio', 'DESC')
                    ->orderBy('periodos.posicion_ordinal', 'DESC')
                    ->orderBy('asignaturas.asignatura', 'ASC');
            },

            // 2. Cargamos el resto de sub-relaciones vinculadas a listas_asignaturas
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

            'estudiantes_licencias:id_estudiante_licencia,id_estudiante,tipo,justificacion,fecha_inicio,fecha_fin,evidencia,estado,fecha_registro',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])
            ->findOrFail($id_estudiante);
    }
}
