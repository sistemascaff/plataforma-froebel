@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-user-tie me-2"></i> {{ $head_title }}
        </h1>
        <a class="btn btn-outline-secondary shadow-sm" href="{{ route('personas.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left me-1"></i>Volver
        </a>
    </div>

    @php
        $estado = match ($persona->estado) {
            0 => 'ARCHIVADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $badgeClass = match ($persona->estado) {
            0 => 'bg-secondary',
            1 => 'bg-success',
            default => 'bg-dark',
        };
    @endphp

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-body text-center pt-4">
                    <img class="rounded-circle img-thumbnail shadow-sm mb-3 zoomable-image"
                        style="width: 160px; height: 160px; object-fit: cover;" alt="Foto de perfil"
                        src="{{ URL::to('/') }}/{{ $persona->usuario->url_foto_perfil }}">

                    <h4 class="fw-bold mb-1 text-primary">
                        {{ $persona->nombres_apellidos }}
                    </h4>
                    <p class="text-muted mb-3"><i class="fa-solid fa-duotone fa-id-badge me-1"></i>CI:
                        {{ $persona->documento_identificacion }}
                        {{ $persona->documento_complemento ? '- ' . $persona->documento_complemento : '' }}
                        {{ $persona->documento_expedido }}</p>

                    <span class="badge {{ $badgeClass }} fs-6 px-3 py-2 rounded-pill mb-3 shadow-sm">
                        <i class="fa-solid fa-duotone fa-circle-user me-1"></i>{{ $estado }}
                    </span>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-info fw-bold text-white">
                    <i class="fa-solid fa-duotone fa-key me-2"></i>Datos de Acceso
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Correo:</span>
                        <span class="fw-bold">{{ $persona->usuario->correo }}</span>
                    </li>

                    {{-- Contraseña visible solo para ADMIN --}}
                    @if (Auth::user()->persona?->tipo_perfil === 'ADMIN' && Auth::user()->id_persona !== $persona->id_persona)
                        <li class="list-group-item flex-column align-items-start border-warning border-start border-4">
                            <div class="d-flex justify-content-between w-100 mb-1">
                                <span class="text-muted">Contraseña:</span>
                                <span
                                    class="fw-bold text-muted">{{ helper_decrypt($persona->usuario->contrasenha) }}</span>
                            </div>
                            <small class="text-warning-emphasis"><i class="fa-solid fa-duotone fa-circle-info"></i> Visible
                                solo para ADMIN</small>
                        </li>
                    @endif

                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Acceso al sistema:</span>
                        @if ($persona->usuario->tiene_acceso)
                            <span class="badge bg-success rounded-pill px-3">SÍ</span>
                        @else
                            <span class="badge bg-danger rounded-pill px-3">NO</span>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span class="text-muted">Perfil:</span>
                        <span class="fw-bold text-secondary">{{ $persona->tipo_perfil }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="col-12 col-lg-8">
            <div class="card shadow-sm mb-4 h-100">
                <div class="card-header border-bottom-0 pt-4 pb-0">
                    <h5 class="text-info fw-bold"><i class="fa-solid fa-duotone fa-address-card me-2"></i>Información
                        Personal</h5>
                </div>
                <div class="card-body">
                    <div class="row border-bottom pb-3 mb-3">
                        <div class="col-sm-4 text-muted">Apellido paterno:</div>
                        <div class="col-sm-8 fw-bold">{{ $persona->apellido_paterno }}</div>
                    </div>
                    <div class="row border-bottom pb-3 mb-3">
                        <div class="col-sm-4 text-muted">Apellido materno:</div>
                        <div class="col-sm-8 fw-bold">{{ $persona->apellido_materno }}</div>
                    </div>
                    <div class="row border-bottom pb-3 mb-3">
                        <div class="col-sm-4 text-muted">Nombre/s:</div>
                        <div class="col-sm-8 fw-bold">{{ $persona->nombres }}</div>
                    </div>
                    <div class="row border-bottom pb-3 mb-3">
                        <div class="col-sm-4 text-muted">Fecha de nacimiento:</div>
                        <div class="col-sm-8">{{ date('d/m/Y', strtotime($persona->fecha_nacimiento)) }}</div>
                    </div>
                    <div class="row border-bottom pb-3 mb-3">
                        <div class="col-sm-4 text-muted">Sexo:</div>
                        <div class="col-sm-8">{{ $persona->sexo == 'M' ? 'MASCULINO' : 'FEMENINO' }}</div>
                    </div>
                    <div class="row border-bottom pb-3 mb-3">
                        <div class="col-sm-4 text-muted">Idioma:</div>
                        <div class="col-sm-8">{{ $persona->idioma }}</div>
                    </div>
                    <div class="row border-bottom pb-3 mb-3">
                        <div class="col-sm-4 text-muted">Celular:</div>
                        <div class="col-sm-8">{{ $persona->celular }}</div>
                    </div>
                    <div class="row pb-2">
                        <div class="col-sm-4 text-muted">Teléfono fijo:</div>
                        <div class="col-sm-8">{{ $persona->telefono }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.app.img_modal')
@endsection
