@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-stairs"></i> {{ $head_title }}
        </h1>
        <a class="btn btn-secondary" href="{{ route('niveles.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-circle-info"></i> Información del Nivel</h5>
            @php
                $badgeClass = match ($nivel->estado) {
                    0 => 'bg-secondary',
                    1 => 'bg-success',
                    default => 'bg-secondary',
                };
                $estadoText = match ($nivel->estado) {
                    0 => 'ARCHIVADO',
                    1 => 'ACTIVO',
                    default => 'DESCONOCIDO',
                };
            @endphp
            <span class="badge {{ $badgeClass }} fs-6">{{ $estadoText }}</span>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-12">
                    <span class="text-muted d-block fw-bold mb-1">Nivel</span>
                    <div class="fs-5">{{ $nivel->nivel }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-list-ol"></i> Grados</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable w-100" id="grados">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Grado</th>
                            <th>Posición Ordinal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($nivel->grados as $grado)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $grado->grado }}</td>
                                <td>{{ $grado->posicion_ordinal }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-book-open-reader"></i> Asignaturas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable w-100" id="asignaturas">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Asignatura</th>
                            <th>Tipo de Calificación</th>
                            <th>Tipo de Bloque</th>
                            <th>Curso</th>
                            <th>Aula</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($nivel->asignaturas as $asignatura)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $asignatura->asignatura }}</td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        @php
                                            $icono =
                                                $asignatura->tipo_calificacion === 'cualitativa'
                                                    ? 'fa-comments'
                                                    : 'fa-chart-column';
                                        @endphp
                                        <i
                                            class="fa-solid fa-duotone {{ $icono }} me-1"></i>{{ strtoupper($asignatura->tipo_calificacion) }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $asignatura->tipo_bloque === 'curso' ? 'bg-primary' : 'bg-danger' }}">
                                        {{ strtoupper($asignatura->tipo_bloque) }}
                                    </span>
                                </td>
                                <td>{{ $asignatura->curso ? $asignatura->curso->curso : 'N/A' }}</td>
                                <td>{{ $asignatura->aula ? $asignatura->aula->aula : 'N/A' }}</td>
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
