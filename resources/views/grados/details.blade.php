@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-object-group"></i> {{ $head_title }}
        </h1>
        <a class="btn btn-secondary" href="{{ route('grados.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-circle-info"></i> Detalles del Grado</h5>
            @php
                $badgeClass = match ($grado->estado) {
                    0 => 'bg-secondary',
                    1 => 'bg-success',
                    default => 'bg-secondary',
                };
                $estadoText = match ($grado->estado) {
                    0 => 'ARCHIVADO',
                    1 => 'ACTIVO',
                    default => 'DESCONOCIDO',
                };
            @endphp
            <span class="badge {{ $badgeClass }} fs-6">{{ $estadoText }}</span>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-4">
                    <span class="text-muted d-block fw-bold mb-1">Grado</span>
                    <div class="fs-5">{{ $grado->grado }}</div>
                </div>
                <div class="col-md-4">
                    <span class="text-muted d-block fw-bold mb-1">Posición Ordinal</span>
                    <div class="fs-5">{{ $grado->posicion_ordinal }}</div>
                </div>
                <div class="col-md-4">
                    <span class="text-muted d-block fw-bold mb-1">Nivel</span>
                    <div class="fs-5">{{ $grado->nivel->nivel }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-book-open"></i> Malla Curricular</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable w-100" id="areas">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Materia</th>
                            <th>M. Abreviatura</th>
                            <th>Area</th>
                            <th>A. Abreviatura</th>
                            <th>Gestión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($grado->mallas_curriculares as $malla_curricular)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $malla_curricular->materia->materia }}</td>
                                <td>{{ $malla_curricular->materia->abreviatura }}</td>
                                <td>{{ $malla_curricular->area->area }}</td>
                                <td>{{ $malla_curricular->area->abreviatura }}</td>
                                <td>{{ $malla_curricular->gestion->anio }}</td>
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
