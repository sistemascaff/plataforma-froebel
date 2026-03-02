<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class HorarioAsignaturaValidation extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'denominacion' => 'required|string|max:45',
            'hora_inicio'  => [
                'required',
                'date_format:H:i',
                Rule::unique('horarios_asignaturas')
                    ->where('id_gestion', $this->id_gestion)
                    ->where('id_nivel', $this->id_nivel)
                    ->ignore($this->route('horario_asignatura'), 'id_horario_asignatura'),
            ],
            'hora_fin'     => 'required|date_format:H:i|after:hora_inicio',
            'id_gestion'   => 'required|exists:gestiones,id_gestion',
            'id_nivel'     => 'required|exists:niveles,id_nivel',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'denominacion.required' => 'La denominación es obligatoria.',
            'denominacion.string'   => 'La denominación debe ser un texto válido.',
            'denominacion.max'      => 'La denominación no puede superar los 45 caracteres.',

            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'La hora de inicio debe tener el formato HH:mm.',
            'hora_inicio.unique' => 'Ya existe un horario de asignaturas con la misma hora de inicio para la gestión y nivel seleccionados.',

            'hora_fin.required' => 'La hora de fin es obligatoria.',
            'hora_fin.date_format' => 'La hora de fin debe tener el formato HH:mm.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',

            'id_gestion.required' => 'La gestión es obligatoria.',
            'id_gestion.exists' => 'La gestión seleccionada no existe.',

            'id_nivel.required' => 'El nivel es obligatorio.',
            'id_nivel.exists' => 'El nivel seleccionado no existe.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'denominacion' => 'denominación',
            'hora_inicio' => 'hora de inicio',
            'hora_fin' => 'hora de fin',
            'id_gestion' => 'gestión',
            'id_nivel' => 'nivel',
        ];
    }
}
