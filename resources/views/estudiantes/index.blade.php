@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0"><i class="fa-solid fa-duotone fa-user-graduate me-2"></i>{{ $head_title }}</h1>

        {{-- Verifica que el usuario tenga permiso para crear estudiantes --}}
        @can('create', App\Models\Estudiante::class)
            <button type="button" class="btn btn-success shadow-sm btn-crear" data-bs-toggle="modal"
                data-bs-target="#modal-formulario">
                <i class="fa-solid fa-duotone fa-plus me-1"></i> Crear estudiante
            </button>
        @endcan
    </div>

    @include('estudiantes.index_filters')

    {{-- DASHBOARD DE GRÁFICOS (TABS) --}}
    <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-bold text-info" data-bs-toggle="tab" data-bs-target="#tab-tarjetas"
                type="button" role="tab">
                <i class="fa-solid fa-chart-pie me-1"></i> Resumen
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-info" data-bs-toggle="tab" data-bs-target="#tab-niveles" type="button"
                role="tab">
                <i class="fa-solid fa-stairs me-1"></i> Por Nivel
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-bold text-info" data-bs-toggle="tab" data-bs-target="#tab-cursos" type="button"
                role="tab">
                <i class="fa-solid fa-chalkboard-user me-1"></i> Por Curso
            </button>
        </li>
    </ul>

    <div class="tab-content border border-top-0 p-4 mb-4 shadow-sm rounded-bottom bg-body" id="dashboardTabsContent">

        <!-- TAB 1: TARJETAS -->
        <div class="tab-pane fade show active" id="tab-tarjetas" role="tabpanel">
            <div class="row g-3" id="kpi-container">
                <!-- Total Registros (Fijo) -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="p-3 rounded bg-primary bg-opacity-10 text-primary me-3">
                                <i class="fa-duotone fa-users me-1"></i>
                            </div>
                            <div>
                                <span class="text-muted small d-block mb-1">Total Estudiantes</span>
                                <h3 id="kpi-total" class="fw-bold m-0 text-primary">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Las tarjetas por Nivel se inyectarán aquí dinámicamente -->
            </div>
        </div>

        <!-- TAB 2: GRÁFICO DE NIVELES -->
        <div class="tab-pane fade" id="tab-niveles" role="tabpanel">
            <div style="position: relative; height: 350px; width: 100%;">
                <canvas id="chart-niveles"></canvas>
            </div>
        </div>

        <!-- TAB 3: GRÁFICO DE CURSOS -->
        <div class="tab-pane fade" id="tab-cursos" role="tabpanel">
            <div style="position: relative; height: 350px; width: 100%;">
                <canvas id="chart-cursos"></canvas>
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
                            <th class="text-center">Foto de perfil</th>
                            <th>Ap. paterno</th>
                            <th>Ap. materno</th>
                            <th>Nombres</th>
                            <th>C.I.</th>
                            <th>C.I. Complemento</th>
                            <th>C.I. Expedido</th>
                            <th>F. Nacimiento</th>
                            <th class="text-center">Sexo</th>
                            <th>Idioma</th>
                            <th>Celular</th>
                            <th>Teléfono</th>
                            <th>Tipo de perfil</th>
                            <th>Correo</th>
                            <th>Contraseña</th>
                            <th>Código interno</th>
                            <th>Código RUDE</th>
                            <th class="text-center">Tiene acceso</th>
                            <th>Nacimiento país</th>
                            <th>Nacimiento departamento</th>
                            <th>Nacimiento provincia</th>
                            <th>Nacimiento localidad</th>
                            <th>Salud tipo de sangre</th>
                            <th>Salud alergias</th>
                            <th>Salud datos méditos importantes</th>
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

    @include('estudiantes.modal_form')
    @include('components.app.img_modal')
@endsection

@section('scripts')
    @include('estudiantes.index_scripts')
@endsection
