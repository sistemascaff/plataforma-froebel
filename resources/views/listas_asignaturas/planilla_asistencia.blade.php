@extends('layouts.app')

@section('content')
    @include('listas_asignaturas.planilla_asistencia_css')

    {{-- Barra Superior de Acciones (Oculta al imprimir) --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2 d-print-none">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-table-list me-2"></i>PLANILLA DE ASISTENCIA
        </h1>

        <div class="d-flex flex-wrap gap-2">
            <button type="button" id="btn-exportar-excel" class="btn btn-success shadow-sm"
                {{ empty($planilla['columnas']) ? 'disabled' : '' }}>
                <i class="fa-solid fa-duotone fa-file-excel me-1"></i> Exportar a Excel (.xlsx)
            </button>

            <button type="button" id="btn-imprimir" class="btn btn-primary shadow-sm"
                {{ empty($planilla['columnas']) ? 'disabled' : '' }}>
                <i class="fa-solid fa-duotone fa-print me-1"></i> Imprimir / Guardar PDF
            </button>

            <a class="btn btn-secondary shadow-sm"
                href="{{ route('listas_asignaturas.detalles', $lista_asignatura->id_lista_asignatura) }}">
                <i class="fa-solid fa-duotone fa-arrow-left me-1"></i> Volver a la Lista
            </a>
        </div>
    </div>

    {{-- Leyenda Rápida de Simbología (Oculta al imprimir) --}}
    <div class="alert alert-info border-info border-start border-4 shadow-sm mb-4 d-print-none py-2">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 small">
            <div>
                <i class="fa-solid fa-duotone fa-circle-info me-1"></i>
                <b>Simbología:</b>
                <span class="badge bg-success ms-1">(.) Presente</span>
                <span class="badge bg-danger ms-1">(F) Falta</span>
                <span class="badge bg-warning text-dark ms-1">(A) Atraso</span>
                <span class="badge bg-info text-dark ms-1">(L) Licencia</span>
                <span class="badge bg-primary ms-1">(P) Permiso</span>
                <span class="badge bg-secondary ms-1">(O) Otros</span>
                <span class="badge border text-muted ms-1">(/) Sin registro / No inscrito en fecha</span>
            </div>
            <div>
                <span class="badge bg-secondary px-2 py-1">
                    <i class="fa-solid fa-user-minus me-1"></i> Fila Gris = Estudiante Retirado de la Lista Actual
                </span>
            </div>
        </div>
    </div>

    {{-- ==================================================================== --}}
    {{-- CONTENEDOR EXCLUSIVO DE IMPRESIÓN (#planilla-asistencia-card)        --}}
    {{-- ==================================================================== --}}
    <div class="card shadow-sm mb-4" id="planilla-asistencia-card">
        <div class="card-body">
            {{-- ENCABEZADO INSTITUCIONAL CON LOGO A LA IZQUIERDA --}}
            <div class="border rounded p-3 mb-3 encabezado-institucional">
                <div class="d-flex align-items-center gap-3">
                    {{-- Logo Institucional a la izquierda --}}
                    <div class="flex-shrink-0 text-center pe-2">
                        <img id="logo-institucional-img" src="{{ URL::to('/') . '/public/img/caff.png' }}" alt="Logo CAFF"
                            class="logo-planilla" crossorigin="anonymous">
                    </div>

                    {{-- Título y Metadatos Institucionales --}}
                    <div class="flex-grow-1">
                        <h4 class="text-center fw-bold mb-2 text-uppercase tracking-wide">
                            PLANILLA DE ASISTENCIA
                        </h4>
                        <div class="row g-2 small">
                            <div class="col-md-6 col-6">
                                <div class="mb-1">
                                    <span class="fw-bold text-muted">UNIDAD EDUCATIVA:</span>
                                    <span class="fw-bold">{{ $planilla['encabezado']['unidad_educativa'] }}</span>
                                </div>
                                <div class="mb-1">
                                    <span class="fw-bold text-muted">DOCENTE:</span>
                                    <span class="fw-bold">{{ $planilla['encabezado']['docente'] }}</span>
                                </div>
                                <div>
                                    <span class="fw-bold text-muted">CAMPO:</span>
                                    <span class="fw-bold">{{ $planilla['encabezado']['campo'] }}</span>
                                </div>
                            </div>
                            <div class="col-md-6 col-6">
                                <div class="mb-1">
                                    <span class="fw-bold text-muted">GESTIÓN:</span>
                                    <span class="fw-bold">{{ $planilla['encabezado']['gestion_periodo'] }}</span>
                                </div>
                                <div class="mb-1">
                                    <span class="fw-bold text-muted">ÁREA:</span>
                                    <span class="fw-bold">{{ $planilla['encabezado']['area'] }}</span>
                                </div>
                                <div>
                                    <span class="fw-bold text-muted">CURSO:</span>
                                    <span class="fw-bold text-primary">{{ $planilla['encabezado']['curso'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (empty($planilla['columnas']))
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-duotone fa-calendar-xmark fa-3x mb-3 text-secondary"></i>
                    <h5 class="fw-bold">No existen asistencias registradas en este periodo</h5>
                    <p class="mb-0">
                        Cuando se registren asistencias dentro de las fechas del periodo
                        (<b>{{ $planilla['encabezado']['gestion_periodo'] }}</b>), la matriz se generará automáticamente.
                    </p>
                </div>
            @else
                {{-- TABLA MATRICIAL DE ASISTENCIA --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover w-100 mb-0 tabla-planilla" id="tabla-planilla-web">
                        <thead>
                            {{-- NIVEL 1: MESES Y RECUENTO --}}
                            <tr class="text-center align-middle table-secondary">
                                <th colspan="2" class="text-end pe-2 fw-bold">MES</th>
                                @foreach ($planilla['meses'] as $grupoMes)
                                    <th colspan="{{ $grupoMes['colspan'] }}" class="fw-bold text-uppercase">
                                        {{ $grupoMes['mes'] }}
                                    </th>
                                @endforeach
                                <th colspan="6" class="fw-bold text-uppercase">RECUENTO</th>
                            </tr>

                            {{-- NIVEL 2: DÍAS DE LA SEMANA (VERTICAL) Y ETIQUETAS DE RECUENTO (VERTICAL) --}}
                            <tr class="text-center align-middle">
                                <th colspan="2" class="text-end pe-2 fw-bold">DÍA</th>
                                @foreach ($planilla['columnas'] as $col)
                                    <th title="Fecha: {{ $col['fecha_completa'] }} | Hora: {{ $col['hora_inicio'] }}">
                                        <div class="th-vertical">{{ $col['etiqueta_dia'] }}</div>
                                    </th>
                                @endforeach
                                <th class="table-secondary" title="Presentes (.)">
                                    <div class="th-vertical">PRESENTES (.)</div>
                                </th>
                                <th class="table-secondary" title="Atrasos (A)">
                                    <div class="th-vertical">ATRASOS (A)</div>
                                </th>
                                <th class="table-secondary" title="Faltas (F)">
                                    <div class="th-vertical">FALTAS (F)</div>
                                </th>
                                <th class="table-secondary" title="Licencias (L)">
                                    <div class="th-vertical">LICENCIAS (L)</div>
                                </th>
                                <th class="table-secondary" title="Permisos (P)">
                                    <div class="th-vertical">PERMISOS (P)</div>
                                </th>
                                <th class="table-secondary" title="Otros (O)">
                                    <div class="th-vertical">OTROS (O)</div>
                                </th>
                            </tr>

                            {{-- NIVEL 3: N°, NOMBRE COMPLETO, DÍA NUMÉRICO Y SIGLAS DE TOTALES --}}
                            <tr class="text-center align-middle">
                                <th class="col-sticky-num table-secondary">N°</th>
                                <th class="col-sticky-nombre text-start table-secondary">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>NOMBRE COMPLETO</span>
                                        <span class="text-muted small">FECHA</span>
                                    </div>
                                </th>
                                @foreach ($planilla['columnas'] as $col)
                                    <th class="celda-asistencia"
                                        title="{{ $col['fecha_completa'] }} ({{ $col['denominacion_horario'] }})">
                                        {{ $col['dia_numero'] }}
                                    </th>
                                @endforeach
                                <th class="table-secondary col-recuento text-success">.</th>
                                <th class="table-secondary col-recuento text-warning">A</th>
                                <th class="table-secondary col-recuento text-danger">F</th>
                                <th class="table-secondary col-recuento text-info">L</th>
                                <th class="table-secondary col-recuento text-primary">P</th>
                                <th class="table-secondary col-recuento text-secondary">O</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($planilla['estudiantes'] as $idxEst => $est)
                                <tr class="{{ !$est['en_lista_actual'] ? 'fila-retirado' : '' }}">
                                    <td class="text-center col-sticky-num fw-bold">{{ $est['numero'] }}</td>
                                    <td class="col-sticky-nombre fw-semibold">
                                        {{ $est['apellidos_nombres'] }}
                                        @if (!$est['en_lista_actual'])
                                            <span class="badge bg-secondary ms-1 d-print-none"
                                                style="font-size: 0.65rem;">
                                                RETIRADO
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Celdas de cada fecha/clase --}}
                                    @foreach ($est['asistencias'] as $idxCol => $celda)
                                        @php
                                            $colInfo = $planilla['columnas'][$idxCol];
                                            $colorClase = match ($celda['tipo']) {
                                                'FALTA' => 'text-danger',
                                                'ATRASO' => 'text-warning',
                                                'LICENCIA' => 'text-info',
                                                'PERMISO' => 'text-primary',
                                                'OTROS' => 'text-secondary',
                                                default => '',
                                            };

                                            $tooltip = "{$colInfo['fecha_completa']} ({$colInfo['etiqueta_dia']}): ";
                                            if (!$celda['registrado']) {
                                                $tooltip .= 'Sin registro en esta fecha';
                                            } elseif ($celda['tipo'] === 'ATRASO' && $celda['tiempo_atraso'] > 0) {
                                                $tooltip .= "ATRASO ({$celda['tiempo_atraso']} min) - Clic para ver detalles";
                                            } elseif (
                                                $celda['tipo'] === 'PERMISO' &&
                                                !empty($celda['justificacion_permiso'])
                                            ) {
                                                $tooltip .= "PERMISO ({$celda['justificacion_permiso']}) - Clic para ver detalles";
                                            } elseif (
                                                $celda['tipo'] === 'OTROS' &&
                                                !empty($colInfo['justificacion_otros'])
                                            ) {
                                                $tooltip .= "OTROS ({$colInfo['justificacion_otros']}) - Clic para ver detalles";
                                            } else {
                                                $tooltip .= "{$celda['tipo']} - Clic para ver detalles";
                                            }
                                        @endphp
                                        <td class="celda-asistencia {{ $colorClase }} {{ $celda['registrado'] ? 'celda-interactiva' : '' }}"
                                            title="{{ $tooltip }}"
                                            @if ($celda['registrado']) data-est-idx="{{ $idxEst }}" data-col-idx="{{ $idxCol }}" @endif>
                                            {{ $celda['simbolo'] }}
                                        </td>
                                    @endforeach

                                    {{-- 6 Columnas de Recuento Individual --}}
                                    <td class="col-recuento">{{ $est['totales']['PRESENTE'] }}</td>
                                    <td class="col-recuento {{ $est['totales']['ATRASO'] > 0 ? 'text-warning' : '' }}">
                                        {{ $est['totales']['ATRASO'] }}
                                    </td>
                                    <td class="col-recuento {{ $est['totales']['FALTA'] > 0 ? 'text-danger' : '' }}">
                                        {{ $est['totales']['FALTA'] }}
                                    </td>
                                    <td class="col-recuento {{ $est['totales']['LICENCIA'] > 0 ? 'text-info' : '' }}">
                                        {{ $est['totales']['LICENCIA'] }}
                                    </td>
                                    <td class="col-recuento {{ $est['totales']['PERMISO'] > 0 ? 'text-primary' : '' }}">
                                        {{ $est['totales']['PERMISO'] }}
                                    </td>
                                    <td class="col-recuento {{ $est['totales']['OTROS'] > 0 ? 'text-secondary' : '' }}">
                                        {{ $est['totales']['OTROS'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    @include('listas_asignaturas.planilla_asistencia_scripts')
@endsection
