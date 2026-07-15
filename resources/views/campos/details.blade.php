@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-layer-group"></i> {{ $head_title }}
        </h1>
        <a class="btn btn-secondary" href="{{ route('campos.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver
        </a>
    </div>

    @php
        $estado = match ($campo->estado) {
            0 => 'ARCHIVADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $badgeClass = match ($campo->estado) {
            0 => 'bg-secondary',
            1 => 'bg-success',
            default => 'bg-secondary',
        };
    @endphp

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-duotone fa-circle-info me-2"></i>Información del Campo</h5>
            <span class="badge {{ $badgeClass }} fs-6">{{ $estado }}</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="text-muted small text-uppercase">Campo de Saberes y Conocimientos</label>
                    <p class="fs-5 fw-semibold mb-0">{{ $campo->campo }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info"><i class="fa-solid fa-duotone fa-object-group me-2"></i>Áreas SIE</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable w-100" id="areas">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Área</th>
                            <th>Abreviatura</th>
                            <th>Posición Ordinal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($campo->areas as $area)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $area->area }}</td>
                                <td>{{ $area->abreviatura }}</td>
                                <td>{{ $area->posicion_ordinal }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info"><i class="fa-solid fa-duotone fa-book-open me-2"></i>Materias Internas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable w-100" id="materias">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Materia</th>
                            <th>Abreviatura</th>
                            <th>Posición Ordinal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($campo->materias as $materia)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $materia->materia }}</td>
                                <td>{{ $materia->abreviatura }}</td>
                                <td>{{ $materia->posicion_ordinal }}</td>
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
