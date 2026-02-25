<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MallaCurricularValidation extends FormRequest
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
            'id_grado' => 'required|exists:grados,id_grado',
            'id_materia' => 'required|exists:materias,id_materia',
            'id_area' => 'required|exists:areas,id_area',
            'id_gestion' => 'required|exists:gestiones,id_gestion',
            'id_grado' => 'unique:mallas_curriculares,id_grado,NULL,id,id_materia,' . $this->id_materia . ',id_gestion,' . $this->id_gestion,
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
            'id_grado.required' => 'El grado es obligatorio.',
            'id_grado.exists' => 'El grado seleccionado no existe.',
            'id_grado.unique' => 'Esta combinación de grado, materia y gestión ya existe.',

            'id_materia.required' => 'La materia es obligatoria.',
            'id_materia.exists' => 'La materia seleccionada no existe.',

            'id_area.required' => 'El área es obligatorio.',
            'id_area.exists' => 'El área seleccionada no existe.',

            'id_gestion.required' => 'La gestión es obligatorio.',
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
            'id_grado' => 'grado',
            'id_materia' => 'materia',
            'id_area' => 'área',
            'id_gestion' => 'gestión',
        ];
    }
}
