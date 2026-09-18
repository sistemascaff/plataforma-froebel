@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-book-reader me-2"></i> {{ $head_title }}
        </h1>
        <button type="button" class="btn btn-success shadow-sm btn-crear" data-bs-toggle="modal"
            data-bs-target="#modal-formulario">
            <i class="fa-solid fa-duotone fa-plus me-1"></i> Crear asignatura
        </button>
    </div>

    <p class="text-justify mb-4">
        Las asignaturas son la representación de las clases que los estudiantes cursan, cada asignatura está asociada a una
        <a href="{{ route('materias.index') }}">materia (interna)</a> y un <a href="{{ route('areas.index') }}">área
            (SIE)</a>, además de otros datos como el tipo de calificación, el tipo de bloque, el aula donde se imparte, el
        nivel al que pertenece y la coordinación encargada.
    </p>

    <div class="card shadow-sm mb-4">
        <div class="card-header p-3 d-flex justify-content-between align-items-center">
            <p class="mb-0">Seleccione una opción para <i class="fa-solid fa-duotone fa-file-export mx-1"></i> exportar o
                <i class="fa-solid fa-duotone fa-filter mx-1"></i> filtrar la tabla:
            </p>
            <div id="dataTable-export-buttons-container"></div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped w-100" id="dataTable">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Asignatura</th>
                            <th>Tipo de calificación</th>
                            <th>Tipo de bloque</th>
                            <th>Materia</th>
                            <th>Área</th>
                            <th>Aula</th>
                            <th>Nivel</th>
                            <th>Coordinación</th>
                            <th>Curso</th>
                            <th class="text-center">Estado</th>
                            <th>F. Registro</th>
                            <th>F. Actualización</th>
                            <th>F. Archivado</th>
                            <th>Creado por</th>
                            <th>Modificado por</th>
                            <th>Archivado por</th>
                            <th>Ip</th>
                            <th>Dispositivo</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('asignaturas.modal_form')
@endsection

@section('scripts')
    @include('asignaturas.index_scripts')
@endsection
