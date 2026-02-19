@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-object-group"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-secondary mb-3" href="{{ route('materias.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver</a>

    <div></div>

    <label for="materia">Materia:</label>
    <p class="form-control mb-3" id="materia">
        {{ $materia->materia }}
    </p>

    <label for="abreviatura">Abreviatura:</label>
    <p class="form-control mb-3" id="abreviatura">
        {{ $materia->abreviatura }}
    </p>

    <label for="posicion_ordinal">Posición ordinal:</label>
    <p class="form-control mb-3" id="posicion_ordinal">
        {{ $materia->posicion_ordinal }}
    </p>

    @php
        $estado = match ($materia->estado) {
            0 => 'ARCHIVADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $class = match ($materia->estado) {
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
