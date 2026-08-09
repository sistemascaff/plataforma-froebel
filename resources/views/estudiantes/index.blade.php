@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-user-graduate"></i> {{ $head_title }}</h1>

    <button type="button" class="btn btn-success mb-3 btn-crear" data-bs-toggle="modal" data-bs-target="#modal-formulario">
        <i class="fa-solid fa-duotone fa-plus"></i> Crear estudiante</button>

    <h2 class="text-info fw-bold">Lista de estudiantes</h2>
    
    <div class="card p-3 mb-3">
        <p>Seleccione una opción para <i class="fa-solid fa-duotone fa-file-export"></i> exportar o <i
                class="fa-solid fa-duotone fa-filter"></i> filtrar la tabla:</p>
        <div id="dataTable-export-buttons-container"></div>
    </div>

    <table class="table table-bordered table-striped" id="dataTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Foto de perfil</th>
                <th>Ap. paterno</th>
                <th>Ap. materno</th>
                <th>Nombres</th>
                <th>C.I.</th>
                <th>C.I. Complemento</th>
                <th>C.I. Expedido</th>
                <th>F. Nacimiento</th>
                <th>Sexo</th>
                <th>Idioma</th>
                <th>Celular</th>
                <th>Teléfono</th>

                <th>Tipo de perfil</th>
                <th>Correo</th>
                <th>Contraseña</th>

                <th>Tiene acceso</th>

                <th>Curso</th>
                <th>Nacimiento país</th>
                <th>Nacimiento departamento</th>
                <th>Nacimiento provincia</th>
                <th>Nacimiento localidad</th>
                <th>Salud tipo de sangre</th>
                <th>Salud alergias</th>
                <th>Salud datos méditos importantes</th>
                
                <th>Estado</th>
                <th>F. Registro</th>
                <th>F. Actualización</th>
                <th>F. Archivado</th>
                <th>Creado por</th>
                <th>Modificado por</th>
                <th>Archivado por</th>
                <th>Ip</th>
                <th>Dispositivo</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>

    <div class="mb-3"></div>

    @include('estudiantes.modal_form')
@endsection

@section('scripts')
    @include('estudiantes.index_scripts')
@endsection
