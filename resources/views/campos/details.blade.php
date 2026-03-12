@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-layer-group"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-secondary mb-3" href="{{ route('campos.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver</a>

    <label for="campo">Campo:</label>
    <p class="form-control mb-3" id="campo">
        {{ $campo->campo }}
    </p>

    @php
        $estado = match ($campo->estado) {
            0 => 'ARCHIVADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $class = match ($campo->estado) {
            0 => 'alert alert-secondary',
            1 => 'alert alert-success',
            default => 'alert alert-secondary',
        };
    @endphp

    <div class="{{ $class }} fw-bold mb-3">
        Estado: {{ $estado }}
    </div>

    <h2 class="text-info fw-bold">Areas SIE</h2>

    <table class="table table-bordered table-striped mb-3 dataTable" id="areas">
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

    <div class="mb-3"></div>

    <h2 class="text-info fw-bold">Materias internas</h2>

    <table class="table table-bordered table-striped mb-3 dataTable" id="materias">
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
