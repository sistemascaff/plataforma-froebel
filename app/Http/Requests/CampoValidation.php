<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CampoValidation extends FormRequest
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
            'campo' => [
                'required','string','min:3','max:45',
                Rule::unique('campos', 'campo')->ignore($this->route('campo'), 'id_campo'),
                ],
            'posicion_ordinal' => 'required|integer|min:1|max:5',
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
            'campo.required' => 'El nombre del campo es obligatorio.',
            'campo.string' => 'El nombre del campo debe ser una cadena de texto.',
            'campo.min' => 'El nombre del campo debe tener al menos 3 caracteres.',
            'campo.max' => 'El nombre del campo no debe exceder los 45 caracteres.',
            'campo.unique' => 'Este nombre de campo ya está registrado.',
            
            'posicion_ordinal.required' => 'La posición ordinal es obligatoria.',
            'posicion_ordinal.integer' => 'La posición ordinal debe ser un número entero.',
            'posicion_ordinal.min' => 'La posición ordinal debe ser al menos 1.',
            'posicion_ordinal.max' => 'La posición ordinal no debe exceder 5.',
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
            'campo' => 'campo',
            'posicion_ordinal' => 'posición ordinal',
        ];
    }
}
