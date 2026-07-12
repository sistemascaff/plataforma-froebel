@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h1 class="text-info fw-bold mb-0 me-auto">
            <i class="fa-solid fa-duotone fa-books fa-rotate-270 me-2"></i>{{ $head_title }}
        </h1>

        <div class="d-flex gap-2">
            <a class="btn btn-secondary" href="{{ route('prestamos_libros.index') }}">
                <i class="fa-solid fa-duotone fa-arrow-left me-1"></i> Volver
            </a>
            <a class="btn btn-primary" href="{{ route('prestamos_libros.imprimir', $prestamo_libro->id_prestamo_libro) }}"
                target="_blank" rel="noopener noreferrer">
                <i class="fa-solid fa-duotone fa-print me-1"></i> Imprimir
            </a>
            <a class="btn btn-warning" href="{{ route('prestamos_libros.editar', $prestamo_libro->id_prestamo_libro) }}">
                <i class="fa-solid fa-duotone fa-edit me-1"></i> Editar
            </a>
        </div>
    </div>

    @php
        $estadoStr = match ($prestamo_libro->estado) {
            0 => 'ANULADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $estadoBadge = match ($prestamo_libro->estado) {
            0 => 'bg-danger',
            1 => 'bg-success',
            default => 'bg-secondary',
        };
    @endphp

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-info">
                <i class="fa-solid fa-duotone fa-circle-info me-2"></i> Información del Préstamo
            </h5>
            <span class="badge {{ $estadoBadge }} fs-6 px-3 py-2"><i class="fa-solid fa-flag me-1"></i>
                {{ $estadoStr }}</span>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-5">
                    <p class="text-muted mb-1 fw-bold"><i class="fa-solid fa-user-graduate me-1"></i> Prestatario</p>
                    <p class="fs-5 mb-0 text-break" id="persona">
                        {{ trim('(' . $prestamo_libro->persona->tipo_perfil . ') ' . $prestamo_libro->persona->apellido_paterno . ' ' . $prestamo_libro->persona->apellido_materno . ' ' . $prestamo_libro->persona->nombres) }}
                    </p>
                </div>
                <div class="col-md-3">
                    <p class="text-muted mb-1 fw-bold"><i class="fa-solid fa-chalkboard-user me-1"></i> Curso</p>
                    <p class="fs-5 mb-0" id="curso">{{ $prestamo_libro->curso ?: 'N/A' }}</p>
                </div>
                <div class="col-md-4">
                    <p class="text-muted mb-1 fw-bold"><i class="fa-solid fa-mobile-screen me-1"></i> Celular</p>
                    <p class="fs-5 mb-0" id="celular">{{ $prestamo_libro->celular ?? '-' }}</p>
                </div>
            </div>

            <hr class="my-4 text-muted">

            <div class="row g-4">
                <div class="col-md-12">
                    <p class="text-muted mb-1 fw-bold"><i class="fa-solid fa-calendar-check me-1"></i> Fecha de Devolución
                        Estipulada</p>
                    <p
                        class="fs-5 mb-0 {{ strtotime($prestamo_libro->fecha_devolucion) < strtotime(date('Y-m-d')) && $prestamo_libro->estado != 0 ? 'text-danger fw-bold' : 'text-primary fw-bold' }}">
                        {{ date('d/m/Y', strtotime($prestamo_libro->fecha_devolucion)) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info">
                <i class="fa-solid fa-duotone fa-list-check me-2"></i> Libros Prestados
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0" id="detalles">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center align-middle">#</th>
                            <th width="15%" class="align-middle">Código</th>
                            <th class="align-middle">Título</th>
                            <th width="15%" class="text-center align-middle">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prestamo_libro->libros as $libro)
                            <tr>
                                <td class="fw-bold text-primary text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="codigo align-middle">{{ $libro->codigo }}</td>
                                <td class="titulo align-middle">{{ $libro->titulo }}</td>
                                <td class="text-center align-middle">
                                    @if ($prestamo_libro->estado != 0)
                                        @if ($libro->pivot->fecha_retorno == null)
                                            <button type="button" class="btn btn-success btn-sm btn-marcar"
                                                data-id="{{ $libro->id_libro }}" data-toggle="tooltip"
                                                title="Marcar como devuelto">
                                                <i class="fa fa-check"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-warning btn-sm btn-marcar"
                                                data-id="{{ $libro->id_libro }}" data-toggle="tooltip"
                                                title="Desmarcar / Marcar como pendiente">
                                                <i class="fa fa-xmark"></i>
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
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
    @include('prestamos_libros.details_scripts')
@endsection
