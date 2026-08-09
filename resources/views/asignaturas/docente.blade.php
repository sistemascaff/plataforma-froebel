@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold">
        <i class="fa-solid fa-duotone fa-book-reader me-1"></i>{{ $head_title }}
    </h1>

    <h2 class="text-info fw-bold">Lista de asignaturas</h2>

    <p class="text-justify">
        Las asignaturas son la representación de las clases que los estudiantes cursan, cada asignatura está asociada a una
        <a href="{{ route('materias.index') }}">materia (interna)</a> y un <a href="{{ route('areas.index') }}">área
            (SIE)</a>, además de otros datos como el tipo de calificación, el tipo de bloque, el aula donde se imparte, el
        nivel al que pertenece y la coordinación encargada.
    </p>

    <div class="card p-3 mb-3">
        <p>Seleccione una opción para <i class="fa-solid fa-duotone fa-file-export"></i> exportar o <i
                class="fa-solid fa-duotone fa-filter"></i> filtrar la tabla:</p>
        <div id="dataTable-export-buttons-container"></div>
    </div>

    <table class="table table-bordered table-striped" id="dataTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Asignatura</th>
                <th>Tipo de calificación</th>
                <th>Tipo de bloque</th>
                <th>Materia</th>
                <th>Área</th>
                <th>Aula</th>
                <th>Nivel</th>
                <th>Coordinación</th>
                <th>Curso</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($asignaturas as $asignatura)
                @php
                    $estado = match ($asignatura->estado) {
                        1 => '<span class="badge bg-success">ACTIVO</span>',
                        0 => '<span class="badge bg-secondary">ARCHIVADO</span>',
                        default => '<span class="badge bg-warning">DESCONOCIDO</span>',
                    };

                    $tipo_calificacion_i =
                        $asignatura->tipo_calificacion === 'cualitativa' ? 'fa-comments' : 'fa-chart-column';
                    $tipo_bloque_bg = $asignatura->tipo_bloque === 'curso' ? 'bg-primary' : 'bg-danger';
                @endphp
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $asignatura->asignatura }}</td>
                    <td>
                        <span class="badge bg-info text-dark"><i
                                class="fa-solid fa-duotone {{ $tipo_calificacion_i }} me-1"></i>
                            {{ strtoupper($asignatura->tipo_calificacion) }}
                    </td>
                    <td>
                        <span class="badge {{ $tipo_bloque_bg }}">{{ strtoupper($asignatura->tipo_bloque) }}</span>
                    </td>
                    <td>{{ $asignatura->materia->materia }}</td>
                    <td>{{ $asignatura->area->area }}</td>
                    <td>{{ $asignatura->aula->aula }}</td>
                    <td>{{ $asignatura->nivel?->nivel }}</td>
                    <td>{{ $asignatura->coordinacion?->coordinacion }}</td>
                    <td>{{ $asignatura->curso->curso }}</td>
                    <td>{!! $estado !!}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <a class="btn btn-info btn-sm"
                                href="{{ route('asignaturas.detalles', $asignatura->id_asignatura) }}" target="_blank"
                                rel="noopener noreferrer" data-toggle="tooltip" title="Detalles">
                                <i class="fa-duotone fa-solid fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mb-3"></div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $("#dataTable").DataTable({
                @include('components.datatables.datatables_global_properties')
                @include('components.datatables.datatables_language_property')
            }).buttons().container().appendTo('#dataTable-export-buttons-container');
        });
    </script>
@endsection
