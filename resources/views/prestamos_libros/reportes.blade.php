@extends('layouts.app')

@section('content')
    @php
        $total = 0;
    @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h1 class="text-info fw-bold mb-0 me-auto">
            <i class="fa-solid fa-duotone fa-chart-pie me-2"></i> Reportes de Biblioteca
        </h1>
        <a class="btn btn-secondary" href="{{ route('prestamos_libros.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="alert alert-info border-info border-start border-4 shadow-sm mb-4 d-flex align-items-center" role="alert">
        <i class="fa-solid fa-duotone fa-circle-info fa-2x me-3"></i>
        <div>
            <h5 class="alert-heading fw-bold mb-1">Información del Reporte</h5>
            <p class="mb-0">
                Por defecto, se muestra la información de los <b>últimos 3 meses</b>. Puedes usar los filtros para consultar
                fechas específicas.<br>
                <small>Nota: El reporte está basado principalmente en la cantidad de libros.</small>
            </p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm h-100">
                <div class="card-header">
                    <h5 class="mb-0 fw-bold text-info"><i class="fa-solid fa-duotone fa-calendar-days me-2"></i> Filtros de
                        Búsqueda</h5>
                </div>
                <div class="card-body">
                    <form class="row g-3" method="GET">
                        <div class="col-md-5">
                            <label for="fecha_inicio" class="form-label fw-bold">Fecha inicio</label>
                            <input type="date" name="fecha_inicio" class="form-control"
                                value="{{ $fecha_inicio ?? date('Y-m-d', strtotime('-3 months')) }}"
                                max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-5">
                            <label for="fecha_fin" class="form-label fw-bold">Fecha fin</label>
                            <input type="date" name="fecha_fin" class="form-control"
                                value="{{ date('Y-m-d', strtotime($fecha_fin)) ?? date('Y-m-d') }}"
                                max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-12 d-flex gap-2 mt-4">
                            <button type="submit" formaction="{{ route('prestamos_libros.reportes') }}"
                                class="btn btn-info flex-grow-1">
                                <i class="fa-solid fa-duotone fa-search me-1"></i> Generar Reporte
                            </button>
                            <button type="submit" formaction="{{ route('prestamos_libros.reportes.imprimir') }}"
                                formtarget="_blank" class="btn btn-primary flex-grow-1">
                                <i class="fa-solid fa-duotone fa-print me-1"></i> Imprimir PDF
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm h-100 border-info">
                <div
                    class="card-body d-flex flex-column justify-content-center align-items-center bg-info bg-opacity-10 text-center rounded">
                    <i class="fa-solid fa-duotone fa-books fa-rotate-270 fa-3x text-info mb-3"></i>
                    <h5 class="text-muted fw-bold mb-1">Total Libros Prestados</h5>
                    <h1 class="display-3 fw-bold text-info mb-0">{{ $libros_mas_prestados->sum('total') }}</h1>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info"><i class="fa-solid fa-duotone fa-list-ol me-2"></i> Índice del Reporte</h5>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <a href="#detalle" class="btn btn-outline-info btn-sm fw-bold">1. Detalle</a>
                <a href="#libros_mas_prestados" class="btn btn-outline-info btn-sm fw-bold">2. Más Prestados</a>
                <a href="#prestamos_por_categoria" class="btn btn-outline-info btn-sm fw-bold">3. Por Categoría</a>
                <a href="#prestamos_por_curso" class="btn btn-outline-info btn-sm fw-bold">4. Por Curso</a>
                <a href="#prestamos_por_tipo_perfil" class="btn btn-outline-info btn-sm fw-bold">5. Por Perfil</a>
                <a href="#prestamos_por_persona" class="btn btn-outline-info btn-sm fw-bold">6. Por Persona</a>
                <a href="#pendientes_hasta_hoy" class="btn btn-outline-danger btn-sm fw-bold">7. Pendientes de
                    Devolución</a>
                <a href="#relacion_prestamos_devoluciones" class="btn btn-outline-info btn-sm fw-bold">8. Préstamos vs
                    Devueltos</a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4" id="detalle">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info">1. Detalle de Préstamos</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table class="table table-bordered table-striped dataTable w-100 mb-0">
                    <thead>
                        <tr>
                            <th>N° Préstamo</th>
                            <th>Código</th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Editorial</th>
                            <th>Prestado a</th>
                            <th>Curso</th>
                            <th>F. Registro</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prestamos_libros as $prestamo_libro)
                            @foreach ($prestamo_libro->libros as $libro)
                                <tr>
                                    <td class="align-middle text-center fw-bold">{{ $prestamo_libro->id_prestamo_libro }}
                                    </td>
                                    <td class="align-middle">{{ $libro->codigo }}</td>
                                    <td class="align-middle">{{ $libro->titulo }}</td>
                                    <td class="align-middle">{{ $libro->autor }}</td>
                                    <td class="align-middle">{{ $libro->editorial }}</td>
                                    <td class="align-middle">
                                        {{ trim('(' . $prestamo_libro->persona->tipo_perfil . ') ' . $prestamo_libro->persona->apellido_paterno . ' ' . $prestamo_libro->persona->apellido_materno . ' ' . $prestamo_libro->persona->nombres) }}
                                    </td>
                                    <td class="align-middle">{{ $prestamo_libro->curso }}</td>
                                    <td class="align-middle">
                                        {{ date('d/m/Y H:i', strtotime($prestamo_libro->fecha_registro)) }}</td>
                                    <td class="align-middle text-center">
                                        <a class="btn btn-info btn-sm"
                                            href="{{ route('prestamos_libros.detalles', $prestamo_libro->id_prestamo_libro) }}"
                                            target="_blank" rel="noopener noreferrer" data-bs-toggle="tooltip"
                                            title="Ver Detalles">
                                            <i class="fa-solid fa-duotone fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @php $total++; @endphp
                            @endforeach
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-info fw-bold">
                            <td colspan="8" class="text-end">Total de registros:</td>
                            <td class="text-center fs-5">{{ $total }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4" id="libros_mas_prestados">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info">2. Libros Más Prestados</h5>
        </div>
        <div class="card-body">
            <div class="border border-info rounded mb-4 p-3">
                <canvas id="chart-libros-mas-prestados"></canvas>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable w-100 mb-0">
                    <thead>
                        <tr>
                            <th width="5%">N°</th>
                            <th>Libro</th>
                            <th>Categoría</th>
                            <th width="15%" class="text-center">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($libros_mas_prestados as $libro_mas_prestado)
                            <tr>
                                <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                <td>{{ $libro_mas_prestado->titulo }}</td>
                                <td>{{ $libro_mas_prestado->categoria }}</td>
                                <td class="text-center fw-bold text-primary">{{ $libro_mas_prestado->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-info fw-bold">
                            <td colspan="3" class="text-end">Total:</td>
                            <td class="text-center">{{ $libros_mas_prestados->sum('total') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4" id="prestamos_por_categoria">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info">3. Préstamos por Categoría</h5>
        </div>
        <div class="card-body">
            <div class="border border-info rounded mb-4 p-3">
                <canvas id="chart-prestamos-por-categoria"></canvas>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable w-100 mb-0">
                    <thead>
                        <tr>
                            <th width="5%">N°</th>
                            <th>Categoría</th>
                            <th width="15%" class="text-center">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prestamos_por_categoria as $categoria)
                            <tr>
                                <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                <td>{{ $categoria->categoria }}</td>
                                <td class="text-center fw-bold text-primary">{{ $categoria->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-info fw-bold">
                            <td colspan="2" class="text-end">Total:</td>
                            <td class="text-center">{{ $prestamos_por_categoria->sum('total') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4" id="prestamos_por_curso">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info">4. Préstamos por Curso</h5>
        </div>
        <div class="card-body">
            <div class="border border-info rounded mb-4 p-3">
                <canvas id="chart-prestamos-por-curso"></canvas>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable w-100 mb-0">
                    <thead>
                        <tr>
                            <th width="5%">N°</th>
                            <th>Curso</th>
                            <th width="15%" class="text-center">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prestamos_por_curso as $curso)
                            <tr>
                                <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                <td>{{ $curso->curso }}</td>
                                <td class="text-center fw-bold text-primary">{{ $curso->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-info fw-bold">
                            <td colspan="2" class="text-end">Total:</td>
                            <td class="text-center">{{ $prestamos_por_curso->sum('total') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4" id="prestamos_por_tipo_perfil">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info">5. Préstamos por Tipo de Perfil</h5>
        </div>
        <div class="card-body">
            <div class="border border-info rounded mb-4 p-3">
                <canvas id="chart-prestamos-por-tipo-perfil"></canvas>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable w-100 mb-0">
                    <thead>
                        <tr>
                            <th width="5%">N°</th>
                            <th>Perfil</th>
                            <th width="15%" class="text-center">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prestamos_por_tipo_perfil as $tipo_perfil)
                            <tr>
                                <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                <td>{{ $tipo_perfil->tipo_perfil }}</td>
                                <td class="text-center fw-bold text-primary">{{ $tipo_perfil->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-info fw-bold">
                            <td colspan="2" class="text-end">Total:</td>
                            <td class="text-center">{{ $prestamos_por_tipo_perfil->sum('total') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4" id="prestamos_por_persona">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info">6. Préstamos por Persona</h5>
        </div>
        <div class="card-body">
            <div class="border border-info rounded mb-4 p-3">
                <canvas id="chart-prestamos-por-persona"></canvas>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable w-100 mb-0">
                    <thead>
                        <tr>
                            <th width="5%">N°</th>
                            <th>Persona</th>
                            <th width="15%" class="text-center">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prestamos_por_persona as $persona)
                            <tr>
                                <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                <td>{{ $persona->persona }}</td>
                                <td class="text-center fw-bold text-primary">{{ $persona->total }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-info fw-bold">
                            <td colspan="2" class="text-end">Total:</td>
                            <td class="text-center">{{ $prestamos_por_persona->sum('total') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="card border-danger shadow-sm mb-4" id="pendientes_hasta_hoy">
        <div class="card-header bg-danger bg-opacity-10 text-danger">
            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i> 7. Libros Pendientes de
                Devolución Hasta Hoy ({{ date('d/m/Y') }})</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table class="table table-bordered table-striped dataTable w-100 mb-0">
                    <thead>
                        <tr class="text-center">
                            <th>N°</th>
                            <th>Persona</th>
                            <th>Curso</th>
                            <th>Celular</th>
                            <th>Cant.</th>
                            <th>Libros Adeudados</th>
                            <th>F. Préstamos</th>
                            <th>Días de Retraso</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prestamos_pendientes as $prestamo_pendiente)
                            <tr>
                                <td class="align-middle text-center fw-bold">{{ $loop->iteration }}</td>
                                <td class="align-middle">
                                    {{ trim('(' . $prestamo_pendiente->tipo_perfil . ') ' . $prestamo_pendiente->apellido_paterno . ' ' . $prestamo_pendiente->apellido_materno . ' ' . $prestamo_pendiente->nombres) }}
                                </td>
                                <td class="align-middle">{{ $prestamo_pendiente->curso }}</td>
                                <td class="align-middle">{{ $prestamo_pendiente->celular }}</td>
                                <td class="align-middle text-center fw-bold text-danger">
                                    {{ $prestamo_pendiente->cantidad_adeudados }}</td>
                                <td>
                                    @foreach ($prestamo_pendiente->detalles as $libro)
                                        <div class="mb-1">
                                            <b class="text-primary">{{ $loop->iteration }}.</b>
                                            <b>{{ $libro->codigo }}</b> - {{ $libro->titulo }}
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($prestamo_pendiente->detalles as $libro)
                                        <div class="mb-1">
                                            <b>{{ $loop->iteration }}.</b>
                                            {{ date('d/m/Y', strtotime($libro->fecha_prestamo)) }}
                                        </div>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($prestamo_pendiente->detalles as $libro)
                                        <div class="mb-1">
                                            <b>{{ $loop->iteration }}.</b>
                                            @if ($libro->dias_retraso < 0)
                                                <span class="badge bg-primary text-white">{{ $libro->dias_retraso * -1 }}
                                                    días restantes</span>
                                            @elseif ($libro->dias_retraso == 0)
                                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock"></i>
                                                    Vence hoy</span>
                                            @else
                                                <span class="badge bg-danger text-white"><i
                                                        class="fa-solid fa-calendar-xmark"></i> {{ $libro->dias_retraso }}
                                                    días vencidos</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-danger fw-bold">
                            <td colspan="4" class="text-end">Total de Libros Adeudados:</td>
                            <td class="text-center fs-5">{{ $prestamos_pendientes->sum('cantidad_adeudados') }}</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4" id="relacion_prestamos_devoluciones">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info">8. Relación entre Libros Prestados y Devueltos Hasta Hoy
                ({{ date('d/m/Y') }})</h5>
        </div>
        <div class="card-body">
            <div class="border border-info rounded mb-4 p-3">
                <canvas id="chart-relacion-prestamos-devoluciones"></canvas>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped dataTable w-100 mb-0">
                    <thead>
                        <tr class="text-center">
                            <th width="5%">N°</th>
                            <th>Persona</th>
                            <th>Curso</th>
                            <th>Celular</th>
                            <th width="10%">Total Prestados</th>
                            <th width="10%">Pendientes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prestamos_totales as $prestamo_total)
                            <tr>
                                <td class="text-center fw-bold align-middle">{{ $loop->iteration }}</td>
                                <td class="align-middle">
                                    {{ trim('(' . $prestamo_total->tipo_perfil . ') ' . $prestamo_total->apellido_paterno . ' ' . $prestamo_total->apellido_materno . ' ' . $prestamo_total->nombres) }}
                                </td>
                                <td class="align-middle">{{ $prestamo_total->curso }}</td>
                                <td class="align-middle">{{ $prestamo_total->celular }}</td>
                                <td class="text-center fw-bold text-primary align-middle">
                                    {{ $prestamo_total->total_libros }}</td>
                                <td class="text-center fw-bold text-danger align-middle">
                                    {{ $prestamo_total->libros_debe }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-info fw-bold">
                            <td colspan="4" class="text-end">Totales:</td>
                            <td class="text-center fs-6">{{ $prestamos_totales->sum('total_libros') }}</td>
                            <td class="text-center fs-6 text-danger">{{ $prestamos_totales->sum('libros_debe') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('prestamos_libros.reportes_scripts')
@endsection
