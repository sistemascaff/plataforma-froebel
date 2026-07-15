@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-book-open"></i> {{ $head_title }}
        </h1>
        <a class="btn btn-secondary" href="{{ route('materias.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver
        </a>
    </div>

    @php
        $estado = match ($materia->estado) {
            0 => 'ARCHIVADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $badgeClass = match ($materia->estado) {
            0 => 'bg-secondary',
            1 => 'bg-success',
            default => 'bg-secondary',
        };
    @endphp

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-duotone fa-circle-info me-2"></i>Información de la Materia Interna
            </h5>
            <span class="badge {{ $badgeClass }} fs-6">{{ $estado }}</span>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="text-muted small text-uppercase">Materia</label>
                    <p class="fs-5 fw-semibold mb-0">{{ $materia->materia }}</p>
                </div>
                <div class="col-md-3">
                    <label class="text-muted small text-uppercase">Abreviatura</label>
                    <p class="fs-5 fw-semibold mb-0">{{ $materia->abreviatura }}</p>
                </div>
                <div class="col-md-3">
                    <label class="text-muted small text-uppercase">Posición Ordinal</label>
                    <p class="fs-5 fw-semibold mb-0">{{ $materia->posicion_ordinal }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info"><i class="fa-solid fa-duotone fa-sitemap me-2"></i>Mallas Curriculares de la
                Materia</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable w-100" id="detalles">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Grado</th>
                            <th>P. Ordinal de grado</th>
                            <th>Área SIE (Ministerio)</th>
                            <th>Gestión</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($materia->mallas_curriculares as $malla_curricular)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $malla_curricular->grado->grado }}</td>
                                <td>{{ $malla_curricular->grado->posicion_ordinal }}</td>
                                <td>{{ $malla_curricular->area->abreviatura }} - {{ $malla_curricular->area->area }}</td>
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
                responsive: true,
                lengthChange: true,
                autoWidth: true,
                colReorder: true,
                order: [],
                pageLength: 100,
                dom: 'Blfrtip',
                buttons: [{
                        extend: 'copy',
                        className: 'btn btn-secondary'
                    },
                    {
                        extend: 'csv',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'excel',
                        className: 'btn btn-success'
                    },
                    {
                        extend: 'pdf',
                        className: 'btn btn-danger'
                    },
                    {
                        extend: 'colvis',
                        className: 'btn btn-info'
                    },
                    {
                        extend: 'searchBuilder',
                        className: 'btn btn-warning'
                    },
                ],
                @include('components.datatables.datatables_language_property')
            });
        });
    </script>
@endsection
