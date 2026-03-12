@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-object-group"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-secondary mb-3" href="{{ route('grados.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver</a>

    <label for="grado">Grado:</label>
    <p class="form-control mb-3" id="grado">
        {{ $grado->grado }}
    </p>

    <label for="posicion_ordinal">Posición ordinal:</label>
    <p class="form-control mb-3" id="posicion_ordinal">
        {{ $grado->posicion_ordinal }}
    </p>

    <label for="nivel">Nivel:</label>
    <p class="form-control mb-3" id="nivel">
        {{ $grado->nivel->nivel }}
    </p>

    @php
        $estado = match ($grado->estado) {
            0 => 'ARCHIVADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $class = match ($grado->estado) {
            0 => 'alert alert-secondary',
            1 => 'alert alert-success',
            default => 'alert alert-secondary',
        };
    @endphp

    <div class="{{ $class }} fw-bold mb-3">
        Estado: {{ $estado }}
    </div>

    <h2 class="text-info fw-bold">Malla curricular</h2>

    <table class="table table-bordered table-striped mb-3 dataTable" id="areas">
        <thead>
            <tr>
                <th>#</th>
                <th>Materia</th>
                <th>M. Abreviatura</th>
                <th>Area</th>
                <th>A. Abreviatura</th>
                <th>Gestión</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($grado->mallas_curriculares as $malla_curricular)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $malla_curricular->materia->materia }}</td>
                    <td>{{ $malla_curricular->materia->abreviatura }}</td>
                    <td>{{ $malla_curricular->area->area }}</td>
                    <td>{{ $malla_curricular->area->abreviatura }}</td>
                    <td>{{ $malla_curricular->gestion->anio }}</td>
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
