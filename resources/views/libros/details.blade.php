@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-book-open"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-secondary mb-3" href="{{ route('libros.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver</a>

    <div></div>

    <label for="titulo">Título:</label>
    <p class="form-control mb-3" id="titulo">
        {{ $libro->titulo }}
    </p>

    <label for="codigo">Código:</label>
    <p class="form-control mb-3" id="codigo">
        {{ $libro->codigo }}
    </p>

    <label for="autor">Autor:</label>
    <p class="form-control mb-3" id="autor">
        {{ $libro->autor }}
    </p>

    <label for="categoria">Categoría:</label>
    <p class="form-control mb-3" id="categoria">
        {{ $libro->categoria }}
    </p>

    <label for="editorial">Editorial:</label>
    <p class="form-control mb-3" id="editorial">
        {{ $libro->editorial }}
    </p>

    <label for="presentacion">Presentación:</label>
    <p class="form-control mb-3" id="presentacion">
        {{ $libro->presentacion }}
    </p>

    <label for="anio">Año:</label>
    <p class="form-control mb-3" id="anio">
        {{ $libro->anio }}
    </p>

    <label for="costo">Costo:</label>
    <p class="form-control mb-3" id="costo">
        {{ $libro->costo }}
    </p>

    <label for="descripcion">Descripción:</label>
    <p class="form-control mb-3" id="descripcion">
        {{ $libro->descripcion ?? '-' }}
    </p>

    <label for="adquisicion">Adquisición:</label>
    <p class="form-control mb-3" id="adquisicion">
        {{ $libro->adquisicion ? 'COMPRA' : 'DONACIÓN' }}
    </p>

    <label for="fecha_ingreso_cooperativa">F. Ingreso Cooperativa:</label>
    <p class="form-control mb-3" id="fecha_ingreso_cooperativa">
        {{ date('d/m/Y', strtotime($libro->fecha_ingreso_cooperativa)) }}
    </p>

    <label for="observacion">Observación:</label>
    <p class="form-control mb-3" id="observacion">
        {{ $libro->observacion ?? '-' }}
    </p>

    @php
        $estado = match ($libro->estado) {
            0 => 'BAJA',
            1 => 'DISPONIBLE',
            2 => 'EN USO',
            default => 'DESCONOCIDO',
        };
        $class = match ($libro->estado) {
            0 => 'alert alert-secondary',
            1 => 'alert alert-success',
            2 => 'alert alert-primary',
            default => 'alert alert-secondary',
        };
    @endphp

    <div class="{{ $class }} fw-bold mb-3">
        Estado: {{ $estado }}
    </div>


    <h2 class="text-info fw-bold">Historial de préstamos</h2>

    <table class="table table-bordered table-striped mb-3 dataTable" id="detalles">
        <thead>
            <tr>
                <th>#</th>
                <th>Nro. Préstamo</th>
                <th>Persona</th>
                <th>F. Retorno</th>
                <th>F. Devolución</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($libro->prestamos_libros as $prestamo)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $prestamo->pivot->id_prestamo_libro }}</td>
                    <td>{{ trim('(' . $prestamo->persona->tipo_perfil . ') ' . $prestamo->persona->apellido_paterno . ' ' . $prestamo->persona->apellido_materno . ' ' . $prestamo->persona->nombres) }}
                    </td>
                    <td>
                        @if (!$prestamo->estado)
                            <b class="text-danger">Anulado</b>
                        @else
                            @if (!$prestamo->pivot->fecha_retorno)
                                <b class="text-primary">En uso</b>
                            @else
                                {{ date('d/m/Y H:i:s', strtotime($prestamo->pivot->fecha_retorno)) }}
                            @endif
                        @endif
                    </td>
                    <td>{{ date('d/m/Y', strtotime($prestamo->fecha_devolucion)) }}</td>
                    <td>
                        <a class="btn btn-info"
                            href="{{ route('prestamos_libros.detalles', $prestamo->pivot->id_prestamo_libro) }}">
                            <i class="fa-solid fa-duotone fa-eye"></i></a>
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
            $(".dataTable").DataTable({
                responsive: true,
                lengthChange: true,
                autoWidth: true,
                colReorder: true,
                order: [
                    [0, 'desc'],
                ],
                pageLength: 10,
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
