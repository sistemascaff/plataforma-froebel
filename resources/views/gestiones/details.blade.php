@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-calendar"></i> {{ $head_title }}
        </h1>
        <a class="btn btn-secondary" href="{{ route('gestiones.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver
        </a>
    </div>

    @php
        $estado = match ($gestion->estado) {
            0 => 'ARCHIVADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $badgeClass = match ($gestion->estado) {
            0 => 'bg-secondary',
            1 => 'bg-success',
            default => 'bg-secondary',
        };
    @endphp

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-duotone fa-circle-info me-2"></i>Información de la Gestión</h5>
            <span class="badge {{ $badgeClass }} fs-6">{{ $estado }}</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="text-muted small text-uppercase">Año de Gestión</label>
                    <p class="fs-5 fw-semibold mb-0">{{ $gestion->anio }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-info"><i class="fa-solid fa-duotone fa-calendar-lines me-2"></i>Periodos</h5>
            <a class="btn btn-primary btn-sm" href="{{ route('periodos.index') }}">
                <i class="fa-solid fa-duotone fa-calendar-alt"></i> Gestionar Periodos
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable w-100" id="tabla-periodos">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Periodo</th>
                            <th>Posición ordinal</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gestion->periodos as $periodo)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $periodo->periodo }}</td>
                                <td>{{ $periodo->posicion_ordinal }}</td>
                                <td>
                                    @php
                                        $periodoEstado = match ($periodo->estado) {
                                            0 => 'ARCHIVADO',
                                            1 => 'ACTIVO',
                                            default => 'DESCONOCIDO',
                                        };
                                        $periodoBadgeClass = match ($periodo->estado) {
                                            0 => 'bg-secondary',
                                            1 => 'bg-success',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $periodoBadgeClass }}">{{ $periodoEstado }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-info"><i class="fa-solid fa-duotone fa-layer-group me-2"></i>Dimensiones</h5>
            <a class="btn btn-primary btn-sm" href="{{ route('dimensiones.index') }}">
                <i class="fa-solid fa-duotone fa-layer-plus"></i> Gestionar Dimensiones
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable w-100" id="tabla-dimensiones">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Dimensión</th>
                            <th>Posición ordinal</th>
                            <th>Puntaje máximo</th>
                            <th>Tipo de cálculo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gestion->dimensiones as $dimension)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $dimension->dimension }}</td>
                                <td>{{ $dimension->posicion_ordinal }}</td>
                                <td>{{ $dimension->puntaje_maximo }}</td>
                                <td>{{ strToUpper($dimension->tipo_calculo) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-info"><i class="fa-solid fa-duotone fa-object-group me-2"></i>Mallas Curriculares
            </h5>
            <a class="btn btn-primary btn-sm" href="{{ route('mallas_curriculares.index') }}">
                <i class="fa-solid fa-duotone fa-objects-column"></i> Gestionar Mallas
            </a>
        </div>
        <div class="card-body">
            <div class="alert border-info bg-info bg-opacity-10 text-info d-flex align-items-center mb-4 shadow-sm"
                role="alert">
                <i class="fa-solid fa-circle-info me-2 fs-5"></i>
                <div>
                    Cuando existen más de un registro por grado, se promedian las dos o más materias involucradas entre sí.
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable w-100" id="tabla-mallas">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Grado</th>
                            <th>P. ordinal de grado</th>
                            <th>Materia</th>
                            <th>Área</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gestion->mallas_curriculares as $malla_curricular)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $malla_curricular->grado->grado }}</td>
                                <td>{{ $malla_curricular->grado->posicion_ordinal }}</td>
                                <td>{{ $malla_curricular->materia->abreviatura }} -
                                    {{ $malla_curricular->materia->materia }}</td>
                                <td>{{ $malla_curricular->area->abreviatura }} - {{ $malla_curricular->area->area }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-info"><i class="fa-solid fa-duotone fa-clock me-2"></i>Horarios de Asignaturas</h5>
            <a class="btn btn-primary btn-sm" href="{{ route('horarios_asignaturas.index') }}">
                <i class="fa-solid fa-duotone fa-clock-rotate-left"></i> Gestionar Horarios
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable w-100" id="tabla-horarios">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nivel</th>
                            <th>P. ordinal de nivel</th>
                            <th>Denominación</th>
                            <th>Hora de inicio</th>
                            <th>Hora de fin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($gestion->horarios_asignaturas as $horario_asignatura)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $horario_asignatura->nivel->nivel }}</td>
                                <td>{{ $horario_asignatura->nivel->posicion_ordinal }}</td>
                                <td>{{ $horario_asignatura->denominacion }}</td>
                                <td>{{ $horario_asignatura->hora_inicio }}</td>
                                <td>{{ $horario_asignatura->hora_fin }}</td>
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
