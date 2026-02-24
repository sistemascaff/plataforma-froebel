<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GradoValidation extends FormRequest
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
            'grado' => ['required', 'string', 'min:3', 'max:45',
                Rule::unique('grados', 'grado')
                    ->where('id_nivel', $this->input('id_nivel'))
                    ->ignore($this->route('grado'), 'id_grado'),
            ],
            'posicion_ordinal' => 'required|integer|min:1|max:20',
            'id_nivel' => 'required|exists:niveles,id_nivel',
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
            'grado.required' => 'El campo grado es obligatorio.',
            'grado.string' => 'El grado debe ser un texto válido.',
            'grado.min' => 'El grado debe tener al menos 3 caracteres.',
            'grado.max' => 'El grado no puede tener más de 45 caracteres.',
            'grado.unique' => 'El grado ya está registrado en el nivel seleccionado.',

            'posicion_ordinal.required' => 'La posición ordinal es obligatoria.',
            'posicion_ordinal.integer' => 'La posición ordinal debe ser un número entero.',
            'posicion_ordinal.min' => 'La posición ordinal debe ser al menos 1.',
            'posicion_ordinal.max' => 'La posición ordinal no puede ser mayor a 20.',

            'id_nivel.required' => 'Debe seleccionar un nivel.',
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
            'grado' => 'grado',
            'posicion_ordinal' => 'posición ordinal',
            'id_nivel' => 'nivel',
        ];
    }
}
