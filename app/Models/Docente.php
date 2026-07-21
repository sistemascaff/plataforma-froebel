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
        return $this::with('persona.usuario', 'nivel', 'coordinacion', 'creado:id_usuario,correo', 'modificado:id_usuario,correo', 'eliminado:id_usuario,correo')
            ->join('personas', 'docentes.id_persona', '=', 'personas.id_persona')
            ->orderBy('personas.apellido_paterno')
            ->orderBy('personas.apellido_materno')
            ->orderBy('personas.nombres')
            ->select('docentes.*')
            ->get();
    }

    public function get_docente($id_docente)
    {
        return $this::with('listas_asignaturas.asignatura', 'listas_asignaturas.periodo.gestion', 'persona.usuario', 'nivel', 'coordinacion', 'creado:id_usuario,correo', 'modificado:id_usuario,correo', 'eliminado:id_usuario,correo')->findOrFail($id_docente);
    }
}
