@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-books fa-rotate-270 me-2"></i>{{ $head_title }}
        </h1>
        <a class="btn btn-secondary" href="{{ route('prestamos_libros.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info"><i class="fa-solid fa-duotone fa-user-graduate me-2"></i> 1. Datos del Prestatario</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="persona" class="form-label fw-bold">Selecciona la persona <span class="text-danger">*</span></label>
                    <select class="form-select" aria-label="Seleccione una persona" id="persona" name="persona_id" required>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="celular" class="form-label fw-bold">Número de celular</label>
                    <input type="text" class="form-control" id="celular" name="celular" placeholder="Ej. 77001122 (Opcional)">
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info"><i class="fa-solid fa-duotone fa-book-open-reader me-2"></i> 2. Catálogo de Libros</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped w-100" id="dataTable">
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
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-info"><i class="fa-solid fa-duotone fa-list-check me-2"></i> 3. Resumen y Devolución</h5>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="fecha_devolucion" class="form-label fw-bold">Fecha de Devolución <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="fecha_devolucion" name="fecha_devolucion" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                </div>
            </div>

            <h6 class="fw-bold text-secondary mb-3">Libros a Prestar</h6>
            <div class="table-responsive">
                <table class="table table-bordered table-striped mb-0" id="detalles">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th class="visually-hidden">Id</th>
                            <th width="15%">Código</th>
                            <th>Título</th>
                            <th width="10%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-end">
            <button type="button" class="btn btn-primary" id="btn-guardar">
                <i class="fa-solid fa-duotone fa-floppy-disk me-1"></i> Guardar Préstamo
            </button>
        </div>
    </div>
@endsection

@section('scripts')
    @include('prestamos_libros.create_scripts')
@endsection