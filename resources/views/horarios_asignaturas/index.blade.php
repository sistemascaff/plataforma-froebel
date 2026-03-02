@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-clock"></i> {{ $head_title }}</h1>

    <button type="button" class="btn btn-success mb-3 btn-crear" data-bs-toggle="modal" data-bs-target="#modal-formulario">
        <i class="fa-solid fa-duotone fa-plus"></i> Crear horario de asignatura</button>

    <h2 class="text-info fw-bold">Lista de horarios de asignaturas</h2>

    <p class="text-justify">
        
    </p>
    
    <div class="card p-3 mb-3">
        <p>Seleccione una opción para <i class="fa-solid fa-duotone fa-file-export"></i> exportar o <i
                class="fa-solid fa-duotone fa-filter"></i> filtrar la tabla:</p>
        <div id="dataTable-export-buttons-container"></div>
    </div>

    <table class="table table-bordered table-striped" id="dataTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Denominación</th>
                <th>Hora de inicio</th>
                <th>Hora de fin</th>
                <th>Nivel</th>
                <th>Gestión</th>
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

    @include('horarios_asignaturas.modal_form')
@endsection

@section('scripts')
    @include('horarios_asignaturas.index_scripts')
@endsection
