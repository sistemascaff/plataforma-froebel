<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CoordinacionValidation extends FormRequest
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
            'coordinacion' => [
                'required', 'string', 'min:3', 'max:45',
                Rule::unique('coordinaciones', 'coordinacion')->ignore($this->route('coordinacion'), 'id_coordinacion'),
            ],
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
            'coordinacion.required' => 'El nombre de la coordinación es obligatorio.',
            'coordinacion.string' => 'La coordinación debe ser un texto válido.',
            'coordinacion.min' => 'La coordinación debe tener al menos 3 caracteres.',
            'coordinacion.max' => 'La coordinación no puede tener más de 45 caracteres.',
            'coordinacion.unique' => 'La coordinación ya está registrada.',
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
            'coordinacion' => 'coordinación',
        ];
    }
}
