@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-user-graduate me-2"></i>{{ $head_title ?? 'Detalles del Estudiante' }}
        </h1>
        <a class="btn btn-outline-secondary shadow-sm" href="{{ route('estudiantes.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left me-1"></i>Volver
        </a>
    </div>

    @php
        $estado = match ($estudiante->estado) {
            0 => 'ARCHIVADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $badgeClass = match ($estudiante->estado) {
            0 => 'bg-secondary',
            1 => 'bg-success',
            default => 'bg-dark',
        };
    @endphp

    <div class="row g-4 mb-4">
        <!-- Tarjetas Laterales de Perfil y Acceso -->
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center pt-4">
                    <img class="rounded-circle img-thumbnail shadow-sm mb-3 zoomable-image"
                        style="width: 160px; height: 160px; object-fit: cover;" alt="Foto de perfil"
                        src="{{ URL::to('/') }}/{{ $estudiante->persona->usuario->url_foto_perfil }}">

                    <h4 class="fw-bold mb-1">
                        {{ $estudiante->persona->apellidos_nombres }}
                    </h4>
                    <p class="text-muted mb-3"><i class="fa-solid fa-duotone fa-id-badge me-1"></i>CI:
                        {{ $estudiante->persona->documento_identificacion }}
                        {{ $estudiante->persona->documento_complemento ? '- ' . $estudiante->persona->documento_complemento : '' }}
                        {{ $estudiante->persona->documento_expedido }}</p>

                    <span class="badge {{ $badgeClass }} fs-6 px-3 py-2 rounded-pill mb-3">
                        <i class="fa-solid fa-duotone fa-circle-user me-1"></i>{{ $estado }}
                    </span>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-info fw-bold">
                    <i class="fa-solid fa-duotone fa-key me-2"></i>Datos de Acceso
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Correo:</span>
                        <span class="fw-bold">{{ $estudiante->persona->usuario->correo }}</span>
                    </li>

                    @if (Auth::user()->persona?->tipo_perfil === 'ADMIN')
                        <li class="list-group-item flex-column align-items-start border-warning border-start border-4">
                            <div class="d-flex justify-content-between w-100 mb-1">
                                <span class="text-muted">Contraseña:</span>
                                <span
                                    class="fw-bold text-muted">{{ helper_decrypt($estudiante->persona->usuario->contrasenha) }}</span>
                            </div>
                            <small class="text-warning-emphasis"><i class="fa-solid fa-duotone fa-circle-info"></i> Visible
                                solo para ADMIN</small>
                        </li>
                    @endif

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Acceso al sistema:</span>
                        @if ($estudiante->persona->usuario->tiene_acceso)
                            <span class="badge bg-success rounded-pill px-3">SÍ</span>
                        @else
                            <span class="badge bg-danger rounded-pill px-3">NO</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Perfil:</span>
                        <span class="fw-bold text-secondary">{{ $estudiante->persona->tipo_perfil }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Tarjetas Centrales de Información -->
        <div class="col-12 col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header border-bottom-0 pt-4 pb-0">
                    <h5 class="text-info fw-bold"><i class="fa-solid fa-duotone fa-address-card me-2"></i> Información
                        Personal</h5>
                </div>
                <div class="card-body">
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Apellido paterno:</div>
                        <div class="col-sm-8 fw-bold">{{ $estudiante->persona->apellido_paterno }}</div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Apellido materno:</div>
                        <div class="col-sm-8 fw-bold">{{ $estudiante->persona->apellido_materno }}</div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Nombre/s:</div>
                        <div class="col-sm-8 fw-bold">{{ $estudiante->persona->nombres }}</div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Fecha de nacimiento:</div>
                        <div class="col-sm-8">{{ date('d/m/Y', strtotime($estudiante->persona->fecha_nacimiento)) }}</div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Sexo:</div>
                        <div class="col-sm-8">{{ $estudiante->persona->sexo == 'M' ? 'MASCULINO' : 'FEMENINO' }}</div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Idioma:</div>
                        <div class="col-sm-8">{{ $estudiante->persona->idioma }}</div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Celular:</div>
                        <div class="col-sm-8">{{ $estudiante->persona->celular ?: 'No registrado' }}</div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-sm-4 text-muted">Teléfono fijo:</div>
                        <div class="col-sm-8">{{ $estudiante->persona->telefono ?: 'No registrado' }}</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header border-bottom-0 pt-4 pb-0">
                    <h5 class="text-info fw-bold"><i class="fa-solid fa-duotone fa-school me-2"></i> Datos Académicos y
                        Nacimiento</h5>
                </div>
                <div class="card-body">
                    <div class="row border-bottom pb-2 mb-2 bg-light rounded p-2">
                        <div class="col-sm-4 text-dark align-self-center">Curso asignado:</div>
                        <div class="col-sm-8 fw-bold text-primary fs-5">
                            {{ $estudiante->curso?->curso ?: 'Sin curso asignado' }}</div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2 mt-3">
                        <div class="col-sm-4 text-muted">País de nacimiento:</div>
                        <div class="col-sm-8">{{ $estudiante->nacimiento_pais }}</div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Departamento:</div>
                        <div class="col-sm-8">{{ $estudiante->nacimiento_departamento }}</div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Provincia:</div>
                        <div class="col-sm-8">{{ $estudiante->nacimiento_provincia }}</div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-sm-4 text-muted">Localidad:</div>
                        <div class="col-sm-8">{{ $estudiante->nacimiento_localidad }}</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4 border-start border-danger border-4">
                <div class="card-header border-bottom-0 pt-4 pb-0">
                    <h5 class="text-danger fw-bold"><i class="fa-solid fa-duotone fa-notes-medical me-2"></i> Información de
                        Salud</h5>
                </div>
                <div class="card-body">
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Tipo de sangre:</div>
                        <div class="col-sm-8 fw-bold text-danger">{{ $estudiante->salud_tipo_sangre ?: 'No especificado' }}
                        </div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Alergias:</div>
                        <div class="col-sm-8">{{ $estudiante->salud_alergias ?: 'Ninguna registrada' }}</div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-sm-4 text-muted">Datos médicos adicionales:</div>
                        <div class="col-sm-8">{{ $estudiante->salud_datos ?: 'Sin observaciones' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SECCIÓN DE PESTAÑAS (TABS) -->
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header border-bottom-0 pt-3 pb-0">
                    <ul class="nav nav-tabs" id="estudianteTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold text-info" id="asignaturas-tab" data-bs-toggle="tab"
                                data-bs-target="#asignaturas-pane" type="button" role="tab"
                                aria-controls="asignaturas-pane" aria-selected="true">
                                <i class="fa-solid fa-duotone fa-book-open-reader me-2"></i>Asignaturas
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-info" id="asistencias-tab" data-bs-toggle="tab"
                                data-bs-target="#asistencias-pane" type="button" role="tab"
                                aria-controls="asistencias-pane" aria-selected="false">
                                <i class="fa-solid fa-duotone fa-clipboard-user me-2"></i>Asistencias
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-info" id="licencias-tab" data-bs-toggle="tab"
                                data-bs-target="#licencias-pane" type="button" role="tab"
                                aria-controls="licencias-pane" aria-selected="false">
                                <i class="fa-solid fa-duotone fa-file-certificate me-2"></i>Licencias
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold text-info" id="prestamos-libros-tab" data-bs-toggle="tab"
                                data-bs-target="#prestamos-libros-pane" type="button" role="tab"
                                aria-controls="prestamos-libros-pane" aria-selected="false">
                                <i class="fa-solid fa-duotone fa-book-open me-2"></i>Préstamos de Libros
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <div class="tab-content" id="estudianteTabsContent">

                        <!-- Panel de Asignaturas -->
                        @include('estudiantes.details_asignaturas')

                        <!-- Panel de Asistencias -->
                        @include('estudiantes.details_asistencias')

                        <!-- Panel de Licencias -->
                        @include('estudiantes.details_licencias')

                        <!-- Panel de Préstamos de libros -->
                        @include('estudiantes.details_prestamos_libros')

                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.app.img_modal')
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            var tablaAsignaturas = $("#asignaturas").DataTable({
                @include('components.datatables.datatables_global_properties')
                @include('components.datatables.datatables_language_property')
            });

            var tablaAsistencias = $("#tabla-asistencias").DataTable({
                @include('components.datatables.datatables_global_properties')
                @include('components.datatables.datatables_language_property')
            });

            var tablaLicencias = $("#tabla-licencias").DataTable({
                @include('components.datatables.datatables_global_properties')
                @include('components.datatables.datatables_language_property')
            });

            var tablaPrestamos = $("#tabla-prestamos-libros").DataTable({
                @include('components.datatables.datatables_global_properties')
                @include('components.datatables.datatables_language_property')
                // Ordenar por préstamo más reciente
                ,
                "order": [
                    [1, "desc"]
                ]
            });

            // Reajustar DataTables al cambiar de pestaña
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                }).columns.adjust();
            });
        });
    </script>
@endsection
