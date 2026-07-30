@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h1 class="text-info fw-bold mb-0 me-auto">
            <i class="fa-solid fa-duotone fa-book-open me-2"></i>{{ $head_title }}
        </h1>
        <a class="btn btn-secondary" href="{{ route('libros.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left me-1"></i> Volver al Inventario
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-info">
                        <i class="fa-solid fa-duotone fa-heading me-2"></i> Datos Principales del Libro
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <p class="text-muted small fw-bold mb-1"><i class="fa-solid fa-book me-1"></i> Título Completo</p>
                        <h3 class="fw-bold text-primary mb-0 text-break" id="titulo">{{ $libro->titulo }}</h3>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <p class="text-muted small fw-bold mb-1"><i class="fa-solid fa-feather-pointed me-1"></i> Autor</p>
                            <p class="fs-5 mb-0" id="autor">{{ $libro->autor }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-muted small fw-bold mb-1"><i class="fa-solid fa-tags me-1"></i> Categoría / Género</p>
                            <p class="fs-5 mb-0" id="categoria">{{ $libro->categoria }}</p>
                        </div>
                    </div>

                    <hr class="my-4 text-muted">

                    <div>
                        <p class="text-muted small fw-bold mb-1"><i class="fa-solid fa-align-left me-1"></i> Descripción o Resumen</p>
                        <p class="fs-6 mb-0 text-break text-muted text-opacity-75" id="descripcion">
                            {{ $libro->descripcion ?: 'Sin descripción registrada en el sistema.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold text-info">
                        <i class="fa-solid fa-duotone fa-paste me-2"></i> Ficha Técnica e Inventario
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <p class="text-muted small fw-bold mb-1"><i class="fa-solid fa-barcode me-1"></i> Código Único</p>
                            <p class="fs-5 fw-bold text-info mb-0" id="codigo">{{ $libro->codigo }}</p>
                        </div>
                        <div class="col-6">
                            <p class="text-muted small fw-bold mb-1"><i class="fa-solid fa-calendar me-1"></i> Año Edición</p>
                            <p class="fs-5 mb-0" id="anio">{{ $libro->anio }}</p>
                        </div>
                        <div class="col-6">
                            <p class="text-muted small fw-bold mb-1"><i class="fa-solid fa-print me-1"></i> Editorial</p>
                            <p class="fs-6 mb-0" id="editorial">{{ $libro->editorial }}</p>
                        </div>
                        <div class="col-6">
                            <p class="text-muted small fw-bold mb-1"><i class="fa-solid fa-book-medical me-1"></i> Presentación</p>
                            <p class="fs-6 mb-0" id="presentacion">{{ $libro->presentacion }}</p>
                        </div>
                        <div class="col-6">
                            <p class="text-muted small fw-bold mb-1"><i class="fa-solid fa-hand-holding-dollar me-1"></i> Costo Unitario</p>
                            <p class="fs-6 mb-0 text-success fw-bold" id="costo">{{ $libro->costo }}</p>
                        </div>
                        <div class="col-6">
                            <p class="text-muted small fw-bold mb-1"><i class="fa-solid fa-boxes-packing me-1"></i> Adquisición</p>
                            <p class="fs-6 mb-0" id="adquisicion">{{ $libro->adquisicion == 1 ? 'COMPRA' : 'DONACIÓN' }}</p>
                        </div>
                    </div>

                    <hr class="my-3 text-muted">

                    <div class="mb-3">
                        <p class="text-muted small fw-bold mb-1"><i class="fa-solid fa-circle-exclamation me-1"></i> Observación Interna</p>
                        <p class="fs-6 mb-0 text-warning" id="observacion">
                            {{ $libro->observacion ?: 'Ninguna observación física sobre este ejemplar.' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-muted small fw-bold mb-1"><i class="fa-solid fa-calendar-check me-1"></i> Ingreso a Cooperativa</p>
                        <p class="fs-6 mb-0 text-secondary" id="fecha_ingreso_cooperativa">
                            {{ date('d/m/Y', strtotime($libro->fecha_ingreso_cooperativa)) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info">
                <i class="fa-solid fa-duotone fa-clock-rotate-left me-2"></i> Historial de Préstamos de este Libro
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table class="table table-bordered table-striped dataTable w-100 mb-0">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">#</th>
                            <th>Persona</th>
                            <th>Curso</th>
                            <th>Celular</th>
                            <th>F. Préstamo</th>
                            <th>F. Devolución Estipulada</th>
                            <th>F. Retorno Real</th>
                            <th>Estado Préstamo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($libro->prestamos_libros as $prestamo)
                            <tr>
                                <td class="fw-bold text-primary text-center align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle">
                                    {{ "({$prestamo->persona->tipo_perfil}) {$prestamo->persona->apellidos_nombres}" }}
                                </td>
                                <td class="align-middle">{{ $prestamo->curso ?: '-' }}</td>
                                <td class="align-middle">{{ $prestamo->celular ?: '-' }}</td>
                                <td class="align-middle">{{ date('d/m/Y H:i', strtotime($prestamo->fecha_registro)) }}</td>
                                <td class="align-middle">{{ date('d/m/Y', strtotime($prestamo->fecha_devolucion)) }}</td>
                                <td class="align-middle">
                                    @if ($prestamo->pivot->fecha_retorno)
                                        <span class="text-success fw-bold">{{ date('d/m/Y H:i', strtotime($prestamo->pivot->fecha_retorno)) }}</span>
                                    @else
                                        <span class="badge bg-danger">AÚN PRESTADO</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    @if ($prestamo->estado == 1)
                                        <span class="badge bg-success">ACTIVO</span>
                                    @else
                                        <span class="badge bg-secondary">ANULADO</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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