{{-- Tarjeta de Información Académica --}}
<div class="card shadow-sm mb-4">
    <div class="card-header p-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-primary">
            <i class="fa-duotone fa-info-circle me-1"></i> Información Académica
        </h5>
        <span
            class="badge {{ $lista_asignatura->asignatura->tipo_bloque === 'mixto' ? 'bg-warning text-dark' : 'bg-info' }} px-3 py-2 fs-6 shadow-sm">
            <i class="fa-duotone fa-cubes me-1"></i> Bloque {{ ucfirst($lista_asignatura->asignatura->tipo_bloque) }}
        </span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-book me-1"></i> Asignatura:</p>
                <p class="mb-0 fs-6 fw-bold">{{ $lista_asignatura->asignatura->asignatura }}</p>
            </div>
            <div class="col-md-4">
                <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-calendar me-1"></i> Gestión y Periodo:</p>
                <p class="mb-0 fs-6">
                    {{ $lista_asignatura->periodo->gestion->anio }} - {{ $lista_asignatura->periodo->periodo }}
                    @php
                        $periodoEstado = $lista_asignatura->periodo->estado;
                        $periodoEstadoClass = match ($periodoEstado) {
                            1 => 'bg-success',
                            0 => 'bg-danger',
                            default => 'bg-secondary',
                        };
                        $periodoEstadoTexto = match ($periodoEstado) {
                            1 => 'ACTIVO',
                            0 => 'INACTIVO',
                            default => 'DESCONOCIDO',
                        };
                    @endphp
                    <span class="badge {{ $periodoEstadoClass }} ms-1">{{ $periodoEstadoTexto }}</span>
                </p>
            </div>
            <div class="col-md-4">
                <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-chalkboard-user me-1"></i> Docente Asignado:
                </p>
                <p class="mb-0 fs-6">
                    @if ($lista_asignatura->docente)
                        {{ $lista_asignatura->docente->persona->nombres_apellidos }}
                    @else
                        <span class="text-danger"><i class="fa-duotone fa-triangle-exclamation"></i> Sin asignar</span>
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
