@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h1 class="text-info fw-bold mb-0 me-auto">
            <i class="fa-solid fa-duotone fa-books fa-rotate-270 me-2"></i>{{ $head_title }}
        </h1>
        
        <div class="d-flex gap-2">
            <a class="btn btn-secondary" href="{{ route('prestamos_libros.index') }}">
                <i class="fa-solid fa-duotone fa-arrow-left me-1"></i> Volver
            </a>
            <a class="btn btn-primary" href="{{ route('prestamos_libros.imprimir', $prestamo_libro->id_prestamo_libro) }}" target="_blank" rel="noopener noreferrer">
                <i class="fa-solid fa-duotone fa-print me-1"></i> Imprimir
            </a>
            <a class="btn btn-info" href="{{ route('prestamos_libros.detalles', $prestamo_libro->id_prestamo_libro) }}">
                <i class="fa-solid fa-duotone fa-eye me-1"></i> Detalles
            </a>
            <button type="button" class="btn btn-danger" id="btnDelete" {{ $prestamo_libro->estado == 0 ? 'disabled' : '' }}>
                <i class="fa-solid fa-duotone fa-file-xmark me-1"></i> Anular
            </button>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info"><i class="fa-solid fa-duotone fa-user-graduate me-2"></i> 1. Datos del Prestatario</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <label for="persona" class="form-label fw-bold">Selecciona la persona <span class="text-danger">*</span></label>
                    <select class="form-select" aria-label="Seleccione una persona" id="persona" name="persona_id" required {{ $prestamo_libro->estado == 0 ? 'disabled' : '' }}>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="celular" class="form-label fw-bold">Número de celular</label>
                    <input type="text" class="form-control" id="celular" name="celular" placeholder="Ej. 77001122 (Opcional)" value="{{ $prestamo_libro->celular ?? '' }}" {{ $prestamo_libro->estado == 0 ? 'disabled' : '' }}>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold text-info"><i class="fa-solid fa-duotone fa-book-open-reader me-2"></i> 2. Catálogo de Libros</h5>
            @if ($prestamo_libro->estado == 0)
                <span class="badge bg-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> Préstamo Anulado</span>
            @endif
        </div>
        <div class="card-body">
            @if ($prestamo_libro->estado != 0)
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
            @else
                <div class="alert alert-danger mb-0" role="alert">
                    <i class="fa-solid fa-duotone fa-exclamation-triangle me-2"></i>
                    No se pueden agregar más libros porque el préstamo ha sido anulado.
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info"><i class="fa-solid fa-duotone fa-list-check me-2"></i> 3. Resumen y Devolución</h5>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="fecha_devolucion" class="form-label fw-bold">Fecha de Devolución <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="fecha_devolucion" name="fecha_devolucion" value="{{ date('Y-m-d', strtotime($prestamo_libro->fecha_devolucion ?? '+7 days')) }}" required {{ $prestamo_libro->estado == 0 ? 'disabled' : '' }}>
                </div>
            </div>

            <h6 class="fw-bold text-secondary mb-3">Libros Prestados</h6>
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-striped mb-0" id="detalles">
                    <thead>
                        <tr>
                            <th width="5%">#</th>
                            <th class="visually-hidden">Id</th>
                            <th width="15%">Código</th>
                            <th>Título</th>
                            <th width="15%">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prestamo_libro->libros as $libro)
                            <tr>
                                <td class="fw-bold text-primary align-middle">{{ $loop->iteration }}</td>
                                <td class="visually-hidden id_libro">{{ $libro->id_libro }}</td>
                                <td class="codigo align-middle">{{ $libro->codigo }}</td>
                                <td class="titulo align-middle">{{ $libro->titulo }}</td>
                                <td class="align-middle text-center">
                                    @if ($prestamo_libro->estado != 0)
                                        @if ($libro->pivot->fecha_retorno == null)
                                            <button type="button" class="btn btn-danger btn-sm btn-remover" data-id="{{ $libro->id_libro }}" data-toggle="tooltip" title="Remover">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @else
                                            <b class="text-success small">Devuelto el <br>{{ date('d/m/Y', strtotime($libro->pivot->fecha_retorno)) }} <i class="fa fa-check"></i></b>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($prestamo_libro->estado == 0)
                <div class="alert alert-danger mb-0" role="alert">
                    <i class="fa-solid fa-duotone fa-exclamation-triangle me-2"></i>
                    No se puede editar la información porque el préstamo ha sido anulado. 
                </div>
            @endif
        </div>
        
        @if ($prestamo_libro->estado != 0)
            <div class="card-footer text-end">
                <button type="button" class="btn btn-warning" id="btn-guardar">
                    <i class="fa-solid fa-duotone fa-floppy-disk me-1"></i> Guardar Cambios
                </button>
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    @include('prestamos_libros.update_scripts')
@endsection