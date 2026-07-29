<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curso extends Model
{
    use HasFactory;

    protected $table = 'cursos';

    protected $primaryKey = 'id_curso';

    const CREATED_AT = 'fecha_registro';

    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación uno a muchos con estudiantes */
    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class, 'id_curso', 'id_curso')->where('estado', 1)
            ->whereHas('persona', function ($query) {
                $query->where('estado', 1);
            });
    }

    /** Relación FK con grados */
    public function grado()
    {
        return $this->belongsTo(Grado::class, 'id_grado', 'id_grado');
    }

    /** Relación FK con paralelos */
    public function paralelo()
    {
        return $this->belongsTo(Paralelo::class, 'id_paralelo', 'id_paralelo');
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

    public function get_all_cursos()
    {
        return $this->with([
            'grado:id_grado,id_nivel,grado,posicion_ordinal,estado',
            'paralelo:id_paralelo,paralelo,estado',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])->withCount('estudiantes')->orderBy('id_grado', 'ASC')->orderBy('id_paralelo', 'ASC')->get();
    }

    public function get_curso(int $id_curso)
    {
        return $this->with([
            'grado:id_grado,id_nivel,grado,posicion_ordinal,estado',
            'paralelo:id_paralelo,paralelo,estado',

            /* se omite datos de nacimiento del estudiante */
            'estudiantes:id_estudiante,id_persona,id_curso,salud_tipo_sangre,salud_alergias,salud_datos,estado',
            'estudiantes.persona:id_persona,id_colegio,apellido_paterno,apellido_materno,nombres,documento_identificacion,documento_complemento,documento_expedido,fecha_nacimiento,sexo,idioma,celular,telefono,tipo_perfil,estado',
            /* se omite contraseña */
            'estudiantes.persona.usuario:id_usuario,id_persona,correo,url_foto_perfil,tiene_acceso,estado',

            'creado:id_usuario,correo',
            'modificado:id_usuario,correo',
            'eliminado:id_usuario,correo'
        ])->findOrFail($id_curso);
    }
}
