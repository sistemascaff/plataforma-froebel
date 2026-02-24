<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MateriaValidation extends FormRequest
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
            'materia' => [
                'required', 'string', 'min:3', 'max:60',
                Rule::unique('materias', 'materia')
                    ->where('id_campo', $this->input('id_campo'))
                    ->ignore($this->route('materia'), 'id_materia'),
            ],
            'abreviatura' => 'required|string|max:5',
            'posicion_ordinal' => 'required|integer|min:1|max:30',
            'id_campo' => 'required|exists:campos,id_campo',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'materia.required' => 'El nombre de la materia es obligatorio.',
            'materia.string' => 'El nombre de la materia debe ser una cadena de texto.',
            'materia.min' => 'El nombre de la materia debe tener al menos 3 caracteres.',
            'materia.max' => 'El nombre de la materia no debe exceder los 60 caracteres.',
            'materia.unique' => 'Este nombre de materia ya existe para el campo seleccionado.',

            'abreviatura.required' => 'La abreviatura de la materia es obligatoria.',
            'abreviatura.string' => 'La abreviatura de la materia debe ser una cadena de texto.',
            'abreviatura.max' => 'La abreviatura de la materia no debe exceder los 5 caracteres.',

            'id_campo.required' => 'El campo asociado es obligatorio.',
            'id_campo.exists' => 'El campo seleccionado no existe.',
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
            'materia' => 'materia',
            'abreviatura' => 'abreviatura',
            'id_campo' => 'campo asociado',
        ];
    }
}
