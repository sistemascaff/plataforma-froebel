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
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body text-center pt-4">
                    <img class="rounded-circle img-thumbnail shadow-sm mb-3"
                        style="width: 160px; height: 160px; object-fit: cover;" alt="Foto de perfil"
                        src="{{ URL::to('/') }}/{{ $estudiante->persona->usuario->url_foto_perfil }}">

                    <h4 class="fw-bold text-dark mb-1">
                        {{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellido_paterno }}
                    </h4>
                    <p class="text-muted mb-3"><i class="fa-solid fa-id-badge me-1"></i> CI:
                        {{ $estudiante->persona->documento_identificacion }}
                        {{ $estudiante->persona->documento_complemento ? '- ' . $estudiante->persona->documento_complemento : '' }}
                        {{ $estudiante->persona->documento_expedido }}</p>

                    <span class="badge {{ $badgeClass }} fs-6 px-3 py-2 rounded-pill mb-3">
                        <i class="fa-solid fa-circle-user me-1"></i> {{ $estado }}
                    </span>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-info fw-bold">
                    <i class="fa-solid fa-key me-2"></i> Datos de Acceso
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Correo:</span>
                        <span class="fw-bold">{{ $estudiante->persona->usuario->correo }}</span>
                    </li>

                    @if (session('tipo_perfil') === 'ADMIN')
                        <li
                            class="list-group-item flex-column align-items-start border-warning border-start border-4">
                            <div class="d-flex justify-content-between w-100 mb-1">
                                <span class="text-muted">Contraseña:</span>
                                <span
                                    class="fw-bold text-muted">{{ helper_decrypt($estudiante->persona->usuario->contrasenha) }}</span>
                            </div>
                            <small class="text-warning-emphasis"><i class="fa-solid fa-circle-info"></i> Visible solo para
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
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header border-bottom-0 pt-4 pb-0">
                    <h5 class="text-info fw-bold"><i class="fa-solid fa-address-card me-2"></i> Información Personal</h5>
                </div>
                <div class="card-body">
                    <div class="row border-bottom pb-2 mb-2">
                        <div class="col-sm-4 text-muted">Nombre completo:</div>
                        <div class="col-sm-8 fw-bold">{{ $estudiante->persona->nombres }}
                            {{ $estudiante->persona->apellido_paterno }} {{ $estudiante->persona->apellido_materno }}
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

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header border-bottom-0 pt-4 pb-0">
                    <h5 class="text-info fw-bold"><i class="fa-solid fa-school me-2"></i> Datos Académicos y Nacimiento</h5>
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

            <div class="card shadow-sm border-0 mb-4 border-start border-danger border-4">
                <div class="card-header border-bottom-0 pt-4 pb-0">
                    <h5 class="text-danger fw-bold"><i class="fa-solid fa-notes-medical me-2"></i> Información de Salud</h5>
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
    </div>
@endsection
