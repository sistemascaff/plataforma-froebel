@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-clipboard-list-check me-2"></i> {{ $head_title }}
        </h1>
        <a class="btn btn-info shadow-sm" href="{{ route('estudiantes_asistencias.reportes') }}">
            <i class="fa-solid fa-duotone fa-chart-column me-1"></i> Reportes
        </a>
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
                            <th>Fecha</th>
                            <th>Horario</th>
                            <th>Asignatura</th>
                            <th>Docente</th>
                            <th>Periodo</th>
                            <th class="text-center">Registrados</th>
                            <th class="text-center">Presentes</th>
                            <th class="text-center">Atrasos</th>
                            <th class="text-center">Faltas</th>
                            <th class="text-center">Licencias</th>
                            <th class="text-center">Estado</th>
                            <th>F. Registro</th>
                            <th>F. Actualización</th>
                            {{-- <th>F. Archivado</th> --}}
                            <th>Creado por</th>
                            <th>Modificado por</th>
                            {{-- <th>Archivado por</th> --}}
                            <th>Ip</th>
                            <th>Dispositivo</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('estudiantes_asistencias.index_scripts')
@endsection
