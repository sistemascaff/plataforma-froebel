<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ public_path('dependencies/bootstrapdompdf.css') }}">

    <title>COMPROBANTE PRÉSTAMO DE LIBROS N° {{ $prestamo_libro->id_prestamo_libro }}</title>
</head>

<body>
    <style>
        html {
            margin: 20px;
        }

        body {
            font-size: 11px;
        }

        .page-break {
            page-break-after: always;
        }

        .table-container {
            display: flex;
            flex-direction: column;
        }

        .table-container table {
            margin: 0;
        }

        .table-container table td {
            padding: 0;
        }

        .inicio {
            margin: 0;
            padding: 0;
        }

        .background-image {
            position: absolute;
            top: 25;
            left: 57.95%;
            width: 40%;
            height: 28%;
            z-index: -1;
            opacity: 0.15;
            /* Ajusta la opacidad para simular un sello seco */
        }
    </style>
    @php
        $totalItems = count($prestamo_libro->libros);
        $numeroBoleta = 1;
    @endphp
    @for ($i = 0; $i < $totalItems; $i += 3)
        <div class="border border-info p-1">
            <div class="table-container">
                <img src="{{ public_path('img/ceff.jpg') }}" class="background-image">
                <table class="table inicio">
                    <tr style="text-align: center; border-style: hidden;">
                        <td width="10%"><img src="{{ public_path('img/ceff.jpg') }}" width="100%"></td>
                        <td width="68%" class="font-weight-bold align-middle text-info">
                            COOPERATIVA EDUCACIONAL FEDERICO FROEBEL R.L.
                            <br>BIBLIOTECA/BIBLIOTHEK
                            <br>Boleta de Préstamo/Ausleihquittung N°
                            {{ $prestamo_libro->id_prestamo_libro }}{{ $totalItems > 3 ? '-' . $numeroBoleta . '/' . ceil($totalItems / 3) : '' }}
                        </td>
                        <td width="22%" class="font-weight-bold align-middle p-1"
                            style="border-left: 1px dotted black;">
                            <div class="border border-dark">
                                Lector/Leser: <span
                                    class="text-info">{{ helper_abreviar_curso($prestamo_libro->curso) }}</span><br>
                                {{ trim($prestamo_libro->persona->apellido_paterno . ' ' . $prestamo_libro->persona->apellido_materno . ' ' . $prestamo_libro->persona->nombres) }}<br>

                            </div>
                        </td>
                    </tr>
                </table>
                <table class="table font-weight-bold">
                    <tr style="border-style: hidden;">
                        <td style="text-align: left;border-style: hidden; border-right: 1px dotted black;"
                            colspan="2">Titulo/Titel:</td>
                        <td class="p-1" width="22%" rowspan="8"
                            style="text-align: left; border-left: 1px dotted black;">
                            <span class="text-info">Boleta para Lector/Quittung für den Leser N°
                                {{ $prestamo_libro->id_prestamo_libro }}{{ $totalItems > 3 ? '-' . $numeroBoleta . '/' . ceil($totalItems / 3) : '' }}</span>
                            <br>Préstamo hasta/Ausgeliehen bis:
                            {{ date('d/m/Y', strtotime($prestamo_libro->fecha_devolucion)) }}
                            @for ($j = 0; $j < 3 && $i + $j < $totalItems; $j++)
                                <br>{{ $i + $j + 1 }}. <span
                                    class="text-info">{{ $prestamo_libro->libros[$i + $j]->codigo }}</span> <span
                                    style="font-weight: normal">{{ helper_recortar_texto($prestamo_libro->libros[$i + $j]->titulo, 70) }}</span>
                            @endfor
                            @if ($i + 3 > $totalItems)
                                @for ($relleno = 0; $relleno < 3 - ($totalItems % 3); $relleno++)
                                    <br>{{ $i + $j + 1 }}. N/A <br>
                                    @php
                                        $j++;
                                    @endphp
                                @endfor
                            @endif
                        </td>
                    </tr>
                    @for ($j = 0; $j < 3 && $i + $j < $totalItems; $j++)
                        <tr style="border-style: hidden;">
                            <td style="text-align: left;" width="66%" colspan="2">{{ $i + $j + 1 }}. <span
                                    class="text-info">{{ $prestamo_libro->libros[$i + $j]->codigo }}</span> <span
                                    style="font-weight: normal;">{{ $prestamo_libro->libros[$i + $j]->titulo }}</span>
                            </td>
                        </tr>
                    @endfor
                    @if ($i + 3 > $totalItems)
                        @for ($relleno = 0; $relleno < 3 - ($totalItems % 3); $relleno++)
                            <tr style="border-style: hidden;">
                                <td colspan="2">{{ $i + $j + 1 }}. N/A</td>
                            </tr>
                            @php
                                $j++;
                            @endphp
                        @endfor
                    @endif
                    <tr style="border-style: hidden;">
                        <td style="text-align: left;">Lector/Leser: <span
                                style="font-weight: normal;">{{ trim('(' . $prestamo_libro->persona->tipo_perfil . ') ' . $prestamo_libro->persona->apellido_paterno . ' ' . $prestamo_libro->persona->apellido_materno . ' ' . $prestamo_libro->persona->nombres) }}</span>
                        </td>
                        <td style="text-align: left;border-style: hidden; border-right: 1px dotted black;">
                            Celular/Handynummer: <span
                                style="font-weight: normal;">{{ $prestamo_libro->celular }}</span></td>
                    </tr>
                    <tr style="border-style: hidden;">
                        <td style="text-align: left;">Fecha/Datum: <span
                                style="font-weight: normal;">{{ date('d/m/Y H:i:s', strtotime($prestamo_libro->fecha_registro)) }}</span>
                            <br>Curso/Klasse: <span style="font-weight: normal;">{{ $prestamo_libro->curso }}</span>
                        </td>
                        <td rowspan="3" class="p-1"
                            style="text-align: left;border-style: hidden; border-right: 1px dotted black;">
                            <div class="border border-dark p-1">
                                @for ($j = 0; $j < 3 && $i + $j < $totalItems; $j++)
                                    {{ $i + $j + 1 }}:
                                    {{ $prestamo_libro->libros[$i + $j]->pivot->fecha_retorno ? '¡Retornado el ' . date('d/m/Y H:i:s', strtotime($prestamo_libro->libros[$i + $j]->pivot->fecha_retorno)) . '!' : '' }}<br><br>
                                @endfor
                                @if ($i + 3 > $totalItems)
                                    @for ($relleno = 0; $relleno < 3 - ($totalItems % 3); $relleno++)
                                        {{ $i + $j + 1 }}: N/A<br><br>
                                        @php
                                            $j++;
                                        @endphp
                                    @endfor
                                @endif
                            </div>
                        </td>
                    </tr>
                    <tr style="border-style: hidden;">
                        <td style="text-align: left;">Fecha de Devolución/Rückgabedatum: <span
                                style="font-weight: normal;">{{ date('d/m/Y', strtotime($prestamo_libro->fecha_devolucion)) }}</span>
                        </td>
                    </tr>
                    <tr style="border-style: hidden;">
                        <td style="text-align: center;">
                            <br>_____________________<br>Firma/Unterschrift
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <br><br>
        @php
            $numeroBoleta++;
        @endphp
        @if ($i + 3 < $totalItems)
            <div class="page-break"></div>
        @endif
    @endfor
</body>

</html>
