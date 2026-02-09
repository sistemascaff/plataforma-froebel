@extends('layouts.app')

@section('content')
    @php
        $total = 0;
    @endphp

    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-books fa-rotate-270"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-secondary mb-3" href="{{ route('prestamos_libros.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver</a>

    <h2 class="text-info fw-bold">Reportes (Módulo de biblioteca)</h2>

    <h5>
        Este reporte genera por defecto la información sobre los últimos 3 meses.
    </h5>
    <p>
        En caso de necesitar fechas específicas, puedes utilizar el formulario de abajo.<br>
        Nota: El reporte está basado principalmente en la <b>cantidad de libros</b>.
    </p>

    <form class="row" method="GET">
        <div class="col-md-4 mb-3">
            <label for="fecha_inicio" class="col-form-label">Fecha inicio:</label>
            <input type="date" name="fecha_inicio" class="form-control"
                value="{{ $fecha_inicio ?? date('Y-m-d', strtotime('-3 months')) }}" max="{{ date('Y-m-d') }}">
        </div>
        <div class="col-md-4 mb-3">
            <label for="fecha_fin" class="col-form-label">Fecha fin:</label>
            <input type="date" name="fecha_fin" class="form-control"
                value="{{ date('Y-m-d', strtotime($fecha_fin)) ?? date('Y-m-d') }}" max="{{ date('Y-m-d') }}">
        </div>
        <div class="col-md-4 mb-3 d-flex align-items-end">
            <button type="submit" formaction="{{ route('prestamos_libros.reportes') }}" class="btn btn-info"><i
                    class="fa-solid fa-duotone fa-search"></i> Buscar</button>
            <button type="submit" formaction="{{ route('prestamos_libros.reportes.imprimir') }}" formtarget="_blank"
                class="btn btn-primary ms-2"><i class="fa-solid fa-duotone fa-print"></i> Imprimir</button>
        </div>
    </form>

    <div class="col-md-6 col-lg-3 mb-4">
        <div class="card info-card shadow-sm border-info">
            <div class="card-body d-flex align-items-center bg-info bg-opacity-10">
                <div class="icon-box bg-info bg-opacity-10 me-3">
                    <i class="text-info fa-solid fa-duotone fa-books fa-rotate-270 fa-xl"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small">Libros prestados</h6>
                    <h3 class="fw-bold">{{ $libros_mas_prestados->sum('total') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <h3 class="text-info fw-bold">INDICE</h3>
            <ul class="fw-bold">
                <li>
                    <a href="#detalle">1. DETALLE</a>
                </li>
                <li>
                    <a href="#libros_mas_prestados">2. LIBROS MÁS PRESTADOS</a>
                </li>
                <li>
                    <a href="#prestamos_por_categoria">3. PRÉSTAMOS POR CATEGORÍA</a>
                </li>
                <li>
                    <a href="#prestamos_por_curso">4. PRÉSTAMOS POR CURSO</a>
                </li>
                <li>
                    <a href="#prestamos_por_tipo_perfil">5. PRÉSTAMOS POR TIPO DE PERFIL</a>
                </li>
                <li>
                    <a href="#prestamos_por_persona">6. PRÉSTAMOS POR PERSONA</a>
                </li>
                <li>
                    <a href="#pendientes_hasta_hoy">7. LIBROS PENDIENTES HASTA HOY ({{ date('d/m/Y') }})</a>
                </li>
                <li>
                    <a href="#relacion_prestamos_devoluciones">8. RELACIÓN ENTRE LIBROS PRESTADOS Y DEVUELTOS HASTA HOY
                        ({{ date('d/m/Y') }})</a>
                </li>
            </ul>
        </div>
    </div>

    <h3 class="text-info fw-bold" id="detalle"><u>1. DETALLE:</u></h3>

    <table class="table table-bordered table-striped dataTable">
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
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($prestamos_libros as $prestamo_libro)
                @foreach ($prestamo_libro->libros as $libro)
                    <tr>
                        <td>{{ $prestamo_libro->id_prestamo_libro }}</td>
                        <td>{{ $libro->codigo }}</td>
                        <td>{{ $libro->titulo }}</td>
                        <td>{{ $libro->autor }}</td>
                        <td>{{ $libro->editorial }}</td>
                        <td>{{ trim('(' . $prestamo_libro->persona->tipo_perfil . ') ' . $prestamo_libro->persona->apellido_paterno . ' ' . $prestamo_libro->persona->apellido_materno . ' ' . $prestamo_libro->persona->nombres) }}
                        </td>
                        <td>{{ $prestamo_libro->curso }}</td>
                        <td>{{ date('d/m/Y H:i:s', strtotime($prestamo_libro->fecha_registro)) }}</td>
                        <td>
                            <a class="btn btn-info"
                                href="{{ route('prestamos_libros.detalles', $prestamo_libro->id_prestamo_libro) }}"
                                target="_blank" rel="noopener noreferrer">
                                <i class="fa-solid fa-duotone fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @php
                        $total++;
                    @endphp
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-info fw-bold">
                <td colspan="8" class="text-end">Total:</td>
                <td>{{ $total }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="mb-3"></div>

    <h3 class="text-info fw-bold" id="libros_mas_prestados"><u>2. LIBROS MÁS PRESTADOS:</u></h3>

    <div class="border border-info rounded mb-3 p-2">
        <canvas id="chart-libros-mas-prestados"></canvas>
    </div>

    <table class="table table-bordered table-striped dataTable">
        <thead>
            <tr>
                <th>N°</th>
                <th>Libro</th>
                <th>Categoria</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($libros_mas_prestados as $libro_mas_prestado)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $libro_mas_prestado->titulo }}</td>
                    <td>{{ $libro_mas_prestado->categoria }}</td>
                    <td>{{ $libro_mas_prestado->total }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-info fw-bold">
                <td colspan="3" class="text-end">Total:</td>
                <td>{{ $libros_mas_prestados->sum('total') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="mb-3"></div>

    <h3 class="text-info fw-bold" id="prestamos_por_categoria"><u>3. PRÉSTAMOS POR CATEGORÍA:</u></h3>

    <div class="border border-info rounded mb-3 p-2">
        <canvas id="chart-prestamos-por-categoria"></canvas>
    </div>

    <table class="table table-bordered table-striped dataTable">
        <thead>
            <tr>
                <th>N°</th>
                <th>Categoria</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($prestamos_por_categoria as $categoria)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $categoria->categoria }}</td>
                    <td>{{ $categoria->total }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-info fw-bold">
                <td colspan="2" class="text-end">Total:</td>
                <td>{{ $prestamos_por_categoria->sum('total') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="mb-3"></div>

    <h3 class="text-info fw-bold" id="prestamos_por_curso"><u>4. PRÉSTAMOS POR CURSO:</u></h3>

    <div class="border border-info rounded mb-3 p-2">
        <canvas id="chart-prestamos-por-curso"></canvas>
    </div>

    <table class="table table-bordered table-striped dataTable">
        <thead>
            <tr>
                <th>N°</th>
                <th>Curso</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($prestamos_por_curso as $curso)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $curso->curso }}</td>
                    <td>{{ $curso->total }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-info fw-bold">
                <td colspan="2" class="text-end">Total:</td>
                <td>{{ $prestamos_por_curso->sum('total') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="mb-3"></div>

    <h3 class="text-info fw-bold" id="prestamos_por_tipo_perfil"><u>5. PRÉSTAMOS POR TIPO DE PERFIL:</u></h3>

    <div class="border border-info rounded mb-3 p-2">
        <canvas id="chart-prestamos-por-tipo-perfil"></canvas>
    </div>

    <table class="table table-bordered table-striped dataTable">
        <thead>
            <tr>
                <th>N°</th>
                <th>Perfil</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($prestamos_por_tipo_perfil as $tipo_perfil)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $tipo_perfil->tipo_perfil }}</td>
                    <td>{{ $tipo_perfil->total }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-info fw-bold">
                <td colspan="2" class="text-end">Total:</td>
                <td>{{ $prestamos_por_tipo_perfil->sum('total') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="mb-3"></div>

    <h3 class="text-info fw-bold" id="prestamos_por_persona"><u>6. PRÉSTAMOS POR PERSONA:</u></h3>

    <div class="border border-info rounded mb-3 p-2">
        <canvas id="chart-prestamos-por-persona"></canvas>
    </div>

    <table class="table table-bordered table-striped dataTable">
        <thead>
            <tr>
                <th>N°</th>
                <th>Persona</th>
                <th>Cantidad</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($prestamos_por_persona as $persona)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $persona->persona }}</td>
                    <td>{{ $persona->total }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-info fw-bold">
                <td colspan="2" class="text-end">Total:</td>
                <td>{{ $prestamos_por_persona->sum('total') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="mb-3"></div>

    <h3 class="text-info fw-bold" id="pendientes_hasta_hoy">
        <u>7. LIBROS PENDIENTES HASTA HOY ({{ date('d/m/Y') }}):</u>
    </h3>

    <table class="table table-bordered table-striped dataTable">
        <thead>
            <tr class="text-center">
                <th>N°</th>
                <th>PERSONA</th>
                <th>CURSO</th>
                <th>CELULAR</th>
                <th>CANT.</th>
                <th>LIBROS ADEUDADOS</th>
                <th>F. PRESTAMOS</th>
                <th>DIAS DE RETRASO</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($prestamos_pendientes as $prestamo_pendiente)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>
                        {{ trim(
                            '(' .
                                $prestamo_pendiente->tipo_perfil .
                                ') ' .
                                $prestamo_pendiente->apellido_paterno .
                                ' ' .
                                $prestamo_pendiente->apellido_materno .
                                ' ' .
                                $prestamo_pendiente->nombres,
                        ) }}
                    </td>
                    <td>{{ $prestamo_pendiente->curso }}</td>
                    <td>{{ $prestamo_pendiente->celular }}</td>
                    <td>{{ $prestamo_pendiente->cantidad_adeudados }}</td>
                    <td>
                        @foreach ($prestamo_pendiente->detalles as $libro)
                            <b class="text-primary">{{ $loop->index + 1 }}.</b> <b>{{ $libro->codigo }}</b> -
                            {{ $libro->titulo }}<br>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($prestamo_pendiente->detalles as $libro)
                            <b>{{ $loop->index + 1 }}.</b>
                            {{ date('d/m/Y H:i:s', strtotime($libro->fecha_prestamo)) }}<br>
                        @endforeach
                    </td>
                    <td>
                        @foreach ($prestamo_pendiente->detalles as $libro)
                            <b>{{ $loop->index + 1 }}.</b>
                            @if ($libro->dias_retraso < 0)
                                <b class="text-primary">{{ $libro->dias_retraso * -1 }} días restantes</b>
                            @elseif ($libro->dias_retraso == 0)
                                <b class="text-warning">Vence hoy</b>
                            @else
                                <b class="text-danger">{{ $libro->dias_retraso }} y contando...</b>
                            @endif
                            <br>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-info fw-bold">
                <td colspan="7" class="text-end">Total:</td>
                <td>{{ $prestamos_pendientes->sum('cantidad_adeudados') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="mb-3"></div>

    <h3 class="text-info fw-bold" id="relacion_prestamos_devoluciones">
        <u>8. RELACIÓN ENTRE LIBROS PRESTADOS Y DEVUELTOS HASTA HOY ({{ date('d/m/Y') }}):</u>
    </h3>

    <div class="border border-info rounded mb-3 p-2">
        <canvas id="chart-relacion-prestamos-devoluciones"></canvas>
    </div>

    <table class="table table-bordered table-striped dataTable">
        <thead>
            <tr class="text-center">
                <th>N°</th>
                <th>PERSONA</th>
                <th>CURSO</th>
                <th>CELULAR</th>
                <th>TOTAL</th>
                <th>PENDIENTES</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($prestamos_totales as $prestamo_total)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>
                        {{ trim(
                            '(' .
                                $prestamo_total->tipo_perfil .
                                ') ' .
                                $prestamo_total->apellido_paterno .
                                ' ' .
                                $prestamo_total->apellido_materno .
                                ' ' .
                                $prestamo_total->nombres,
                        ) }}
                    </td>
                    <td>{{ $prestamo_total->curso }}</td>
                    <td>{{ $prestamo_total->celular }}</td>
                    <td>{{ $prestamo_total->total_libros }}</td>
                    <td>{{ $prestamo_total->libros_debe }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="table-info fw-bold">
                <td colspan="4" class="text-end">Totales:</td>
                <td>{{ $prestamos_totales->sum('total_libros') }}</td>
                <td>{{ $prestamos_totales->sum('libros_debe') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="mb-3"></div>
@endsection

@section('scripts')
    @include('prestamos_libros.reportes_scripts')
@endsection
