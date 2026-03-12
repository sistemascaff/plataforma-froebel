@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-object-group"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-secondary mb-3" href="{{ route('areas.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver</a>

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

    <h2 class="text-info fw-bold mt-3">Mallas curriculares del área</h2>

    <p class="text-info">Cuando existen más de un registro por grado, se promedian las dos o más materias involucradas entre sí.</p>

    <table class="table table-bordered table-striped mb-3 dataTable" id="detalles">
        <thead>
            <tr>
                <th>#</th>
                <th>Grado</th>
                <th>P. Ordinal de grado</th>
                <th>Materia</th>
                <th>Gestión</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($area->mallas_curriculares as $malla_curricular)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $malla_curricular->grado->grado }}</td>
                    <td>{{ $malla_curricular->grado->posicion_ordinal }}</td>
                    <td>{{ $malla_curricular->materia->abreviatura }} - {{ $malla_curricular->materia->materia }}</td>
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
