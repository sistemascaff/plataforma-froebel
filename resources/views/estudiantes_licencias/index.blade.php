@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-file-medical"></i> {{ $head_title }}</h1>

    <button type="button" class="btn btn-success mb-3 btn-crear" data-bs-toggle="modal" data-bs-target="#modal-formulario">
        <i class="fa-solid fa-duotone fa-plus"></i> Registrar licencia
    </button>

    <h2 class="text-info fw-bold">Lista de licencias estudiantiles</h2>

    <div class="card p-3 mb-3 shadow-sm">
        <p>Seleccione una opción para <i class="fa-solid fa-duotone fa-file-export"></i> exportar o <i
                class="fa-solid fa-duotone fa-filter"></i> filtrar la tabla:</p>
        <div id="dataTable-export-buttons-container"></div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="dataTable" style="width: 100%;">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Estudiante</th>
                    <th>Motivo (Tipo)</th>
                    <th>Fechas (Inicio - Fin)</th>
                    <th>Justificación</th>
                    <th>Evidencia</th>

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
    </div>

    <div class="mb-3"></div>

    @include('estudiantes_licencias.modal_form')
@endsection

@section('scripts')
    @include('estudiantes_licencias.index_scripts')
@endsection
