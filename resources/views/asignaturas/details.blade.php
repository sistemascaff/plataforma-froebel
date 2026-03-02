@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-star"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-secondary mb-3" href="{{ route('asignaturas.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver</a>

    <div></div>

    <label for="asignatura">Asignatura:</label>
    <p class="form-control mb-3" id="asignatura">
        {{ $asignatura->asignatura }}
    </p>

    <label for="tipo_calificacion">Tipo de calificación:</label>
    <p class="form-control mb-3" id="tipo_calificacion">
        {{ strtoupper($asignatura->tipo_calificacion) }}
    </p>

    <label for="tipo_bloque">Tipo de bloque:</label>
    <p class="form-control mb-3" id="tipo_bloque">
        {{ strtoupper($asignatura->tipo_bloque) }}
    </p>

    @php
        $estado = match ($asignatura->estado) {
            0 => 'ARCHIVADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $class = match ($asignatura->estado) {
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
