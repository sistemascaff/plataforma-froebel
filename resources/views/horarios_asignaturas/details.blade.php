@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-clock"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-secondary mb-3" href="{{ route('horarios_asignaturas.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver</a>

    <label for="denominacion">Denominación:</label>
    <p class="form-control mb-3" id="denominacion">
        {{ $horario_asignatura->denominacion }}
    </p>

    <label for="hora_inicio">Hora de inicio:</label>
    <p class="form-control mb-3" id="hora_inicio">
        {{ $horario_asignatura->hora_inicio }}
    </p>

    <label for="hora_fin">Hora de fin:</label>
    <p class="form-control mb-3" id="hora_fin">
        {{ $horario_asignatura->hora_fin }}
    </p>

    <label for="nivel">Nivel:</label>
    <p class="form-control mb-3" id="nivel">
        {{ $horario_asignatura->nivel->nivel }}
    </p>

    <label for="gestion">Gestión:</label>
    <p class="form-control mb-3" id="gestion">
        {{ $horario_asignatura->gestion->anio }}
    </p>

    @php
        $estado = match ($horario_asignatura->estado) {
            0 => 'ARCHIVADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $class = match ($horario_asignatura->estado) {
            0 => 'alert alert-secondary',
            1 => 'alert alert-success',
            default => 'alert alert-secondary',
        };
    @endphp

    <div class="{{ $class }} fw-bold mb-3">
        Estado: {{ $estado }}
    </div>

    <h2 class="text-info fw-bold">Asignaturas dentro del horario</h2>

    <table class="table table-bordered table-striped mb-3 dataTable" id="detalles">
        <thead>
            <tr>
                <th>#</th>
                <th>Día</th>
                <th>Asignatura</th>
                <th>Tipo de calificación</th>
                <th>Tipo de bloque</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($horario_asignatura->asignaturas as $asignatura)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ helper_dia_semana_a_nombre($asignatura->pivot->dia_semana) }}</td>
                    <td>{{ $asignatura->asignatura }}</td>
                    <td>{{ strtoupper($asignatura->tipo_calificacion) }}</td>
                    <td>{{ strtoupper($asignatura->tipo_bloque) }}</td>
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
                @include('components.datatables.datatables_global_properties')
                @include('components.datatables.datatables_language_property')
            });
        });
    </script>
@endsection
