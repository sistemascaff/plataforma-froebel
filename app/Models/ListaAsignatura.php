<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListaAsignatura extends Model
{
    use HasFactory;

    protected $table = 'listas_asignaturas';
    protected $primaryKey = 'id_lista_asignatura';

    const CREATED_AT = 'fecha_registro';
    const UPDATED_AT = 'fecha_actualizacion';

    /** Relación FK con asignaturas */
    public function asignatura()
    {
        return $this->belongsTo(Asignatura::class, 'id_asignatura', 'id_asignatura');
    }

    /** Relación FK con periodos */
    public function periodo()
    {
        return $this->belongsTo(Periodo::class, 'id_periodo', 'id_periodo');
    }

    /** Relación FK con docentes */
    public function docente()
    {
        return $this->belongsTo(Docente::class, 'id_docente', 'id_docente');
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

    public function get_all_listas_asignaturas()
    {
        return $this->with('asignatura', 'periodo', 'docente', 'creado', 'modificado', 'eliminado')->get();
    }

    public function get_lista_asignatura($id_lista_asignatura)
    {
        return $this->with('asignatura', 'periodo', 'docente', 'creado', 'modificado', 'eliminado')->findOrFail($id_lista_asignatura);
    }
}
