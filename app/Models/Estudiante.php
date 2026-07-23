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
        return $this::with(
            'persona.usuario',
            'curso',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        )->orderBy('id_estudiante', 'ASC')->get();
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

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        )
            ->findOrFail($id_estudiante);
    }
}
