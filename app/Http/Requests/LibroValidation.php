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
}
