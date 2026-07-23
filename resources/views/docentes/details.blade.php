@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-chalkboard-user me-2"></i> {{ $head_title }}
        </h1>
        <a class="btn btn-outline-secondary shadow-sm" href="{{ route('docentes.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    @php
        $estado = match ($docente->estado) {
            0 => 'ARCHIVADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $badgeClass = match ($docente->estado) {
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
                        src="{{ URL::to('/') }}/{{ $docente->persona->usuario->url_foto_perfil }}">

                    <h4 class="fw-bold text-dark mb-1">
                        {{ $docente->persona->nombres }} {{ $docente->persona->apellido_paterno }}
                    </h4>
                    <p class="text-muted mb-3"><i class="fa-solid fa-id-badge me-1"></i> CI:
                        {{ $docente->persona->documento_identificacion }}
                        {{ $docente->persona->documento_complemento ? '- ' . $docente->persona->documento_complemento : '' }}
                        {{ $docente->persona->documento_expedido }}</p>

                    <span class="badge {{ $badgeClass }} fs-6 px-3 py-2 rounded-pill mb-3">
                        <i class="fa-solid fa-circle-user me-1"></i> {{ $estado }}
                    </span>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-info fw-bold">
                    <i class="fa-solid fa-key me-2"></i> Datos de Acceso
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Correo:</span>
                        <span class="fw-bold">{{ $docente->persona->usuario->correo }}</span>
                    </li>

                    @if (session('tipo_perfil') === 'ADMIN')
                        <li class="list-group-item flex-column align-items-start border-warning border-start border-4">
                            <div class="d-flex justify-content-between w-100 mb-1">
                                <span class="text-muted">Contraseña:</span>
                                <span
                                    class="fw-bold text-muted">{{ helper_decrypt($docente->persona->usuario->contrasenha) }}</span>
                            </div>
                            <small class="text-warning-emphasis"><i class="fa-solid fa-circle-info"></i> Visible solo para
                                ADMIN</small>
                        </li>
                    @endif

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Acceso al sistema:</span>
                        @if ($docente->persona->usuario->tiene_acceso)
                            <span class="badge bg-success rounded-pill px-3">SÍ</span>
                        @else
                            <span class="badge bg-danger rounded-pill px-3">NO</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Perfil:</span>
                        <span class="fw-bold text-secondary">{{ $docente->persona->tipo_perfil }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header border-bottom-0 pt-4 pb-0">
                    <h5 class="text-info fw-bold"><i class="fa-solid fa-address-card me-2"></i> Información Personal</h5>
                </div>
                <div class="card-body">
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Apellido paterno:</div>
                        <div class="col-sm-8 fw-bold">{{ $docente->persona->apellido_paterno }}</div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Apellido materno:</div>
                        <div class="col-sm-8 fw-bold">{{ $docente->persona->apellido_materno }}</div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Nombre/s:</div>
                        <div class="col-sm-8 fw-bold">{{ $docente->persona->nombres }}</div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Fecha de nacimiento:</div>
                        <div class="col-sm-8">{{ date('d/m/Y', strtotime($docente->persona->fecha_nacimiento)) }}</div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Sexo:</div>
                        <div class="col-sm-8">{{ $docente->persona->sexo == 'M' ? 'MASCULINO' : 'FEMENINO' }}</div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Idioma:</div>
                        <div class="col-sm-8">{{ $docente->persona->idioma }}</div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Celular:</div>
                        <div class="col-sm-8">{{ $docente->persona->celular }}</div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-sm-4 text-muted">Teléfono fijo:</div>
                        <div class="col-sm-8">{{ $docente->persona->telefono }}</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header border-bottom-0 pt-4 pb-0">
                    <h5 class="text-info fw-bold"><i class="fa-solid fa-graduation-cap me-2"></i> Información Académica</h5>
                </div>
                <div class="card-body">
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Resp. de Nivel:</div>
                        <div class="col-sm-8 fw-bold text-primary">{{ $docente->nivel?->nivel ?: '-' }}</div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Resp. de Coordinación:</div>
                        <div class="col-sm-8 fw-bold text-primary">{{ $docente->coordinacion?->coordinacion ?: '-' }}</div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Especialidad:</div>
                        <div class="col-sm-8">{{ $docente->especialidad ?: '-' }}</div>
                    </div>
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Grado de estudios:</div>
                        <div class="col-sm-8">{{ $docente->grado_estudios }}</div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-sm-4 text-muted">Domicilio:</div>
                        <div class="col-sm-8">{{ $docente->domicilio }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-5">
        <div class="card-header pt-4 pb-2 border-bottom">
            <h5 class="text-info fw-bold mb-0"><i class="fa-solid fa-book-open me-2"></i> Listas de asignaturas asignadas
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped mb-0 dataTable" id="materias">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Asignatura</th>
                            <th scope="col">Tipo de Calificación</th>
                            <th scope="col">Tipo de Bloque</th>
                            <th scope="col">Periodo</th>
                            <th scope="col">Gestión</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($docente->listas_asignaturas as $lista_asignatura)
                            <tr>
                                <td class="fw-bold">{{ $loop->index + 1 }}</td>
                                <td>{{ $lista_asignatura->asignatura->asignatura }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        @php
                                            $icono =
                                                $lista_asignatura->asignatura->tipo_calificacion === 'cualitativa'
                                                    ? '<i class="fa-solid fa-comments"></i>'
                                                    : '<i class="fa-solid fa-chart-column"></i>';
                                        @endphp
                                        {!! $icono !!}
                                        {{ strtoupper($lista_asignatura->asignatura->tipo_calificacion) }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $lista_asignatura->asignatura->tipo_bloque === 'curso' ? 'bg-primary' : 'bg-danger' }}">
                                        {{ strtoupper($lista_asignatura->asignatura->tipo_bloque) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $lista_asignatura->periodo->periodo }}
                                    </span>
                                </td>
                                <td>{{ $lista_asignatura->periodo->gestion->anio }}</td>
                                <td>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
