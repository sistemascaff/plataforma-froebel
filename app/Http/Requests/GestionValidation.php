<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GestionValidation extends FormRequest
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
            'anio' => [
                'required',
                'integer',
                'digits:4',
                'min:2000',
                'max:2050',
                Rule::unique('gestiones', 'anio')->ignore($this->route('gestion'), 'id_gestion'),
            ],
            'limite_periodos' => ['required', 'integer', 'min:1', 'max:5'],
            'limite_dimensiones' => ['required', 'integer', 'min:1', 'max:5'],
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
            'anio.required' => 'El año es obligatorio.',
            'anio.integer' => 'El año debe ser un número entero.',
            'anio.digits' => 'El año debe tener exactamente 4 dígitos.',
            'anio.min' => 'El año debe ser mayor o igual a 2000.',
            'anio.max' => 'El año debe ser menor o igual a 2050.',
            'anio.unique' => 'Este año ya está registrado.',
            'limite_periodos.required' => 'El límite de periodos es obligatorio.',
            'limite_periodos.integer' => 'El límite de periodos debe ser un número entero.',
            'limite_periodos.min' => 'El límite de periodos debe ser mayor o igual a 1.',
            'limite_periodos.max' => 'El límite de periodos debe ser menor o igual a 5.',
            'limite_dimensiones.required' => 'El límite de dimensiones es obligatorio.',
            'limite_dimensiones.integer' => 'El límite de dimensiones debe ser un número entero.',
            'limite_dimensiones.min' => 'El límite de dimensiones debe ser mayor o igual a 1.',
            'limite_dimensiones.max' => 'El límite de dimensiones debe ser menor o igual a 5.',
        ];
    }
}
