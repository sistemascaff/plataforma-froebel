@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-object-group"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-secondary mb-3" href="{{ route('areas.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver</a>

    <div></div>

    <label for="area">Área:</label>
    <p class="form-control mb-3" id="area">
        {{ $area->area }}
    </p>

    <label for="abreviatura">Abreviatura:</label>
    <p class="form-control mb-3" id="abreviatura">
        {{ $area->abreviatura }}
    </p>

    <label for="posicion_ordinal">Posición ordinal:</label>
    <p class="form-control mb-3" id="posicion_ordinal">
        {{ $area->posicion_ordinal }}
    </p>

    @php
        $estado = match ($area->estado) {
            0 => 'ARCHIVADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $class = match ($area->estado) {
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
