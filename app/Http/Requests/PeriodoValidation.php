<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PeriodoValidation extends FormRequest
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
            'periodo' => ['required','string','min:3','max:45',
                Rule::unique('periodos', 'periodo')
                    ->where('id_gestion', $this->input('id_gestion'))
                    ->ignore($this->route('periodo'), 'id_periodo'),
            ],
            'posicion_ordinal' => 'required|integer|min:1|max:4',
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
            'periodo.required' => 'El campo periodo es obligatorio.',
            'periodo.string' => 'El periodo debe ser un texto válido.',
            'periodo.min' => 'El periodo debe tener al menos 3 caracteres.',
            'periodo.max' => 'El periodo no puede tener más de 45 caracteres.',
            'periodo.unique' => 'El periodo ya está registrado en la gestión seleccionada.',

            'posicion_ordinal.required' => 'La posición ordinal es obligatoria.',
            'posicion_ordinal.integer' => 'La posición ordinal debe ser un número entero.',
            'posicion_ordinal.min' => 'La posición ordinal debe ser al menos 1.',
            'posicion_ordinal.max' => 'La posición ordinal no puede ser mayor a 4.',

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
            'periodo' => 'periodo',
            'posicion_ordinal' => 'posición ordinal',
            'id_gestion' => 'gestión',
        ];
    }
}
