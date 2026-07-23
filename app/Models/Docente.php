<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    use HasFactory;

    protected $table = 'docentes';
    protected $primaryKey = 'id_docente';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación uno a muchos con listas_asignaturas */
    public function listas_asignaturas()
    {
        return $this->hasMany(ListaAsignatura::class, 'id_docente', 'id_docente');
    }

    /** Relación FK con personas */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona', 'id_persona');
    }

    /** Relación FK con niveles */
    public function nivel()
    {
        return $this->belongsTo(Nivel::class, 'id_nivel', 'id_nivel');
    }

    /** Relación FK con coordinaciones */
    public function coordinacion()
    {
        return $this->belongsTo(Coordinacion::class, 'id_coordinacion', 'id_coordinacion');
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

    public function get_all_docentes()
    {
        return $this::with([
            'persona:id_persona,id_colegio,apellido_paterno,apellido_materno,nombres,documento_identificacion,documento_complemento,documento_expedido,fecha_nacimiento,sexo,idioma,celular,telefono,tipo_perfil,estado',
            'persona.usuario:id_usuario,id_persona,correo,url_foto_perfil,tiene_acceso,ultima_conexion,ultimo_dispositivo,ultima_ip,estado',

            'nivel:id_nivel,nivel,posicion_ordinal,estado',
            'coordinacion:id_coordinacion,coordinacion,estado',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])
            ->join('personas', 'docentes.id_persona', '=', 'personas.id_persona')
            ->orderBy('personas.apellido_paterno')
            ->orderBy('personas.apellido_materno')
            ->orderBy('personas.nombres')
            ->select('docentes.*')
            ->get();
    }

    public function get_docente($id_docente)
    {
        return $this::with([
            'listas_asignaturas:id_lista_asignatura,id_asignatura,id_periodo,id_docente,estado',
            'listas_asignaturas.asignatura:id_asignatura,id_materia,id_area,id_aula,id_nivel,id_coordinacion,id_curso,asignatura,tipo_calificacion,tipo_bloque,estado',
            'listas_asignaturas.periodo:id_periodo,id_gestion,periodo,posicion_ordinal,estado',
            'listas_asignaturas.periodo.gestion:id_gestion,anio,estado',

            'persona:id_persona,id_colegio,apellido_paterno,apellido_materno,nombres,documento_identificacion,documento_complemento,documento_expedido,fecha_nacimiento,sexo,idioma,celular,telefono,tipo_perfil,estado',
            'persona.usuario:id_usuario,id_persona,correo,contrasenha,url_foto_perfil,codigo_recuperacion,tiene_acceso,ultima_conexion,ultimo_dispositivo,ultima_ip,estado',

            'nivel:id_nivel,nivel,posicion_ordinal,estado',
            'coordinacion:id_coordinacion,coordinacion,estado',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])->findOrFail($id_docente);
    }
}
