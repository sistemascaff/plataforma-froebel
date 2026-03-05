@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-calendar"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-secondary mb-3" href="{{ route('gestiones.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver</a>

    <div></div>

    <label for="anio">Año:</label>
    <p class="form-control mb-3" id="anio">
        {{ $gestion->anio }}
    </p>

    @php
        $estado = match ($gestion->estado) {
            0 => 'ARCHIVADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $class = match ($gestion->estado) {
            0 => 'alert alert-secondary',
            1 => 'alert alert-success',
            default => 'alert alert-secondary',
        };
    @endphp

    <div class="{{ $class }} fw-bold mb-3">
        Estado: {{ $estado }}
    </div>


    <h2 class="text-info fw-bold">Periodos</h2>

    <a class="btn btn-primary mb-3" href="{{ route('periodos.index') }}">
        <i class="fa-solid fa-duotone fa-calendar-alt"></i> Ir a periodos
    </a>

    <table class="table table-bordered table-striped mb-3 dataTable" id="periodos">
        <thead>
            <tr>
                <th>#</th>
                <th>Periodo</th>
                <th>Posición ordinal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($gestion->periodos as $periodo)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $periodo->periodo }}</td>
                    <td>{{ $periodo->posicion_ordinal }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mb-3"></div>

    <h2 class="text-info fw-bold">Dimensiones</h2>

    <a class="btn btn-primary mb-3" href="{{ route('dimensiones.index') }}">
        <i class="fa-solid fa-duotone fa-layer-group"></i> Ir a dimensiones
    </a>

    <table class="table table-bordered table-striped mb-3 dataTable" id="periodos">
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

    <div class="mb-3"></div>

    <h2 class="text-info fw-bold">Mallas curriculares</h2>

    <a class="btn btn-primary mb-3" href="{{ route('mallas_curriculares.index') }}">
        <i class="fa-solid fa-duotone fa-object-group"></i> Ir a mallas curriculares
    </a>

    <p class="text-info">Cuando existen más de un registro por grado, se promedian las dos o más materias involucradas entre sí.</p>

    <table class="table table-bordered table-striped mb-3 dataTable" id="periodos">
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
                    <td>{{ $malla_curricular->materia->abreviatura }} - {{ $malla_curricular->materia->materia }}</td>
                    <td>{{ $malla_curricular->area->abreviatura }} - {{ $malla_curricular->area->area }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mb-3"></div>

    <h2 class="text-info fw-bold">Horarios de asignaturas</h2>

    <a class="btn btn-primary mb-3" href="{{ route('horarios_asignaturas.index') }}">
        <i class="fa-solid fa-duotone fa-clock"></i> Ir a horarios de asignaturas
    </a>

    <table class="table table-bordered table-striped mb-3 dataTable" id="periodos">
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

    <div class="mb-3"></div>
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
