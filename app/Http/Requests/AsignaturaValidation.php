<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AsignaturaValidation extends FormRequest
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
            'asignatura' => ['required', 'string', 'max:100', 
                Rule::unique('asignaturas', 'asignatura')->ignore($this->route('asignatura'), 'id_asignatura'),
                ],
            'tipo_calificacion' => 'required|string|max:20|in:cualitativa,cuantitativa',
            'tipo_bloque' => 'required|string|max:20|in:curso,mixto',
            'id_materia' => 'required|integer|exists:materias,id_materia',
            'id_area' => 'required|integer|exists:areas,id_area',
            'id_aula' => 'required|integer|exists:aulas,id_aula',
            'id_nivel' => 'required|integer|exists:niveles,id_nivel',
            'id_coordinacion' => 'nullable|integer|exists:coordinaciones,id_coordinacion',
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
            'asignatura.required'       => 'El nombre de la asignatura es obligatorio.',
            'asignatura.string'         => 'El nombre de la asignatura debe ser un texto válido.',
            'asignatura.max'            => 'El nombre de la asignatura no puede superar los 100 caracteres.',
            'asignatura.unique'         => 'El nombre de la asignatura ya está registrado.',

            'tipo_calificacion.required' => 'El tipo de calificación es obligatorio.',
            'tipo_calificacion.string'   => 'El tipo de calificación debe ser un texto válido.',
            'tipo_calificacion.max'      => 'El tipo de calificación no puede superar los 20 caracteres.',
            'tipo_calificacion.in'       => 'El tipo de calificación debe ser "cualitativa" o "cuantitativa".',

            'tipo_bloque.required'      => 'El tipo de bloque es obligatorio.',
            'tipo_bloque.string'        => 'El tipo de bloque debe ser un texto válido.',
            'tipo_bloque.max'           => 'El tipo de bloque no puede superar los 20 caracteres.',
            'tipo_bloque.in'            => 'El tipo de bloque debe ser "curso" o "mixto".',

            'id_materia.required'       => 'La materia es obligatoria.',
            'id_materia.integer'        => 'La materia seleccionada no es válida.',
            'id_materia.exists'         => 'La materia seleccionada no existe en el sistema.',

            'id_area.required'          => 'El área es obligatoria.',
            'id_area.integer'           => 'El área seleccionada no es válida.',
            'id_area.exists'            => 'El área seleccionada no existe en el sistema.',

            'id_aula.required'          => 'El aula es obligatoria.',
            'id_aula.integer'           => 'El aula seleccionada no es válida.',
            'id_aula.exists'            => 'El aula seleccionada no existe en el sistema.',

            'id_nivel.required'         => 'El nivel es obligatorio.',
            'id_nivel.integer'          => 'El nivel seleccionado no es válido.',
            'id_nivel.exists'           => 'El nivel seleccionado no existe en el sistema.',

            'id_coordinacion.integer'   => 'La coordinación seleccionada no es válida.',
            'id_coordinacion.exists'    => 'La coordinación seleccionada no existe en el sistema.',
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
            'asignatura'        => 'asignatura',
            'tipo_calificacion' => 'tipo de calificación',
            'tipo_bloque'       => 'tipo de bloque',
            'id_materia'        => 'materia',
            'id_area'           => 'área',
            'id_aula'           => 'aula',
            'id_nivel'          => 'nivel',
            'id_coordinacion'   => 'coordinación',
        ];
    }
}
