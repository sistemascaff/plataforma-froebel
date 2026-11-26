@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h1 class="text-info fw-bold mb-0 me-auto">
            <i class="fa-solid fa-duotone fa-chart-pie me-2"></i>Reportes y Métricas de Asistencias
        </h1>
        <a class="btn btn-secondary shadow-sm" href="{{ route('estudiantes_asistencias.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left me-1"></i>Volver
        </a>
    </div>

    <div class="alert alert-info border-info border-start border-4 shadow-sm mb-4 d-flex align-items-center" role="alert">
        <i class="fa-solid fa-duotone fa-circle-info fa-2x me-3"></i>
        <div>
            <h5 class="alert-heading fw-bold mb-1">Información del Reporte</h5>
            <p class="mb-0">
                Por defecto, se muestra la información de <b>hoy</b>. Al aplicar los filtros,
                tanto las tablas como los gráficos y tarjetas se recalcularán automáticamente en tiempo real.
            </p>
        </div>
    </div>

    {{-- PANEL DE FILTROS DINÁMICOS --}}
    @include('estudiantes_asistencias.reportes_filters')

    {{-- A) TARJETAS DE MÉTRICAS --}}
    <div class="card p-3 mb-4 shadow-sm">
        <div class="row align-items-center mb-3">
            <div class="col-12">
                <h5 class="fw-bold m-0 text-uppercase tracking-wider small text-muted">
                    <i class="fa-solid fa-chart-simple me-2 text-info"></i>Estadísticas de Incidencias
                </h5>
            </div>
        </div>

        <div class="row g-3">
            <!-- Total Incidencias -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="p-3 rounded bg-secondary bg-opacity-10 me-3">
                            <i class="fa-duotone fa-sigma fa-2xl"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-1">Total Incidencias</span>
                            <h3 id="kpi-total" class="fw-bold m-0 text-secondary">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Atrasos -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="p-3 rounded bg-warning bg-opacity-10 text-warning me-3">
                            <i class="fa-duotone fa-hourglass-half fa-2xl"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-1">Atrasos</span>
                            <h3 id="kpi-atrasos" class="fw-bold m-0 text-warning">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Faltas -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="p-3 rounded bg-danger bg-opacity-10 text-danger me-3">
                            <i class="fa-duotone fa-user-xmark fa-2xl"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-1">Faltas</span>
                            <h3 id="kpi-faltas" class="fw-bold m-0 text-danger">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Licencias -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="p-3 rounded bg-info bg-opacity-10 text-info me-3">
                            <i class="fa-duotone fa-file-certificate fa-2xl"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-1">Licencias</span>
                            <h3 id="kpi-licencias" class="fw-bold m-0 text-info">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permisos -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="p-3 rounded bg-primary bg-opacity-10 text-primary me-3">
                            <i class="fa-duotone fa-file-signature fa-2xl"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-1">Permisos</span>
                            <h3 id="kpi-permisos" class="fw-bold m-0 text-primary">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Suspendidos -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="p-3 rounded bg-dark bg-opacity-10 text-danger border border-danger me-3">
                            <i class="fa-duotone fa-user-slash fa-2xl"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-1">Suspendidos</span>
                            <h3 id="kpi-suspendidos" class="fw-bold m-0 text-danger">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Atrasos (Tiempos) -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="p-3 rounded bg-secondary bg-opacity-10 me-3">
                            <i class="fa-duotone fa-clock-rotate-left fa-2xl"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-1">Atrasos (Mins)</span>
                            <h4 class="fw-bold m-0">
                                <span id="kpi-minutos-total" class="text-danger">0</span> <span
                                    class="fs-6 text-muted fw-normal">min</span>
                            </h4>
                            <span class="fs-7 text-muted d-block">x̄: <span id="kpi-minutos-promedio">0</span> min</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DASHBOARD DE GRÁFICOS (TABS) --}}
    <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold text-info" data-bs-toggle="tab" data-bs-target="#tab-proporcion"
                type="button" role="tab">
                <i class="fa-solid fa-chart-pie"></i> General
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-info" data-bs-toggle="tab" data-bs-target="#tab-fechas" type="button"
                role="tab">
                <i class="fa-solid fa-chart-line"></i> Fechas
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-info" data-bs-toggle="tab" data-bs-target="#tab-horarios"
                type="button" role="tab">
                <i class="fa-solid fa-chart-column"></i> Horarios
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-info" data-bs-toggle="tab" data-bs-target="#tab-ranking" type="button"
                role="tab">
                <i class="fa-solid fa-user-graduate"></i> Estudiantes
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-info" data-bs-toggle="tab" data-bs-target="#tab-cursos" type="button"
                role="tab">
                <i class="fa-solid fa-chalkboard-user"></i> Cursos
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-info" data-bs-toggle="tab" data-bs-target="#tab-asignaturas"
                type="button" role="tab">
                <i class="fa-solid fa-books"></i> Asignaturas
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-info" data-bs-toggle="tab" data-bs-target="#tab-docentes"
                type="button" role="tab">
                <i class="fa-solid fa-user-tie"></i> Docentes
            </button>
        </li>
    </ul>

    <div class="tab-content border border-top-0 p-4 mb-4 shadow-sm rounded-bottom bg-body" id="dashboardTabsContent">
        <div class="tab-pane fade show active" id="tab-proporcion" role="tabpanel">
            <div style="position: relative; height: 350px; width: 100%;">
                <canvas id="chart-proporcion"></canvas>
            </div>
        </div>
        <div class="tab-pane fade" id="tab-fechas" role="tabpanel">
            <div style="position: relative; height: 350px; width: 100%;">
                <canvas id="chart-fechas"></canvas>
            </div>
        </div>
        <div class="tab-pane fade" id="tab-horarios" role="tabpanel">
            <div style="position: relative; height: 350px; width: 100%;">
                <canvas id="chart-horarios"></canvas>
            </div>
        </div>
        <div class="tab-pane fade" id="tab-ranking" role="tabpanel">
            <div style="position: relative; height: 350px; width: 100%;">
                <canvas id="chart-ranking"></canvas>
            </div>
        </div>
        <div class="tab-pane fade" id="tab-cursos" role="tabpanel">
            <div style="position: relative; height: 350px; width: 100%;">
                <canvas id="chart-cursos"></canvas>
            </div>
        </div>
        <div class="tab-pane fade" id="tab-asignaturas" role="tabpanel">
            <div style="position: relative; height: 350px; width: 100%;">
                <canvas id="chart-asignaturas"></canvas>
            </div>
        </div>
        <div class="tab-pane fade" id="tab-docentes" role="tabpanel">
            <div style="position: relative; height: 350px; width: 100%;">
                <canvas id="chart-docentes"></canvas>
            </div>
        </div>
    </div>

    {{-- TABLAS DE RESULTADOS (TABS) --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header pb-0 border-bottom-0">
            <ul class="nav nav-tabs card-header-tabs" id="tablasTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold text-info" id="btn-tab-incidencias" data-bs-toggle="tab"
                        data-bs-target="#tab-tabla-incidencias" type="button" role="tab">
                        <i class="fa-solid fa-duotone fa-calendar-lines me-1"></i>
                        Detalle de incidencias
                        <span class="badge bg-secondary ms-1" id="badge-count-incidencias">0</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold text-info" id="btn-tab-ranking" data-bs-toggle="tab"
                        data-bs-target="#tab-tabla-ranking" type="button" role="tab">
                        <i class="fa-solid fa-duotone fa-ranking-star me-1"></i>
                        Ranking por estudiante
                        <span class="badge bg-info text-dark ms-1" id="badge-count-ranking">0</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="tablasTabsContent">
                {{-- TAB 1: REPORTE DETALLADO DE INCIDENCIAS --}}
                <div class="tab-pane fade show active" id="tab-tabla-incidencias" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped w-100" id="dataTable">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">#</th>
                                    <th>Fecha</th>
                                    <th>Horario</th>
                                    <th>Curso</th>
                                    <th>Estudiante</th>
                                    <th>Asignatura</th>
                                    <th>Docente</th>
                                    <th class="text-center">Estado</th>
                                    <th class="text-center">Atraso</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                {{-- TAB 2: RANKING POR ESTUDIANTE --}}
                <div class="tab-pane fade" id="tab-tabla-ranking" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped w-100" id="table-ranking-estudiantes">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 4%;">#</th>
                                    <th>Curso</th>
                                    <th>Estudiante</th>
                                    <th class="text-center">Atrasos</th>
                                    <th class="text-center">Faltas</th>
                                    <th class="text-center">Suspendidos</th>
                                    <th class="text-center table-danger text-danger"
                                        title="Subtotal: Atrasos + Faltas + Suspendidos">
                                        Disciplinarias
                                    </th>
                                    <th class="text-center">Licencias</th>
                                    <th class="text-center">Permisos</th>
                                    <th class="text-center table-info text-info" title="Subtotal: Licencias + Permisos">
                                        Justificadas
                                    </th>
                                    <th class="text-center">Atrasos (Prom.)</th>
                                    <th class="text-center table-secondary">Total</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('estudiantes_asistencias.reportes_scripts')
@endsection
