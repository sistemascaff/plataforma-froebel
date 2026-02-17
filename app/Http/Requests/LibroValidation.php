<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LibroValidation extends FormRequest
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
            'titulo' => ['required', 'string', 'max:200'],
            'codigo' => ['required', 'integer', 'min:1'],
            'autor' => ['required', 'string', 'max:100'],
            'categoria' => ['required', 'string', 'max:100'],
            'editorial' => ['required', 'string', 'max:100'],
            'presentacion' => ['required', 'string', 'max:50'],
            'anio' => ['required', 'integer', 'digits:4', 'min:1901'],
            'costo' => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'observacion' => ['nullable', 'string'],
            'descripcion' => ['nullable', 'string'],
            'adquisicion' => ['required', 'integer', 'in:1,2'],
            'fecha_ingreso_cooperativa' => ['required', 'date'],
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
            'titulo.required' => 'El título es obligatorio.',
            'titulo.string' => 'El título debe ser un texto.',
            'titulo.max' => 'El título no puede exceder los 200 caracteres.',
            
            'codigo.required' => 'El código es obligatorio.',
            'codigo.integer' => 'El código debe ser un número entero.',
            'codigo.min' => 'El código debe ser mayor o igual a 1.',
            
            'autor.required' => 'El autor es obligatorio.',
            'autor.string' => 'El autor debe ser un texto.',
            'autor.max' => 'El autor no puede exceder los 100 caracteres.',
            
            'categoria.required' => 'La categoría es obligatoria.',
            'categoria.string' => 'La categoría debe ser un texto.',
            'categoria.max' => 'La categoría no puede exceder los 100 caracteres.',
            
            'editorial.required' => 'La editorial es obligatoria.',
            'editorial.string' => 'La editorial debe ser un texto.',
            'editorial.max' => 'La editorial no puede exceder los 100 caracteres.',
            
            'presentacion.required' => 'La presentación es obligatoria.',
            'presentacion.string' => 'La presentación debe ser un texto.',
            'presentacion.max' => 'La presentación no puede exceder los 50 caracteres.',
            
            'anio.required' => 'El año es obligatorio.',
            'anio.integer' => 'El año debe ser un número entero.',
            'anio.digits' => 'El año debe tener exactamente 4 dígitos.',
            'anio.min' => 'El año debe ser mayor o igual a 1901.',
            
            'costo.required' => 'El costo es obligatorio.',
            'costo.numeric' => 'El costo debe ser un número.',
            'costo.min' => 'El costo no puede ser negativo.',
            'costo.max' => 'El costo no puede exceder 99999.99.',
            
            'observacion.string' => 'La observación debe ser un texto.',
            
            'descripcion.string' => 'La descripción debe ser un texto.',
            
            'adquisicion.required' => 'La forma de adquisición es obligatoria.',
            'adquisicion.integer' => 'La forma de adquisición debe ser un número.',
            'adquisicion.in' => 'La forma de adquisición no es válida.',
            
            'fecha_ingreso_cooperativa.required' => 'La fecha de ingreso a la cooperativa es obligatoria.',
            'fecha_ingreso_cooperativa.date' => 'La fecha de ingreso a la cooperativa debe ser una fecha válida.',
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
            'titulo' => 'título',
            'codigo' => 'código',
            'autor' => 'autor',
            'categoria' => 'categoría',
            'editorial' => 'editorial',
            'presentacion' => 'presentación',
            'anio' => 'año',
            'costo' => 'costo',
            'observacion' => 'observación',
            'descripcion' => 'descripción',
            'adquisicion' => 'forma de adquisición',
            'fecha_ingreso_cooperativa' => 'fecha de ingreso a la cooperativa',
        ];
    }
}