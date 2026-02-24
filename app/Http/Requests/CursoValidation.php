<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CursoValidation extends FormRequest
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
            'curso' => ['required', 'string', 'min:3', 'max:45',
                Rule::unique('cursos', 'curso')
                    ->ignore($this->route('curso'), 'id_curso'),
            ],
            'id_grado' => ['required', 'exists:grados,id_grado',
                Rule::unique('cursos', 'id_grado')
                    ->where('id_paralelo', $this->input('id_paralelo'))
                    ->ignore($this->route('curso'), 'id_curso'),
            ],
            'id_paralelo' => 'required|exists:paralelos,id_paralelo',
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
            'curso.required' => 'El nombre del curso es obligatorio.',
            'curso.string'   => 'El nombre del curso debe ser texto.',
            'curso.min'      => 'El nombre del curso debe tener al menos 3 caracteres.',
            'curso.max'      => 'El nombre del curso no debe superar los 45 caracteres.',
            'curso.unique'   => 'Este curso ya existe.',

            'id_grado.required'    => 'El grado es obligatorio.',
            'id_grado.exists'      => 'El grado seleccionado no existe.',
            'id_grado.unique'      => 'Ya existe un curso con el mismo grado y paralelo.',

            'id_paralelo.required' => 'El paralelo es obligatorio.',
            'id_paralelo.exists'   => 'El paralelo seleccionado no existe.',
        ];
    }
}
