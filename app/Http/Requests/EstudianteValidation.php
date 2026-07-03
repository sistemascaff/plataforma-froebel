<?php

namespace App\Http\Requests;

use App\Models\Estudiante;
use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EstudianteValidation extends FormRequest
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
        // En update, el id_estudiante viene por la ruta
        $id_estudiante = $this->route('estudiante');
        $isUpdate  = $this->isMethod('PUT') || $this->isMethod('PATCH') || $id_estudiante !== null;

        // Para validar correo único en usuarios, excluyendo el registro actual [cite: 83]
        $id_persona = null;
        if ($isUpdate && $id_estudiante) {
            $id_persona = Estudiante::find($id_estudiante)?->id_persona;
        }

        $correoUnico = $isUpdate && $id_persona
            ? Rule::unique('usuarios', 'correo')
            ->where('estado', 1)
            ->ignore(Usuario::where('id_persona', $id_persona)->value('id_usuario'),
                'id_usuario'
            )
            : Rule::unique('usuarios', 'correo')->where('estado', 1);

        return [
            // --- Persona ---
            'apellido_paterno'          => ['required', 'string', 'max:50'],
            'apellido_materno'          => ['required', 'string', 'max:50'], // Requerido según el formulario Blade
            'nombres'                   => ['required', 'string', 'max:50'],
            'documento_identificacion'  => ['required', 'string', 'max:15'],
            'documento_complemento'     => ['nullable', 'string', 'max:10'],
            'documento_expedido'        => ['required', 'string', 'max:20'],
            'fecha_nacimiento'          => ['required', 'date', 'before:today'],
            'sexo'                      => ['required', 'string', Rule::in(['M', 'F'])],
            'idioma'                    => ['required', 'string', 'max:45'],
            'celular'                   => ['nullable', 'string', 'max:20'],
            'telefono'                  => ['nullable', 'string', 'max:20'],

            // --- Estudiante ---
            'id_curso'                  => ['nullable', 'integer', 'exists:cursos,id_curso'],
            'nacimiento_pais'           => ['nullable', 'string', 'max:45'],
            'nacimiento_departamento'   => ['nullable', 'string', 'max:45'],
            'nacimiento_provincia'      => ['nullable', 'string', 'max:45'],
            'nacimiento_localidad'      => ['nullable', 'string', 'max:45'],
            'salud_tipo_sangre'         => ['nullable', 'string', 'max:20', Rule::in(['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'NO ESTABLECIDO'])],
            'salud_alergias'            => ['nullable', 'string', 'max:45'],
            'salud_datos'               => ['nullable', 'string'],

            // --- Usuario ---
            'correo'                    => ['required', 'email', 'max:60', $correoUnico],
            'contrasenha'               => $isUpdate
                ? ['nullable', 'string', 'min:8', 'max:60']
                : ['required', 'string', 'min:8', 'max:60'],
            'confirmar_contrasenha'     => $isUpdate
                ? ['nullable', 'string', 'same:contrasenha']
                : ['required', 'string', 'same:contrasenha'],
            'foto_perfil'               => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            // Persona
            'apellido_paterno.required'         => 'El apellido paterno es obligatorio.',
            'apellido_paterno.max'              => 'El apellido paterno no puede superar los 50 caracteres.',
            'apellido_materno.required'         => 'El apellido materno es obligatorio.',
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
            'idioma.required'                   => 'El idioma es obligatorio.',
            'idioma.max'                        => 'El idioma no puede superar los 45 caracteres.',
            'celular.max'                       => 'El celular no puede superar los 20 caracteres.',
            'telefono.max'                      => 'El teléfono no puede superar los 20 caracteres.',

            // Estudiante
            'id_curso.integer'                  => 'El curso debe ser un valor entero válido.',
            'id_curso.exists'                   => 'El curso seleccionado no existe.',
            'nacimiento_pais.max'               => 'El país de nacimiento no puede superar los 45 caracteres.',
            'nacimiento_departamento.max'       => 'El departamento de nacimiento no puede superar los 45 caracteres.',
            'nacimiento_provincia.max'          => 'La provincia de nacimiento no puede superar los 45 caracteres.',
            'nacimiento_localidad.max'          => 'La localidad de nacimiento no puede superar los 45 caracteres.',
            'salud_tipo_sangre.max'             => 'El tipo de sangre no puede superar los 20 caracteres.',
            'salud_tipo_sangre.in'              => 'El tipo de sangre debe ser uno de los valores válidos.',
            'salud_alergias.max'                => 'Las alergias no pueden superar los 45 caracteres.',

            // Usuario
            'correo.required'                   => 'El correo electrónico es obligatorio.',
            'correo.email'                      => 'El correo electrónico no tiene un formato válido.',
            'correo.max'                        => 'El correo no puede superar los 60 caracteres.',
            'correo.unique'                     => 'Este correo electrónico ya está registrado.',
            'contrasenha.required'              => 'La contraseña es obligatoria.',
            'contrasenha.min'                   => 'La contraseña debe tener al menos 8 caracteres.',
            'contrasenha.max'                   => 'La contraseña no puede superar los 60 caracteres.',
            'confirmar_contrasenha.required'    => 'La confirmación de contraseña es obligatoria.',
            'confirmar_contrasenha.same'        => 'Las contraseñas no coinciden.',
            'foto_perfil.image'                 => 'El archivo debe ser una imagen.',
            'foto_perfil.mimes'                 => 'La imagen debe ser de tipo JPG, JPEG, PNG o WEBP.',
            'foto_perfil.max'                   => 'La imagen no puede superar los 2 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'apellido_paterno'          => 'apellido paterno',
            'apellido_materno'          => 'apellido materno',
            'nombres'                   => 'nombres',
            'documento_identificacion'  => 'documento de identificación',
            'documento_complemento'     => 'complemento',
            'documento_expedido'        => 'expedido en',
            'fecha_nacimiento'          => 'fecha de nacimiento',
            'sexo'                      => 'sexo',
            'idioma'                    => 'idioma',
            'celular'                   => 'celular',
            'telefono'                  => 'teléfono',
            'id_curso'                  => 'curso',
            'nacimiento_pais'           => 'país de nacimiento',
            'nacimiento_departamento'   => 'departamento de nacimiento',
            'nacimiento_provincia'      => 'provincia de nacimiento',
            'nacimiento_localidad'      => 'localidad de nacimiento',
            'salud_tipo_sangre'         => 'tipo de sangre',
            'salud_alergias'            => 'alergias',
            'salud_datos'               => 'datos médicos importantes',
            'correo'                    => 'correo electrónico',
            'contrasenha'               => 'contraseña',
            'confirmar_contrasenha'     => 'confirmación de contraseña',
            'foto_perfil'               => 'foto de perfil',
        ];
    }
}