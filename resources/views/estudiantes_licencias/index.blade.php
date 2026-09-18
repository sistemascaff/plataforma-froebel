@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-file-medical me-2"></i> {{ $head_title }}
        </h1>
        <button type="button" class="btn btn-success shadow-sm btn-crear" data-bs-toggle="modal"
            data-bs-target="#modal-formulario">
            <i class="fa-solid fa-duotone fa-plus me-1"></i> Registrar licencia
        </button>
    </div>

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
                            <th>Curso</th>
                            <th>Estudiante</th>
                            <th>Motivo (Tipo)</th>
                            <th>Fechas (Inicio - Fin)</th>
                            <th>Justificación</th>
                            <th>Evidencia</th>
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

    @include('estudiantes_licencias.modal_form')
@endsection

@section('scripts')
    @include('estudiantes_licencias.index_scripts')
@endsection
