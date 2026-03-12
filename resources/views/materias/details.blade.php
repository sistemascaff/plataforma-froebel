@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-object-group"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-secondary mb-3" href="{{ route('materias.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver</a>

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

    <h2 class="text-info fw-bold mt-3">Mallas curriculares de la materia</h2>

    <table class="table table-bordered table-striped mb-3 dataTable" id="detalles">
        <thead>
            <tr>
                <th>#</th>
                <th>Grado</th>
                <th>P. Ordinal de grado</th>
                <th>Área SIE</th>
                <th>Gestión</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($materia->mallas_curriculares as $malla_curricular)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $malla_curricular->grado->grado }}</td>
                    <td>{{ $malla_curricular->grado->posicion_ordinal }}</td>
                    <td>{{ $malla_curricular->area->abreviatura }} - {{ $malla_curricular->area->area }}</td>
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
