@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-clipboard-list-check me-2"></i>{{ $head_title }}
        </h1>
        <a class="btn btn-info text-dark shadow-sm" href="{{ route('estudiantes_asistencias.reportes') }}">
            <i class="fa-solid fa-duotone fa-chart-column me-1"></i>Reportes
        </a>
    </div>

    @include('estudiantes_asistencias.index_filters')

    <div class="row g-3 mb-4">
        <!-- Total Asistencias -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="p-3 rounded bg-primary bg-opacity-10 text-primary me-3">
                        <i class="fa-duotone fa-clipboard-list-check fa-2xl"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block mb-1">Total Asistencias</span>
                        <h3 id="kpi-total" class="fw-bold m-0 text-primary">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sin novedad (100% Presentes) -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="p-3 rounded bg-success bg-opacity-10 text-success me-3">
                        <i class="fa-duotone fa-circle-check fa-2xl"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block mb-1">Sin novedad</span>
                        <h3 id="kpi-sin-novedad" class="fw-bold m-0 text-success">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Con incidencias / Otros -->
        <div class="col-12 col-sm-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="p-3 rounded bg-secondary bg-opacity-10 text-secondary me-3">
                        <i class="fa-duotone fa-calendar-star fa-2xl"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block mb-1">Otros</span>
                        <h3 id="kpi-otros" class="fw-bold m-0 text-secondary">0</h3>
                    </div>
                </div>
            </div>
        </div>
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
                            <th class="text-center">Permisos</th>
                            <th class="text-center">Otros</th>
                            <th class="text-center">Estado</th>
                            <th>F. Registro</th>
                            <th>F. Actualización</th>
                            <th>Creado por</th>
                            <th>Modificado por</th>
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
