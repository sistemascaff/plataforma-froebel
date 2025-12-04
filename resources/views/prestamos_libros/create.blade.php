@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-books fa-rotate-270"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-secondary mb-3" href="{{ route('prestamos_libros.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver</a>

    <h2 class="text-info fw-bold">1. Selecciona la persona</h2>

    <div>
        <select class="form-select" aria-label="Seleccione una persona" id="persona" name="persona_id" required>
        </select>
    </div>

    <div class="mb-3"></div>

    <h2 class="text-info fw-bold">2. Indica su número de celular</h2>
    <input type="text" class="form-control" id="celular" name="celular"
        placeholder="Número de celular (opcional)">

    <h2 class="text-info fw-bold">2. Selecciona los libros</h2>

    <!-- Esta tabla es igual a la de libros.index pero sin los atributos de auditoría -->
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

    <div class="mb-3"></div>

    <h2 class="text-info fw-bold">3. Indica la fecha de devolución</h2>

    <input type="date" class="form-control" id="fecha_devolucion" name="fecha_devolucion"
        value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>

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

        </tbody>
    </table>

    <button type="button" class="btn btn-primary" id="btnSave"><i class="fa-solid fa-duotone fa-floppy-disk"></i>
        Guardar</button>

    <div class="mb-3"></div>
@endsection

@section('scripts')
    @include('prestamos_libros.create_scripts')
@endsection
