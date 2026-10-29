<?php

namespace App\Http\Requests;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PersonaValidation extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // En update, el id_persona viene por la ruta
        $id_persona = $this->route('persona');
        $isUpdate   = $this->isMethod('PUT') || $this->isMethod('PATCH') || $id_persona !== null;

        // Para validar correo único en usuarios, excluyendo el registro actual si es actualización
        $correoUnico = $isUpdate && $id_persona
            ? Rule::unique('usuarios', 'correo')
            ->where('estado', 1)
            ->ignore(
                Usuario::where('id_persona', $id_persona)->value('id_usuario'),
                'id_usuario'
            )
            : Rule::unique('usuarios', 'correo')->where('estado', 1);

        return [
            // --- Persona ---
            'apellido_paterno'         => ['nullable', 'string', 'max:50', 'required_without:apellido_materno'],
            'apellido_materno'         => ['nullable', 'string', 'max:50', 'required_without:apellido_paterno'],
            'nombres'                  => ['required', 'string', 'max:50'],
            'documento_identificacion' => ['required', 'string', 'max:15'],
            'documento_complemento'    => ['nullable', 'string', 'max:10'],
            'documento_expedido'       => ['required', 'string', 'max:20'],
            'fecha_nacimiento'         => ['required', 'date', 'before:today'],
            'sexo'                     => ['required', 'string', Rule::in(['M', 'F'])],
            'idioma'                   => ['nullable', 'string', 'max:45'],
            'celular'                  => ['nullable', 'string', 'max:20'],
            'telefono'                 => ['nullable', 'string', 'max:20'],

            // Perfil específico de PersonaController (ADMINISTRADOR, GERENTE GENERAL, DIRECTOR, etc.)
            'tipo_perfil'              => ['required', 'string', 'max:50'],

            // --- Usuario ---
            'correo'                   => ['required', 'email', 'max:60', $correoUnico],
            'contrasenha'              => $isUpdate
                ? ['nullable', 'string', 'min:8', 'max:60']
                : ['required', 'string', 'min:8', 'max:60'],
            'confirmar_contrasenha'    => $isUpdate
                ? ['nullable', 'string', 'same:contrasenha']
                : ['required', 'string', 'same:contrasenha'],
            'foto_perfil'              => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            // Persona
            'apellido_paterno.required_without' => 'Debe ingresar el apellido paterno si no cuenta con apellido materno.',
            'apellido_paterno.max'              => 'El apellido paterno no puede superar los 50 caracteres.',
            'apellido_materno.required_without' => 'Debe ingresar el apellido materno si no cuenta con apellido paterno.',
            'apellido_materno.max'              => 'El apellido materno no puede superar los 50 caracteres.',
            'nombres.required'                  => 'Los nombres son obligatorios.',
            'nombres.max'                       => 'Los nombres no pueden superar los 50 caracteres.',
            'documento_identificacion.required' => 'El documento de identificación es obligatorio.',
            'documento_identificacion.max'      => 'El documento de identificación no puede superar los 15 caracteres.',
            'documento_complemento.max'         => 'El complemento no puede superar los 10 caracteres.',
            'documento_expedido.required'       => 'El lugar de expedición del documento es obligatorio.',
            'documento_expedido.max'            => 'El lugar de expedición no puede superar los 20 caracteres.',
            'fecha_nacimiento.required'         => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date'             => 'La fecha de nacimiento no tiene un formato válido.',
            'fecha_nacimiento.before'           => 'La fecha de nacimiento debe ser anterior a hoy.',
            'sexo.required'                     => 'El sexo es obligatorio.',
            'sexo.in'                           => 'El sexo debe ser M o F.',
            'celular.max'                       => 'El celular no puede superar los 20 caracteres.',
            'telefono.max'                      => 'El teléfono no puede superar los 20 caracteres.',
            'tipo_perfil.required'              => 'El tipo de perfil es obligatorio.',
            'tipo_perfil.max'                   => 'El tipo de perfil no puede superar los 50 caracteres.',

            // Usuario
            'correo.required'                   => 'El correo electrónico es obligatorio.',
            'correo.email'                      => 'El correo electrónico no tiene un formato válido.',
            'correo.max'                        => 'El correo no puede superar los 60 caracteres.',
            'correo.unique'                     => 'Este correo electrónico ya está registrado en el sistema.',
            'contrasenha.required'              => 'La contraseña es obligatoria.',
            'contrasenha.min'                   => 'La contraseña debe tener al menos 8 caracteres.',
            'contrasenha.max'                   => 'La contraseña no puede superar los 60 caracteres.',
            'confirmar_contrasenha.required'    => 'La confirmación de contraseña es obligatoria.',
            'confirmar_contrasenha.same'        => 'Las contraseñas no coinciden.',
            'foto_perfil.image'                 => 'El archivo debe ser una imagen.',
            'foto_perfil.mimes'                 => 'La imagen debe ser de tipo JPG, PNG o WEBP.',
            'foto_perfil.max'                   => 'La imagen no puede superar los 2 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'apellido_paterno'         => 'apellido paterno',
            'apellido_materno'         => 'apellido materno',
            'nombres'                  => 'nombres',
            'documento_identificacion' => 'documento de identificación',
            'documento_complemento'    => 'complemento',
            'documento_expedido'       => 'lugar de expedición',
            'fecha_nacimiento'         => 'fecha de nacimiento',
            'sexo'                     => 'sexo',
            'idioma'                   => 'idioma',
            'celular'                  => 'celular',
            'telefono'                 => 'teléfono',
            'tipo_perfil'              => 'tipo de perfil',
            'correo'                   => 'correo electrónico',
            'contrasenha'              => 'contraseña',
            'confirmar_contrasenha'    => 'confirmación de contraseña',
            'foto_perfil'              => 'foto de perfil',
        ];
    }
}
