@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-clock"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-secondary mb-3" href="{{ route('horarios_asignaturas.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver</a>

    <div></div>

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
