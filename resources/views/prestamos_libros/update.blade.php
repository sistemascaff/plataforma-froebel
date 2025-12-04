@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-books fa-rotate-270"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-secondary mb-3" href="{{ route('prestamos_libros.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver</a>

    <a class="btn btn-primary mb-3 ms-2" href="{{ route('prestamos_libros.imprimir', $prestamo_libro->id_prestamo_libro) }}"
        target="_blank" rel="noopener noreferrer">
        <i class="fa-solid fa-duotone fa-print"></i> Imprimir</a>

    <a class="btn btn-info mb-3 ms-2" href="{{ route('prestamos_libros.detalles', $prestamo_libro->id_prestamo_libro) }}">
        <i class="fa-solid fa-duotone fa-eye"></i> Detalles</a>

    <button type="button" class="btn btn-danger mb-3 ms-2" id="btnDelete"
        {{ $prestamo_libro->estado == 0 ? 'disabled' : '' }}>
        <i class="fa-solid fa-duotone fa-file-xmark"></i>
        Anular</button>

    <h2 class="text-info fw-bold">1. Selecciona la persona</h2>

    <div>
        <select class="form-select" aria-label="Seleccione una persona" id="persona" name="persona_id" required
            {{ $prestamo_libro->estado == 0 ? 'disabled' : '' }}>
        </select>
    </div>

    <div class="mb-3"></div>

    <h2 class="text-info fw-bold">2. Indica su número de celular</h2>
    <input type="text" class="form-control" id="celular" name="celular" placeholder="Número de celular (opcional)"
        value="{{ $prestamo_libro->celular ?? '' }}" {{ $prestamo_libro->estado == 0 ? 'disabled' : '' }}>

    <h2 class="text-info fw-bold">2. Selecciona los libros</h2>

    <!-- Esta tabla es igual a la de libros.index pero sin los atributos de auditoría -->
    @if ($prestamo_libro->estado != 0)
        <table class="table table-bordered table-striped" id="dataTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Categoria</th>
                    <th>Código</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Editorial</th>
                    <th>Año</th>
                    <th>Descripción</th>
                    <th>Costo</th>
                    <th>Adquisición</th>
                    <th>Presentación</th>
                    <th>Observación</th>
                    <th>F. Ingreso Cooperativa</th>
                    <th>Prestado a</th>
                    <th>Cant. Préstamos</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
        </table>
    @else
        <div class="alert alert-danger" role="alert">
            <i class="fa-solid fa-duotone fa-exclamation-triangle"></i>
            No se pueden seleccionar libros porque el préstamo ha sido anulado.
        </div>
    @endif

    <div class="mb-3"></div>

    <h2 class="text-info fw-bold">3. Indica la fecha de devolución</h2>

    <input type="date" class="form-control" id="fecha_devolucion" name="fecha_devolucion"
        value="{{ date('Y-m-d', strtotime($prestamo_libro->fecha_devolucion ?? '+7 days')) }}" required
        {{ $prestamo_libro->estado == 0 ? 'disabled' : '' }}>

    <div class="mb-3"></div>

    <h2 class="text-info fw-bold">Detalles</h2>

    <table class="table table-bordered table-striped mb-3" id="detalles">
        <thead>
            <tr>
                <th>#</th>
                <th class="visually-hidden">Id</th>
                <th>Código</th>
                <th>Título</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($prestamo_libro->libros as $libro)
                <tr>
                    <td class="fw-bold text-primary">{{ $loop->iteration }}</td>
                    <td class="visually-hidden id_libro">{{ $libro->id_libro }}</td>
                    <td class="codigo">{{ $libro->codigo }}</td>
                    <td class="titulo">{{ $libro->titulo }}</td>
                    <td>
                        @if ($prestamo_libro->estado != 0)
                            @if ($libro->pivot->fecha_retorno == null)
                                <button type="button" class="btn btn-danger btn-sm btn-remover"
                                    data-id="{{ $libro->id_libro }}" data-toggle="tooltip" title="Remover">
                                    <i class="fa fa-trash"></i>
                                </button>
                            @else
                                <b class="text-success">Devuelto el
                                    {{ date('d/m/Y', strtotime($libro->pivot->fecha_retorno)) }}
                                    <i class="fa fa-check"></i></b>
                            @endif
                        @else
                            -
                        @endif

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if ($prestamo_libro->estado != 0)
        <button type="button" class="btn btn-warning" id="btnSave" {{ $prestamo_libro->estado == 0 ? 'disabled' : '' }}>
            <i class="fa-solid fa-duotone fa-floppy-disk"></i> Guardar
        </button>
    @else
        <div class="alert alert-danger" role="alert">
            <i class="fa-solid fa-duotone fa-exclamation-triangle"></i>
            No se puede editar este préstamo de libros porque el préstamo ha sido anulado. 
        </div>
    @endif

    <div class="mb-3"></div>
@endsection

@section('scripts')
    @include('prestamos_libros.update_scripts')
@endsection
