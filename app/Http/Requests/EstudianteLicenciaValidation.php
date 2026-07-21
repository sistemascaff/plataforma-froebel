<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EstudianteLicenciaValidation extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'id_estudiante' => 'required|integer|exists:estudiantes,id_estudiante',
            'tipo' => 'required|string|in:enfermedad,consulta,representacion_institucional,tramites,motivos_familiares,motivos_religiosos,fuerza_mayor,fallecimiento_familiar,otras_causas_justificadas',
            'justificacion' => 'required|string|max:255',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'evidencia' => 'nullable|url|max:150',
        ];
    }

    public function messages(): array
    {
        return [
            'id_estudiante.required' => 'El estudiante es obligatorio.',
            'id_estudiante.exists' => 'El estudiante seleccionado no es válido en el sistema.',

            'tipo.required' => 'El motivo (tipo) de la licencia es obligatorio.',
            'tipo.in' => 'El motivo de la licencia seleccionado no pertenece al catálogo válido.',

            'justificacion.required' => 'Debes escribir una justificación para la licencia.',
            'justificacion.max' => 'La justificación es demasiado larga (máximo 255 caracteres).',

            'fecha_inicio.required' => 'La fecha y hora de inicio son obligatorias.',
            'fecha_inicio.date' => 'El formato de la fecha de inicio es inválido.',

            'fecha_fin.required' => 'La fecha y hora de fin son obligatorias.',
            'fecha_fin.date' => 'El formato de la fecha de fin es inválido.',
            'fecha_fin.after_or_equal' => 'La fecha de fin no puede ser anterior a la fecha de inicio.',

            'evidencia.url' => 'La evidencia debe ser un enlace válido (ej. URL de Google Drive).',
            'evidencia.max' => 'El enlace de la evidencia es demasiado largo (máximo 150 caracteres).',
        ];
    }

    public function attributes(): array
    {
        return [
            'id_estudiante' => 'estudiante',
            'tipo' => 'tipo',
            'justificacion' => 'justificación',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'evidencia' => 'evidencia',
        ];
    }
}
