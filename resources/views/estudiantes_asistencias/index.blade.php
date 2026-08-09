@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold">
        <i class="fa-solid fa-duotone fa-clipboard-list-check me-1"></i>{{ $head_title }}
    </h1>

    <h2 class="text-info fw-bold">Lista de asistencias</h2>

    <div class="card p-3 mb-3">
        <p>Seleccione una opción para <i class="fa-solid fa-duotone fa-file-export"></i> exportar o <i
                class="fa-solid fa-duotone fa-filter"></i> filtrar la tabla:</p>
        <div id="dataTable-export-buttons-container"></div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="dataTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fecha</th>
                    <th>Horario</th>
                    <th>Asignatura</th>
                    <th>Docente</th>
                    <th>Periodo</th>
                    <th>Registrados</th>
                    <th>Presentes</th>
                    <th>Atrasos</th>
                    <th>Faltas</th>
                    <th>Licencias</th>
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
@endsection

@section('scripts')
    @include('estudiantes_asistencias.index_scripts')
@endsection
