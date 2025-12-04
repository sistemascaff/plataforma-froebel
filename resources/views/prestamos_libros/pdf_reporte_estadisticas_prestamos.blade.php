<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ public_path('dependencies/bootstrapdompdf.css') }}">

    <title>REPORTE DE PRÉSTAMOS DE LIBROS ENTRE {{ date('d/m/Y', strtotime($fecha_inicio)) }} Y
        {{ date('d/m/Y', strtotime($fecha_fin)) }}</title>
</head>

<body>
    <style>
        html {
            margin: 25px;
        }

        body {
            font-size: 11px;
            position: relative;
        }

        .page-break {
            page-break-after: always;
        }

        .table-container {
            display: flex;
            flex-direction: column;
        }

        .tabla-inicio table {
            margin: 0;
        }

        .tabla-inicio table td {
            padding: 0;
        }

        .tabla-relleno-corto {
            margin-bottom: 1rem;
        }

        .tabla-relleno-corto table td {
            padding: 0;
        }

        .inicio {
            margin: 0;
            padding: 0;
        }

        .watermark {
            position: fixed;
            top: 34.5%;
            left: 28%;
            width: 300px;
            opacity: 0.15;
            z-index: -1000;
        }

        .subtitulo {
            font-size: 20px;
            font-weight: bold;
        }
    </style>
    <img src="{{ public_path('img/ceff.jpg') }}" class="watermark">
    @php
        $total = 0;
    @endphp
    <div class="d-flex justify-content-center">
        <table class="tabla-inicio">
            <tr>
                <td width="25%"><img src="{{ public_path('img/ceff.jpg') }}" width="30%"></td>
                <td width="50%" class="align-middle text-center font-weight-bold">
                    <p class="inicio" style="font-size: 25px">REPORTE DE BIBLIOTECA</p>
                    <p>Préstamos de libros efectuados entre fechas: <span
                            class="text-info">{{ date('d/m/Y', strtotime($fecha_inicio)) }}</span> a <span
                            class="text-info">{{ date('d/m/Y', strtotime($fecha_fin)) }}</span>
                </td>
                <td width="25%"></td>
            </tr>
        </table>
    </div>

    <p class="align-middle border border-info rounded p-1 font-weight-bold">Fecha de creación: <span
            class="text-info align-middle">{{ date('d/m/Y H:i:s') }}</span>, generado por: <span
            class="text-info align-middle">{{ session('correo') }}</span></p>

    <p class="subtitulo bg-info text-white p-1 text-center rounded align-middle">CANTIDAD TOTAL DE LIBROS PRESTADOS:
        {{ $libros_mas_prestados->sum('total') }}</p>

    <div class="border border-info rounded p-1">
        <p class="subtitulo text-info ml-2">INDICE</p>
        <ul class="subtitulo">
            <li>
                <a class="text-dark" href="#detalle"><u>1. DETALLE</u></a>
            </li>
            <li>
                <a class="text-dark" href="#libros_mas_prestados"><u>2. LIBROS MÁS PRESTADOS</u></a>
            </li>
            <li>
                <a class="text-dark" href="#prestamos_por_categoria"><u>3. PRÉSTAMOS POR CATEGORÍA</u></a>
            </li>
            <li>
                <a class="text-dark" href="#prestamos_por_curso"><u>4. PRÉSTAMOS POR CURSO</u></a>
            </li>
            <li>
                <a class="text-dark" href="#prestamos_por_tipo_perfil"><u>5. PRÉSTAMOS POR TIPO DE PERFIL</u></a>
            </li>
            <li>
                <a class="text-dark" href="#prestamos_por_persona"><u>6. PRÉSTAMOS POR PERSONA</u></a>
            </li>
        </ul>
    </div>

    <div class="page-break"></div>

    <p class="subtitulo text-info" id="detalle"><u>1. DETALLE</u></p>
    @if (count($prestamos_libros) > 0)
        <table class="table table-bordered table-striped table-sm">
            <thead class="bg-secondary text-light font-weight-bold text-center">
                <tr>
                    <th>#</th>
                    <th>N° Préstamo</th>
                    <th>Código</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Editorial</th>
                    <th>Prestado a</th>
                    <th>Curso</th>
                    <th>F. Registro</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prestamos_libros as $prestamo_libro)
                    @foreach ($prestamo_libro->libros as $libro)
                        <tr>
                            <td class="font-weight-bold">{{ $total + 1 }}</td>
                            <td class="text-info font-weight-bold">{{ $prestamo_libro->id_prestamo_libro }}</td>
                            <td>{{ $libro->codigo }}</td>
                            <td>{{ $libro->titulo }}</td>
                            <td>{{ $libro->autor }}</td>
                            <td>{{ $libro->editorial }}</td>
                            <td>{{ trim('(' . $prestamo_libro->persona->tipo_perfil . ') ' . $prestamo_libro->persona->apellido_paterno . ' ' . $prestamo_libro->persona->apellido_materno . ' ' . $prestamo_libro->persona->nombres) }}
                            </td>
                            <td>{{ $prestamo_libro->curso }}</td>
                            <td>{{ date('d/m/Y H:i:s', strtotime($prestamo_libro->fecha_registro)) }}</td>
                        </tr>
                        @php
                            $total++;
                        @endphp
                    @endforeach
                @endforeach
            </tbody>
        </table>
    @else
        <p class="font-weight-bold">No se encontraron registros :(</p>
    @endif

    @if (count($prestamos_libros) > 0)
        <div class="page-break"></div>
    @endif

    <p class="subtitulo text-info" id="libros_mas_prestados"><u>2. LIBROS MÁS PRESTADOS</u></p>
    @if (count($libros_mas_prestados) > 0)
        <table class="table-bordered table-striped tabla-relleno-corto col-12">
            <thead class="bg-secondary text-light">
                <tr class="text-center">
                    <th>N°</th>
                    <th>Libro</th>
                    <th>Categoria</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($libros_mas_prestados as $libro_mas_prestado)
                    <tr>
                        <td class="text-center font-weight-bold">{{ $loop->index + 1 }}</td>
                        <td>{{ $libro_mas_prestado->titulo }}</td>
                        <td>{{ $libro_mas_prestado->categoria }}</td>
                        <td class="text-center">{{ $libro_mas_prestado->total }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-weight-bold">
                    <td colspan="3" class="text-right">Total:</td>
                    <td class="text-center">{{ $libros_mas_prestados->sum('total') }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <p class="font-weight-bold">No se encontraron registros :(</p>
    @endif

    @if (count($libros_mas_prestados) > 0)
        <div class="page-break"></div>
    @endif

    <p class="subtitulo text-info" id="prestamos_por_categoria"><u>3. PRÉSTAMOS POR CATEGORÍA</u></p>
    @if (count($prestamos_por_categoria) > 0)
        <table class="table-bordered table-striped tabla-relleno-corto col-12">
            <thead class="bg-secondary text-light">
                <tr class="text-center">
                    <th>N°</th>
                    <th>Categoria</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prestamos_por_categoria as $categoria)
                    <tr>
                        <td class="text-center font-weight-bold">{{ $loop->index + 1 }}</td>
                        <td>{{ $categoria->categoria }}</td>
                        <td class="text-center">{{ $categoria->total }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-weight-bold">
                    <td colspan="2" class="text-right">Total:</td>
                    <td class="text-center">{{ $prestamos_por_categoria->sum('total') }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <p class="font-weight-bold">No se encontraron registros :(</p>
    @endif

    @if (count($prestamos_por_categoria) > 0)
        <div class="page-break"></div>
    @endif

    <p class="subtitulo text-info" id="prestamos_por_curso"><u>4. PRÉSTAMOS POR CURSO</u></p>
    @if (count($prestamos_por_curso) > 0)
        <table class="table-bordered table-striped tabla-relleno-corto col-12">
            <thead class="bg-secondary text-light">
                <tr class="text-center">
                    <th>N°</th>
                    <th>Curso</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prestamos_por_curso as $curso)
                    <tr>
                        <td class="text-center font-weight-bold">{{ $loop->index + 1 }}</td>
                        <td>{{ $curso->curso }}</td>
                        <td class="text-center">{{ $curso->total }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-weight-bold">
                    <td colspan="2" class="text-right">Total:</td>
                    <td class="text-center">{{ $prestamos_por_curso->sum('total') }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <p class="font-weight-bold">No se encontraron registros :(</p>
    @endif

    <p class="subtitulo text-info" id="prestamos_por_tipo_perfil"><u>5. PRÉSTAMOS POR TIPO DE PERFIL</u></p>
    @if (count($prestamos_por_tipo_perfil) > 0)
        <table class="table-bordered table-striped tabla-relleno-corto col-12">
            <thead class="bg-secondary text-light">
                <tr class="text-center">
                    <th>N°</th>
                    <th>Perfil</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prestamos_por_tipo_perfil as $tipo_perfil)
                    <tr>
                        <td class="text-center font-weight-bold">{{ $loop->index + 1 }}</td>
                        <td>{{ $tipo_perfil->tipo_perfil }}</td>
                        <td class="text-center">{{ $tipo_perfil->total }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-weight-bold">
                    <td colspan="2" class="text-right">Total:</td>
                    <td class="text-center">{{ $prestamos_por_tipo_perfil->sum('total') }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <p class="font-weight-bold">No se encontraron registros :(</p>
    @endif

    @if (count($prestamos_por_tipo_perfil) > 0)
        <div class="page-break"></div>
    @endif

    <p class="subtitulo text-info" id="prestamos_por_persona"><u>6. PRÉSTAMOS POR PERSONA</u></p>
    @if (count($prestamos_por_persona) > 0)
        <table class="table-bordered table-striped tabla-relleno-corto col-12">
            <thead class="bg-secondary text-light">
                <tr class="text-center">
                    <th>N°</th>
                    <th>Persona</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($prestamos_por_persona as $persona)
                    <tr>
                        <td class="text-center font-weight-bold">{{ $loop->index + 1 }}</td>
                        <td>{{ $persona->persona }}</td>
                        <td class="text-center">{{ $persona->total }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-weight-bold">
                    <td colspan="2" class="text-right">Total:</td>
                    <td class="text-center">{{ $prestamos_por_persona->sum('total') }}</td>
                </tr>
            </tfoot>
        </table>
    @else
        <p class="font-weight-bold">No se encontraron registros :(</p>
    @endif

    <script type="text/php">
        if (isset($pdf)) {
        $pdf->page_script('
            $text = sprintf(_("- Página %d de %d -"),  $PAGE_NUM, $PAGE_COUNT);
            // Descomentar la siguiente línea si se desea usar un texto personalizado
            //$text = __("Page :pageNum/:pageCount", ["pageNum" => $PAGE_NUM, "pageCount" => $PAGE_COUNT]);
            $font = $fontMetrics->get_font("helvetica", "normal");
            $size = 9;
            $color = array(0,0,0);
            $word_space = 0.0;  //default
            $char_space = 0.0;  //default
            $angle = 0.0;   //default

            // Obtener las métricas de la fuente para calcular el ancho del texto
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);

            $x = ($pdf->get_width() - $textWidth) / 2;
            $y = $pdf->get_height() - 25;

            $pdf->text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        ');
    }
    </script>
</body>

</html>
