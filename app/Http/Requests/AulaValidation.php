<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AulaValidation extends FormRequest
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
            'aula' => [
                'required','string','min:3','max:65',
                Rule::unique('aulas', 'aula')->ignore($this->route('aula'), 'id_aula'),
            ],
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
            'aula.required' => 'El nombre del aula es obligatorio.',
            'aula.string' => 'El nombre del aula debe ser una cadena de texto.',
            'aula.min' => 'El nombre del aula debe tener al menos 3 caracteres.',
            'aula.max' => 'El nombre del aula no debe exceder los 65 caracteres.',
            'aula.unique' => 'Este nombre de aula ya está registrado.',
        ];
    }
}
