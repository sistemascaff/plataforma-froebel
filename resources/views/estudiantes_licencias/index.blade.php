@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-file-medical me-2"></i>{{ $head_title }}
        </h1>
        @can('create', App\Models\EstudianteLicencia::class)
            <button type="button" class="btn btn-success shadow-sm btn-crear" data-bs-toggle="modal"
            data-bs-target="#modal-formulario">
            <i class="fa-solid fa-duotone fa-plus me-1"></i> Registrar licencia
        </button>
        @endcan
    </div>

    @include('estudiantes_licencias.index_filters')

    {{-- A) TARJETAS DE MÉTRICAS --}}
    <div class="card p-3 mb-4 shadow-sm">
        <div class="row align-items-center mb-3">
            <div class="col-12">
                <h5 class="fw-bold m-0 text-uppercase tracking-wider small text-muted">
                    <i class="fa-solid fa-chart-simple me-2 text-info"></i>Estadísticas de Licencias
                </h5>
            </div>
        </div>

        <div class="row g-3">
            <!-- Total Registros -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="p-3 rounded bg-primary bg-opacity-10 text-primary me-3">
                            <i class="fa-duotone fa-file-medical fa-2xl"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-1">Total Registros</span>
                            <h3 id="kpi-total" class="fw-bold m-0 text-primary">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Licencias Programadas -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="p-3 rounded bg-info bg-opacity-10 text-info me-3">
                            <i class="fa-duotone fa-calendar-clock fa-2xl"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-1">Programadas</span>
                            <h3 id="kpi-programadas" class="fw-bold m-0 text-info">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Licencias Vigentes -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="p-3 rounded bg-success bg-opacity-10 text-success me-3">
                            <i class="fa-duotone fa-calendar-check fa-2xl"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-1">Vigentes</span>
                            <h3 id="kpi-vigentes" class="fw-bold m-0 text-success">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Licencias Finalizadas -->
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="p-3 rounded bg-danger bg-opacity-10 text-danger me-3">
                            <i class="fa-duotone fa-calendar-xmark fa-2xl"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-1">Finalizadas</span>
                            <h3 id="kpi-finalizadas" class="fw-bold m-0 text-danger">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DASHBOARD DE GRÁFICOS --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent p-3">
                    <h6 class="fw-bold m-0 text-primary"><i class="fa-duotone fa-chalkboard-user me-1"></i> Por Curso</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="chart-cursos"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-transparent p-3">
                    <h6 class="fw-bold m-0 text-primary"><i class="fa-duotone fa-tags me-1"></i> Por Motivo (Tipo)</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 300px; width: 100%;">
                        <canvas id="chart-tipos"></canvas>
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
                            <th>Curso</th>
                            <th>Estudiante</th>
                            <th>Motivo (Tipo)</th>
                            <th>Fechas (Inicio - Fin)</th>
                            <th>Justificación</th>
                            <th>Evidencia</th>
                            <th>Vigencia</th>
                            <th>Solicitado por</th>
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
