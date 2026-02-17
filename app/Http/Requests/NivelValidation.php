<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NivelValidation extends FormRequest
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
            'nivel' => [
                'required', 'string', 'min:3', 'max:45',
                Rule::unique('niveles', 'nivel')->ignore($this->route('nivel'), 'id_nivel'),
            ],
            'posicion_ordinal' => 'required|integer|min:1|max:5',
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
            'nivel.required' => 'El campo nivel es obligatorio.',
            'nivel.string' => 'El nivel debe ser un texto válido.',
            'nivel.min' => 'El nivel debe tener al menos 3 caracteres.',
            'nivel.max' => 'El nivel no puede tener más de 45 caracteres.',
            'nivel.unique' => 'El nivel ya está registrado.',

            'posicion_ordinal.required' => 'La posición ordinal es obligatoria.',
            'posicion_ordinal.integer' => 'La posición ordinal debe ser un número entero.',
            'posicion_ordinal.min' => 'La posición ordinal debe ser al menos 1.',
            'posicion_ordinal.max' => 'La posición ordinal no puede ser mayor a 5.',
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
            'nivel' => 'nivel',
            'posicion_ordinal' => 'posición ordinal',
        ];
    }
}
