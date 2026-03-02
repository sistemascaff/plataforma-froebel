<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DimensionValidation extends FormRequest
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
            'dimension' => ['required', 'string', 'min:3', 'max:45',
                Rule::unique('dimensiones', 'dimension')
                    ->where('id_gestion', $this->input('id_gestion'))
                    ->ignore($this->route('dimension'), 'id_dimension'),
            ],
            'posicion_ordinal' => 'required|integer|min:1|max:5',
            'puntaje_maximo' => 'required|integer|min:1|max:100',
            'tipo_calculo' => 'required|string|max:20|in:sumatoria,promedio',
            'id_gestion' => 'required|exists:gestiones,id_gestion',
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
            'dimension.required' => 'El campo dimensión es obligatorio.',
            'dimension.string' => 'La dimensión debe ser un texto válido.',
            'dimension.min' => 'La dimensión debe tener al menos 3 caracteres.',
            'dimension.max' => 'La dimensión no puede tener más de 45 caracteres.',
            'dimension.unique' => 'La dimensión ya está registrado en la gestión seleccionada.',

            'posicion_ordinal.required' => 'La posición ordinal es obligatoria.',
            'posicion_ordinal.integer' => 'La posición ordinal debe ser un número entero.',
            'posicion_ordinal.min' => 'La posición ordinal debe ser al menos 1.',
            'posicion_ordinal.max' => 'La posición ordinal no puede ser mayor a 5.',

            'puntaje_maximo.required' => 'El puntaje máximo es obligatorio.',
            'puntaje_maximo.integer' => 'El puntaje máximo debe ser un número entero.',
            'puntaje_maximo.min' => 'El puntaje máximo debe ser al menos 1.',
            'puntaje_maximo.max' => 'El puntaje máximo no puede ser mayor a 100.',
            
            'tipo_calculo.required' => 'El tipo de cálculo es obligatorio.',
            'tipo_calculo.string' => 'El tipo de cálculo debe ser un texto válido.',
            'tipo_calculo.max' => 'El tipo de cálculo no puede tener más de 20 caracteres.',
            'tipo_calculo.in' => 'El tipo de cálculo debe ser "sumatoria" o "promedio".',

            'id_gestion.required' => 'Debe seleccionar una gestión.',
            'id_gestion.exists' => 'La gestión seleccionada no existe.',
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
            'dimension' => 'dimensión',
            'posicion_ordinal' => 'posición ordinal',
            'id_gestion' => 'gestión',
        ];
    }
}
