@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-user-graduate me-2"></i> {{ $head_title ?? 'Detalles del Estudiante' }}
        </h1>
        <a class="btn btn-outline-secondary shadow-sm" href="{{ route('estudiantes.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left me-1"></i> Volver
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
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center pt-4">
                    <img class="rounded-circle img-thumbnail shadow-sm mb-3"
                        style="width: 160px; height: 160px; object-fit: cover;" alt="Foto de perfil"
                        src="{{ URL::to('/') }}/{{ $estudiante->persona->usuario->url_foto_perfil }}">

                    <h4 class="fw-bold text-dark mb-1">
                        {{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellido_paterno }}
                    </h4>
                    <p class="text-muted mb-3"><i class="fa-solid fa-duotone fa-id-badge me-1"></i> CI:
                        {{ $estudiante->persona->documento_identificacion }}
                        {{ $estudiante->persona->documento_complemento ? '- ' . $estudiante->persona->documento_complemento : '' }}
                        {{ $estudiante->persona->documento_expedido }}</p>

                    <span class="badge {{ $badgeClass }} fs-6 px-3 py-2 rounded-pill mb-3">
                        <i class="fa-solid fa-duotone fa-circle-user me-1"></i> {{ $estado }}
                    </span>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-info fw-bold">
                    <i class="fa-solid fa-duotone fa-key me-2"></i> Datos de Acceso
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Correo:</span>
                        <span class="fw-bold">{{ $estudiante->persona->usuario->correo }}</span>
                    </li>

                    @if (session('tipo_perfil') === 'ADMIN')
                        <li class="list-group-item flex-column align-items-start border-warning border-start border-4">
                            <div class="d-flex justify-content-between w-100 mb-1">
                                <span class="text-muted">Contraseña:</span>
                                <span
                                    class="fw-bold text-muted">{{ helper_decrypt($estudiante->persona->usuario->contrasenha) }}</span>
                            </div>
                            <small class="text-warning-emphasis"><i class="fa-solid fa-duotone fa-circle-info"></i> Visible
                                solo para
                                ADMIN</small>
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

        <div class="col-12 col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header border-bottom-0 pt-4 pb-0">
                    <h5 class="text-info fw-bold"><i class="fa-solid fa-duotone fa-address-card me-2"></i> Información
                        Personal</h5>
                </div>
                <div class="card-body">
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Apellido paterno:</div>
                        <div class="col-sm-8 fw-bold">{{ $estudiante->persona->apellido_paterno }}
                        </div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Apellido materno:</div>
                        <div class="col-sm-8 fw-bold">{{ $estudiante->persona->apellido_materno }}
                        </div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Nombre/s:</div>
                        <div class="col-sm-8 fw-bold">{{ $estudiante->persona->nombres }}
                        </div>
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

        <div class="col-12 col-lg-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-info">
                        <i class="fa-solid fa-duotone fa-book-open-reader me-2"></i>
                        Asignaturas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped dataTable" id="asignaturas">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Asignatura</th>
                                    <th scope="col">Tipo de Calificación</th>
                                    <th scope="col">Tipo de Bloque</th>
                                    <th scope="col">Docente</th>
                                    <th scope="col">Gestión</th>
                                    <th scope="col">Periodo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($estudiante->listas_asignaturas as $lista_asignatura)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $lista_asignatura->asignatura->asignatura }}</td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                @php
                                                    $icono =
                                                        $lista_asignatura->asignatura->tipo_calificacion ===
                                                        'cualitativa'
                                                            ? 'fa-comments'
                                                            : 'fa-chart-column';
                                                @endphp
                                                <i
                                                    class="fa-solid fa-duotone {{ $icono }} me-1"></i>{{ strtoupper($lista_asignatura->asignatura->tipo_calificacion) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $lista_asignatura->asignatura->tipo_bloque === 'curso' ? 'bg-primary' : 'bg-danger' }}">
                                                {{ strtoupper($lista_asignatura->asignatura->tipo_bloque) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $p = $lista_asignatura->docente?->persona;
                                                $nombre_completo = $p
                                                    ? trim(
                                                        "{$p->apellido_paterno} {$p->apellido_materno} {$p->nombres}",
                                                    )
                                                    : '';
                                            @endphp
                                            {{ $nombre_completo }}
                                        </td>
                                        <td>{{ $lista_asignatura->periodo->gestion->anio }}</td>
                                        <td>{{ $lista_asignatura->periodo->periodo }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $(".dataTable").DataTable({
                @include('components.datatables.datatables_global_properties')
                @include('components.datatables.datatables_language_property')
            });
        });
    </script>
@endsection
