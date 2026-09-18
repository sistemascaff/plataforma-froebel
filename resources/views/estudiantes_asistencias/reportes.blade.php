@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h1 class="text-info fw-bold mb-0 me-auto">
            <i class="fa-solid fa-duotone fa-chart-pie me-2"></i> Reportes de Asistencias
        </h1>
        <a class="btn btn-secondary shadow-sm" href="{{ route('estudiantes_asistencias.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    <div class="alert alert-info border-info border-start border-4 shadow-sm mb-4 d-flex align-items-center" role="alert">
        <i class="fa-solid fa-duotone fa-circle-info fa-2x me-3"></i>
        <div>
            <h5 class="alert-heading fw-bold mb-1">Información del Reporte</h5>
            <p class="mb-0">
                Por defecto, se muestra la información de <b>hoy</b>. Puedes usar los filtros para consultar
                fechas, niveles, coordinaciones o cursos específicos.
            </p>
        </div>
    </div>

    {{-- PANEL DE FILTROS DINÁMICOS --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header p-3">
            <h5 class="fw-bold mb-0 text-primary">
                <i class="fa-duotone fa-filter me-1"></i> Filtros de Búsqueda
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label for="fecha_inicio" class="form-label text-muted fw-bold">Desde Fecha:</label>
                    <input type="date" class="form-control" id="fecha_inicio" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <label for="fecha_fin" class="form-label text-muted fw-bold">Hasta Fecha:</label>
                    <input type="date" class="form-control" id="fecha_fin" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-2">
                    <label for="id_nivel" class="form-label text-muted fw-bold">Nivel:</label>
                    <select class="form-select" id="id_nivel">
                        <option value="">Todos</option>
                        @if (isset($niveles))
                            @foreach ($niveles as $nivel)
                                <option value="{{ $nivel->id_nivel }}">{{ $nivel->nivel }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="id_coordinacion" class="form-label text-muted fw-bold">Coordinacion:</label>
                    <select class="form-select" id="id_coordinacion">
                        <option value="">Todos</option>
                        @if (isset($coordinaciones))
                            @foreach ($coordinaciones as $coordinacion)
                                <option value="{{ $coordinacion->id_coordinacion }}">{{ $coordinacion->coordinacion }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="id_curso" class="form-label text-muted fw-bold">Curso:</label>
                    <select class="form-select" id="id_curso">
                        <option value="">Todos</option>
                        @if (isset($cursos))
                            @foreach ($cursos as $curso)
                                <option value="{{ $curso->id_curso }}">{{ $curso->curso }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary w-100 shadow-sm" id="btn-filtrar">
                        <i class="fa-duotone fa-search me-1"></i> Filtrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLA DE RESULTADOS --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-info">
                <i class="fa-solid fa-duotone fa-calendar-lines me-2"></i>
                Reporte de incidencias
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped w-100" id="dataTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">#</th>
                            <th>Fecha</th>
                            <th>Horario</th>
                            <th>Curso</th>
                            <th>Estudiante</th>
                            <th>Asignatura</th>
                            <th>Docente</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Atraso</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // Inicialización de la Tabla
            let tablaReportes = $("#dataTable").DataTable({
                processing: true,
                /* Heredamos configuraciones globales de DataTables */
                @include('components.datatables.datatables_global_properties')
                @include('components.datatables.datatables_language_property'),
                ajax: {
                    url: "{{ route('estudiantes_asistencias.listar_reporte_incidencias') }}",
                    type: "GET",
                    data: function(d) {
                        // Enviamos el valor de los inputs al Request
                        d.fecha_inicio = $('#fecha_inicio').val();
                        d.fecha_fin = $('#fecha_fin').val();
                        d.id_nivel = $('#id_nivel').val();
                        d.id_coordinacion = $('#id_coordinacion').val();
                        d.id_curso = $('#id_curso').val();
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                },
                columns: [{
                        data: null,
                        className: "text-center align-middle",
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: "estudiante_asistencia.fecha",
                        className: "align-middle fw-bold",
                        render: function(data) {
                            return data ? moment(data).format('DD/MM/YYYY') : '';
                        }
                    },
                    {
                        data: "estudiante_asistencia.horario_asignatura.hora_inicio",
                        className: "align-middle text-muted",
                        render: function(data) {
                            return data ? moment(data, 'HH:mm:ss').format('HH:mm') : '';
                        }
                    },
                    {
                        data: "estudiante.curso.curso",
                        className: "align-middle text-truncate",
                        render: function(data) {
                            return `<span class="badge bg-info text-dark">${data}</span>`;
                        }
                    },
                    {
                        data: null,
                        className: "align-middle fw-bold",
                        render: function(data, type, row) {
                            if (row.estudiante && row.estudiante.persona) {
                                let p = row.estudiante.persona;
                                return `${p.apellidos_nombres}`;
                            }
                            return '-';
                        }
                    },
                    {
                        data: "estudiante_asistencia.lista_asignatura.asignatura.asignatura",
                        className: "align-middle"
                    },
                    {
                        data: null,
                        className: "align-middle",
                        render: function(data, type, row) {
                            let docente = row.estudiante_asistencia?.lista_asignatura?.docente
                                ?.persona;
                            return docente ? `${docente.nombres_apellidos}` :
                                '<span class="text-danger">Sin docente</span>';
                        }
                    },
                    {
                        data: "tipo",
                        className: "text-center align-middle",
                        render: function(data) {
                            let badgeClass = 'bg-secondary';
                            let icon = 'fa-question';
                            let texto = data;

                            if (data === 'A') {
                                badgeClass = 'bg-warning text-dark';
                                icon = 'fa-hourglass-half';
                                texto = 'Atraso';
                            } else if (data === 'F') {
                                badgeClass = 'bg-danger';
                                icon = 'fa-times-circle';
                                texto = 'Falta';
                            } else if (data === 'L') {
                                badgeClass = 'bg-info text-dark';
                                icon = 'fa-file-certificate';
                                texto = 'Licencia';
                            }

                            return `<span class="badge ${badgeClass} px-3 py-2 shadow-sm"><i class="fa-solid fa-duotone ${icon} me-1"></i> ${texto}</span>`;
                        }
                    },
                    {
                        data: "tiempo_atraso",
                        className: "text-center align-middle",
                        render: function(data, type, row) {
                            if (row.tipo === 'A' && data) {
                                return `<span class="text-danger fw-bold">${data} min</span>`;
                            }
                            return '<span class="text-muted">-</span>';
                        }
                    },
                    {
                        data: null,
                        className: "align-middle",
                        render: function(data, type, row) {
                            if (row.tipo === 'L' && row.estudiante_licencia) {
                                return `<span class="d-inline-block" style="max-width: 200px;" title="${row.estudiante_licencia.justificacion}">
                                            ${row.estudiante_licencia.justificacion || 'Sin justificación'}
                                        </span>`;
                            }
                            return '<span class="text-muted text-center d-block">-</span>';
                        }
                    }
                ]
            });

            // Disparador del evento que definiste
            $("#btn-filtrar").on("click", function(e) {
                e.preventDefault();
                tablaReportes.ajax.reload();
            });
        });
    </script>
@endsection
