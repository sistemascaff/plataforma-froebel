<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EstudianteSuspensionValidation extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_estudiante' => $this->isMethod('post')
                ? 'required|integer|exists:estudiantes,id_estudiante'
                : 'sometimes|integer|exists:estudiantes,id_estudiante',
            'motivo' => 'required|string',
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

            'motivo.required' => 'Debes describir el motivo de la suspensión.',
            'motivo.string' => 'El motivo debe ser una cadena de texto válida.',

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
            'motivo' => 'motivo',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'evidencia' => 'evidencia',
        ];
    }
}
