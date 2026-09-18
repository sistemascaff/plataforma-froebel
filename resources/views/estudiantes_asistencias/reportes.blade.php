@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h1 class="text-info fw-bold mb-0 me-auto">
            <i class="fa-solid fa-duotone fa-chart-pie me-2"></i> Reportes y Métricas de Asistencias
        </h1>
        <a class="btn btn-secondary shadow-sm" href="{{ route('estudiantes_asistencias.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="alert alert-info border-info border-start border-4 shadow-sm mb-4 d-flex align-items-center" role="alert">
        <i class="fa-solid fa-duotone fa-circle-info fa-2x me-3"></i>
        <div>
            <h5 class="alert-heading fw-bold mb-1">Información del Reporte</h5>
            <p class="mb-0">
                Por defecto, se muestra la información de <b>hoy</b>. Al aplicar los filtros,
                tanto la tabla como los gráficos y tarjetas se recalcularán automáticamente en tiempo real.
            </p>
        </div>
    </div>

    {{-- PANEL DE FILTROS DINÁMICOS --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header p-3">
            <h5 class="fw-bold mb-0 text-primary">
                <i class="fa-duotone fa-filter me-1"></i> Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="fecha_inicio" class="form-label text-muted fw-bold">Desde Fecha:</label>
                    <input type="date" class="form-control" id="fecha_inicio" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="fecha_fin" class="form-label text-muted fw-bold">Hasta Fecha:</label>
                    <input type="date" class="form-control" id="fecha_fin" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-3">
                    <label for="id_nivel" class="form-label text-muted fw-bold">Nivel:</label>
                    <select class="form-select" id="id_nivel">
                        <option value="">Todos</option>
                        @if (isset($niveles))
                            @foreach ($niveles as $nivel)
                                <option value="{{ $nivel->id_nivel }}">{{ $nivel->nivel }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="id_coordinacion" class="form-label text-muted fw-bold">Coordinación:</label>
                    <select class="form-select" id="id_coordinacion">
                        <option value="">Todos</option>
                        @if (isset($coordinaciones))
                            @foreach ($coordinaciones as $coordinacion)
                                <option value="{{ $coordinacion->id_coordinacion }}">{{ $coordinacion->coordinacion }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="id_curso" class="form-label text-muted fw-bold">Curso:</label>
                    <select class="form-select" id="id_curso">
                        <option value="">Todos</option>
                        @if (isset($cursos))
                            @foreach ($cursos as $curso)
                                <option value="{{ $curso->id_curso }}">{{ $curso->curso }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="id_docente" class="form-label text-muted fw-bold">Docente:</label>
                    <select class="form-select" id="id_docente">
                        <option value="">Todos</option>
                        @if (isset($docentes))
                            @foreach ($docentes as $docente)
                                <option value="{{ $docente->id_docente }}">{{ $docente->persona?->nombres_apellidos }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary w-100 shadow-sm" id="btn-filtrar">
                        <i class="fa-duotone fa-search me-1"></i> Filtrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- A) TARJETAS DE MÉTRICAS --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md">
            <div class="card shadow-sm border-start border-4 border-secondary h-100">
                <div class="card-body p-3 text-center">
                    <p class="text-muted fw-bold mb-1 fs-7"><i class="fa-duotone fa-sigma me-1"></i> Total Incidencias</p>
                    <h3 class="fw-bold mb-0 text-secondary" id="kpi-total">0</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card shadow-sm border-start border-4 border-warning h-100">
                <div class="card-body p-3 text-center">
                    <p class="text-muted fw-bold mb-1 fs-7"><i class="fa-duotone fa-hourglass-half me-1"></i> Atrasos</p>
                    <h3 class="fw-bold mb-0 text-warning" id="kpi-atrasos">0</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card shadow-sm border-start border-4 border-danger h-100">
                <div class="card-body p-3 text-center">
                    <p class="text-muted fw-bold mb-1 fs-7"><i class="fa-duotone fa-user-xmark me-1"></i> Faltas</p>
                    <h3 class="fw-bold mb-0 text-danger" id="kpi-faltas">0</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card shadow-sm border-start border-4 border-info h-100">
                <div class="card-body p-3 text-center">
                    <p class="text-muted fw-bold mb-1 fs-7"><i class="fa-duotone fa-file-certificate me-1"></i> Licencias
                    </p>
                    <h3 class="fw-bold mb-0 text-info" id="kpi-licencias">0</h3>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card shadow-sm border-start border-4 border-warning h-100">
                <div class="card-body p-3 text-center">
                    <p class="text-muted fw-bold mb-1 fs-7"><i class="fa-duotone fa-clock-rotate-left me-1"></i> Atrasos
                        (Tiempos)</p>
                    <h5 class="fw-bold mb-0">
                        <i class="fa-solid fa-duotone fa-sigma"></i>: <span id="kpi-minutos-total" class="text-danger">0</span> min
                        <span class="fs-6 text-muted ms-2 fw-normal">(x̄: <span id="kpi-minutos-promedio">0</span>
                            min)</span>
                    </h5>
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
            <div style="position: relative; height: 400px; width: 100%;">
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

    {{-- TABLA DE RESULTADOS --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-info">
                <i class="fa-solid fa-duotone fa-calendar-lines me-2"></i>
                Reporte de incidencias
            </h5>
        </div>
        <div class="card-body">
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
    </div>
@endsection

@section('scripts')
    @include('estudiantes_asistencias.reportes_scripts')
@endsection
