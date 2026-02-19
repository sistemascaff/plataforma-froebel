<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AreaValidation extends FormRequest
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
            'area' => [
                'required', 'string', 'min:3', 'max:100',
                Rule::unique('areas', 'area')->ignore($this->route('area'), 'id_area'),
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
            'area.required' => 'El nombre del área es obligatorio.',
            'area.string' => 'El nombre del área debe ser una cadena de texto.',
            'area.min' => 'El nombre del área debe tener al menos 3 caracteres.',
            'area.max' => 'El nombre del área no debe exceder los 100 caracteres.',
            'area.unique' => 'Este nombre de área ya está registrado.',

            'abreviatura.required' => 'La abreviatura del área es obligatoria.',
            'abreviatura.string' => 'La abreviatura del área debe ser una cadena de texto.',
            'abreviatura.max' => 'La abreviatura del área no debe exceder los 5 caracteres.',

            'posicion_ordinal.required' => 'La posición ordinal es obligatoria.',
            'posicion_ordinal.integer' => 'La posición ordinal debe ser un número entero.',
            'posicion_ordinal.min' => 'La posición ordinal debe ser al menos 1.',
            'posicion_ordinal.max' => 'La posición ordinal no debe exceder 30.',

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
            'area' => 'área',
            'posicion_ordinal' => 'posición ordinal',
            'id_campo' => 'campo asociado',
        ];
    }
}
