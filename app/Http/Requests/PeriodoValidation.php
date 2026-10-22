<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Añadimos el facade DB para consultar el año

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
            'periodo' => [
                'required',
                'string',
                'min:3',
                'max:45',
                Rule::unique('periodos', 'periodo')
                    ->where('id_gestion', $this->input('id_gestion'))
                    ->ignore($this->route('periodo'), 'id_periodo'),
            ],
            'posicion_ordinal' => 'required|integer|min:1|max:4',
            'id_gestion' => 'required|exists:gestiones,id_gestion',

            'fecha_inicio' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $idGestion = $this->input('id_gestion');
                    if ($value && $idGestion) {
                        // Obtenemos el año exacto de la gestión seleccionada
                        $anioGestion = DB::table('gestiones')
                            ->where('id_gestion', $idGestion)
                            ->value('anio');

                        if ($anioGestion) {
                            $anioInicio = Carbon::parse($value)->year;
                            if ($anioInicio != $anioGestion) {
                                $fail("El año de la fecha de inicio ({$anioInicio}) debe coincidir estrictamente con el año de la gestión seleccionada ({$anioGestion}).");
                            }
                        }
                    }
                }
            ],
            'fecha_fin' => [
                'required',
                'date',
                'after:fecha_inicio',
                function ($attribute, $value, $fail) {
                    $idGestion = $this->input('id_gestion');
                    if ($value && $idGestion) {
                        // Obtenemos el año exacto de la gestión seleccionada
                        $anioGestion = DB::table('gestiones')
                            ->where('id_gestion', $idGestion)
                            ->value('anio');

                        if ($anioGestion) {
                            $anioFin = Carbon::parse($value)->year;
                            if ($anioFin != $anioGestion) {
                                $fail("El año de la fecha de finalización ({$anioFin}) debe coincidir estrictamente con el año de la gestión seleccionada ({$anioGestion}).");
                            }
                        }
                    }
                },
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

            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio debe ser una fecha válida.',

            'fecha_fin.required' => 'La fecha de finalización es obligatoria.',
            'fecha_fin.date' => 'La fecha de finalización debe ser una fecha válida.',
            'fecha_fin.after' => 'La fecha de finalización debe ser estrictamente posterior a la fecha de inicio.',
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
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de finalización',
        ];
    }
}
