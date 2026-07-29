@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold">
        <i class="fa-solid fa-duotone fa-star me-1"></i>{{ $head_title }}
    </h1>

    <button type="button" class="btn btn-success mb-3 btn-crear" data-bs-toggle="modal" data-bs-target="#modal-formulario">
        <i class="fa-solid fa-duotone fa-plus me-1"></i>Crear asignatura
    </button>

    <h2 class="text-info fw-bold">Lista de asignaturas</h2>

    <p class="text-justify">
        Las asignaturas son la representación de las clases que los estudiantes cursan, cada asignatura está asociada a una
        <a href="{{ route('materias.index') }}">materia (interna)</a> y un <a href="{{ route('areas.index') }}">área
            (SIE)</a>, además de otros datos como el tipo de calificación, el tipo de bloque, el aula donde se imparte, el
        nivel al que pertenece y la coordinación encargada.
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
                <th>Asignatura</th>
                <th>Tipo de calificación</th>
                <th>Tipo de bloque</th>
                <th>Materia</th>
                <th>Área</th>
                <th>Aula</th>
                <th>Nivel</th>
                <th>Coordinación</th>
                <th>Curso</th>
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

    @include('asignaturas.modal_form')
@endsection

@section('scripts')
    @include('asignaturas.index_scripts')
@endsection
