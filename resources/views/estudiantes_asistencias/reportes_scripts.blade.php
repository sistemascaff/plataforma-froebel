<script>
    const AUTH_PERFIL = '{{ Auth::user()->persona?->tipo_perfil }}';

    // Variables globales de gráficos
    let chartProporcion, chartFechas, chartHorarios, chartRanking, chartCursos, chartAsignaturas, chartDocentes;

    $(document).ready(function() {
        $('#id_docente, #id_curso').select2({
            width: '100%',
            language: "es",
            dropdownCssClass: localStorage.getItem('theme') === 'dark' ? 'bg-dark text-white' : '',
            selectionCssClass: localStorage.getItem('theme') === 'dark' ? 'bg-dark text-white' : '',
            matcher: matchFlexible,
        });

        $('#filter_rango_fechas').on('change', function() {
            const selectedValue = $(this).val();
            if (selectedValue === 'personalizado') {
                $('#filter_fecha_inicio_container').removeClass('d-none');
                $('#filter_fecha_fin_container').removeClass('d-none');
            } else {
                $('#filter_fecha_inicio_container').addClass('d-none');
                $('#filter_fecha_fin_container').addClass('d-none');
            }
        });

        // Catálogo maestro de estados para gráficos y filtros
        const ESTADOS_CONFIG = [{
                key: 'ATRASO',
                label: 'Atrasos',
                color: () => chartPalettes.incidencias.atraso
            },
            {
                key: 'FALTA',
                label: 'Faltas',
                color: () => chartPalettes.incidencias.falta
            },
            {
                key: 'LICENCIA',
                label: 'Licencias',
                color: () => chartPalettes.incidencias.licencia
            },
            {
                key: 'PERMISO',
                label: 'Permisos',
                color: () => chartPalettes.incidencias.permiso
            },
            {
                key: 'SUSPENDIDO',
                label: 'Suspendidos',
                color: () => chartPalettes.incidencias.suspendido
            }
        ];

        const getEstadosActivos = () => $('.filter-estado:checked').map(function() {
            return $(this).val();
        }).get();

        let estadosActivos = getEstadosActivos();

        // Filtro reactivo en cliente exclusivo para la tabla principal (#dataTable)
        $.fn.dataTable.ext.search.push(function(settings, searchData, dataIndex, rowData) {
            if (settings.nTable.id !== 'dataTable') {
                return true;
            }

            const tipo = rowData?.tipo || settings.aoData[dataIndex]?._aData?.tipo;
            if (!tipo) return true;

            return estadosActivos.includes(tipo);
        });

        // Helper visual para contadores individuales de la tabla de ranking
        const renderConteoBadge = (valor, type, badgeClass) => {
            if (type === 'sort' || type === 'type') return valor;
            if (valor > 0) {
                return `<span class="badge ${badgeClass} px-2 py-1 shadow-sm">${valor}</span>`;
            }
            return `<span class="text-muted">0</span>`;
        };

        // 1. Inicialización de la Tabla de Ranking por Estudiante (se declara antes para estar lista en drawCallback)
        let tablaRanking = $("#table-ranking-estudiantes").DataTable({
            data: [],
            columns: [{
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: "text-center align-middle",
                    render: (data, type, row, meta) => meta.row + 1
                },
                {
                    data: "curso",
                    className: "align-middle text-truncate",
                    render: (data, type, row) => {
                        if (type === 'sort' || type === 'type' || type === 'filter')
                            return data;
                        return `<span class="badge bg-info text-dark" title="${data}">${row.curso}</span>`;
                    }
                },
                {
                    data: "estudiante",
                    className: "align-middle fw-bold"
                },
                {
                    data: "atrasos",
                    className: "text-center align-middle",
                    render: (data, type) => renderConteoBadge(data, type, 'bg-warning text-dark')
                },
                {
                    data: "faltas",
                    className: "text-center align-middle",
                    render: (data, type) => renderConteoBadge(data, type, 'bg-danger')
                },
                {
                    data: "suspendidos",
                    className: "text-center align-middle",
                    render: (data, type) => renderConteoBadge(data, type,
                        'bg-dark text-light border border-danger')
                },
                {
                    data: "disciplinarias",
                    className: "text-center align-middle fw-bold",
                    createdCell: function(td, cellData) {
                        if (cellData > 0) {
                            $(td).addClass('table-danger');
                        }
                    },
                    render: (data, type) => {
                        if (type === 'sort' || type === 'type') return data;
                        if (data > 0) {
                            return `<span class="badge bg-danger fs-6 px-3 py-1 shadow-sm">${data}</span>`;
                        }
                        return `<span class="text-muted fw-normal">0</span>`;
                    }
                },
                {
                    data: "licencias",
                    className: "text-center align-middle",
                    render: (data, type) => renderConteoBadge(data, type, 'bg-info text-dark')
                },
                {
                    data: "permisos",
                    className: "text-center align-middle",
                    render: (data, type) => renderConteoBadge(data, type, 'bg-primary')
                },
                {
                    data: "justificadas",
                    className: "text-center align-middle fw-bold",
                    createdCell: function(td, cellData) {
                        if (cellData > 0) {
                            $(td).addClass('table-info');
                        }
                    },
                    render: (data, type) => {
                        if (type === 'sort' || type === 'type') return data;
                        if (data > 0) {
                            return `<span class="badge bg-info text-dark fs-6 px-3 py-1 shadow-sm">${data}</span>`;
                        }
                        return `<span class="text-muted fw-normal">0</span>`;
                    }
                },
                {
                    data: "promedio_mins",
                    className: "text-center align-middle",
                    render: (data, type, row) => {
                        if (type === 'sort' || type === 'type') return data;
                        if (row.atrasos === 0) {
                            return ``;
                        }
                        return `
                            <span class="fw-bold text-danger">${data} min</span>
                            <br>
                            <span class="text-muted small">${row.suma_mins} min total</span>
                        `;
                    }
                },
                {
                    data: "total",
                    className: "text-center align-middle fw-bold table-secondary",
                    render: (data, type) => {
                        if (type === 'sort' || type === 'type') return data;
                        return `<span class="badge bg-secondary fs-6 px-3 py-1 shadow-sm">${data}</span>`;
                    }
                }
            ],
            drawCallback: function() {
                // Recalcula la numeración (#) dinámicamente según el orden y búsqueda actual
                let api = this.api();
                api.column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            },
            ...dtDefaults,
            // Ajustes específicos para ver todas las columnas sin que se colapse automáticamente por responsividad, que sobreescriben a la constante dtDefaults.
            responsive: false,
            lengthChange: true,
            autoWidth: false,
            scrollX: true,
            order: [
                [6, 'desc'], // Primero por Subtotal Disciplinarias (desc)
                [11, 'desc'] // Desempate por Total general (desc)
            ],
        });

        // 2. Inicialización de la Tabla Principal de Incidencias
        let tablaReportes = $("#dataTable").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('estudiantes_asistencias.listar_reporte_incidencias') }}",
                type: "GET",
                data: function(d) {
                    d.rango_fechas = $('#filter_rango_fechas').val();
                    if (d.rango_fechas === 'personalizado') {
                        d.fecha_inicio = $('#fecha_inicio').val();
                        d.fecha_fin = $('#fecha_fin').val();
                    }

                    d.id_nivel = $('#id_nivel').val();
                    d.id_coordinacion = $('#id_coordinacion').val();
                    d.id_curso = $('#id_curso').val();
                    d.id_docente = $('#id_docente').val();
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            drawCallback: function(settings) {
                let api = this.api();
                let filteredData = api.rows({
                    search: 'applied'
                }).data().toArray();

                // 1. Variables de agrupación
                let kpiAtrasos = 0,
                    kpiFaltas = 0,
                    kpiLicencias = 0,
                    kpiPermisos = 0,
                    kpiSuspendidos = 0,
                    sumaMins = 0;
                let objFechas = {},
                    objHorarios = {},
                    objEstudiantes = {},
                    objCursos = {},
                    objAsignaturas = {},
                    objDocentes = {};

                // Helper para inicializar contadores con los 5 tipos de incidencia
                const initIncidencias = () => ({
                    ATRASO: 0,
                    FALTA: 0,
                    LICENCIA: 0,
                    PERMISO: 0,
                    SUSPENDIDO: 0,
                    Total: 0
                });

                const tiposValidos = ['ATRASO', 'FALTA', 'LICENCIA', 'PERMISO', 'SUSPENDIDO'];

                // 2. Iteración sobre la data filtrada
                filteredData.forEach(row => {
                    let t = row.tipo;

                    // Ignorar por seguridad si llegara algún registro que no sea incidencia
                    if (!tiposValidos.includes(t)) return;

                    let minsAtraso = 0;
                    if (t === 'ATRASO') {
                        kpiAtrasos++;
                        minsAtraso = (parseFloat(row.tiempo_atraso) || 0);
                        sumaMins += minsAtraso;
                    } else if (t === 'FALTA') {
                        kpiFaltas++;
                    } else if (t === 'LICENCIA') {
                        kpiLicencias++;
                    } else if (t === 'PERMISO') {
                        kpiPermisos++;
                    } else if (t === 'SUSPENDIDO') {
                        kpiSuspendidos++;
                    }

                    // Extracción segura de datos
                    let fecha = row.estudiante_asistencia?.fecha ?
                        moment(row.estudiante_asistencia.fecha).format('DD/MM/YYYY') :
                        'S/F';
                    let horario = row.estudiante_asistencia?.horario_asignatura
                        ?.hora_inicio ?
                        moment(row.estudiante_asistencia.horario_asignatura.hora_inicio,
                            'HH:mm:ss').format('HH:mm') :
                        'S/H';
                    let estudiante = row.estudiante?.persona ?
                        row.estudiante.persona.apellidos_nombres :
                        'Desc.';
                    let idEstudiante = row.id_estudiante ?? row.estudiante?.id_estudiante ??
                        estudiante;
                    let cursoCompleto = row.estudiante?.curso?.curso || 'Desc.';
                    let curso = row.estudiante?.curso?.curso ? window.Helpers.abreviarCurso(
                        row.estudiante.curso.curso) : 'Desc.';
                    let asignatura = row.estudiante_asistencia?.lista_asignatura?.asignatura
                        ?.asignatura || 'Desc.';
                    let docente = row.estudiante_asistencia?.lista_asignatura?.docente
                        ?.persona ?
                        row.estudiante_asistencia.lista_asignatura.docente.persona
                        .nombres_apellidos :
                        'Sin Asignar';

                    // Acumuladores por Fecha y Horario
                    objFechas[fecha] = (objFechas[fecha] || 0) + 1;
                    if (!objHorarios[horario]) objHorarios[horario] = initIncidencias();

                    // Inicialización de acumuladores por entidad (Estudiantes agrupado por ID)
                    if (!objEstudiantes[idEstudiante]) {
                        objEstudiantes[idEstudiante] = {
                            ...initIncidencias(),
                            nombre: estudiante,
                            curso: cursoCompleto,
                            cursoAbreviado: curso,
                            sumaMins: 0
                        };
                    }
                    if (!objCursos[curso]) objCursos[curso] = initIncidencias();
                    if (!objAsignaturas[asignatura]) objAsignaturas[asignatura] =
                        initIncidencias();
                    if (!objDocentes[docente]) objDocentes[docente] = initIncidencias();

                    // Suma desagregada y total
                    objHorarios[horario][t]++;
                    objHorarios[horario].Total++;

                    objEstudiantes[idEstudiante][t]++;
                    objEstudiantes[idEstudiante].Total++;
                    if (t === 'ATRASO') {
                        objEstudiantes[idEstudiante].sumaMins += minsAtraso;
                    }

                    objCursos[curso][t]++;
                    objCursos[curso].Total++;

                    objAsignaturas[asignatura][t]++;
                    objAsignaturas[asignatura].Total++;

                    objDocentes[docente][t]++;
                    objDocentes[docente].Total++;
                });

                // 3. Pintar KPIs y badges de pestañas
                const totalIncidencias = kpiAtrasos + kpiFaltas + kpiLicencias + kpiPermisos +
                    kpiSuspendidos;
                $('#kpi-total').text(totalIncidencias);
                $('#kpi-atrasos').text(kpiAtrasos);
                $('#kpi-faltas').text(kpiFaltas);
                $('#kpi-licencias').text(kpiLicencias);
                $('#kpi-permisos').text(kpiPermisos);
                $('#kpi-suspendidos').text(kpiSuspendidos);
                $('#kpi-minutos-total').text(sumaMins);
                $('#kpi-minutos-promedio').text(kpiAtrasos > 0 ? (sumaMins / kpiAtrasos).toFixed(
                    1) : 0);

                // Atenuar visualmente las tarjetas KPI cuyos estados están desmarcados
                $('#kpi-atrasos').closest('.card').toggleClass('opacity-50', !estadosActivos
                    .includes('ATRASO'));
                $('#kpi-minutos-total').closest('.card').toggleClass('opacity-50', !estadosActivos
                    .includes('ATRASO'));
                $('#kpi-faltas').closest('.card').toggleClass('opacity-50', !estadosActivos
                    .includes('FALTA'));
                $('#kpi-licencias').closest('.card').toggleClass('opacity-50', !estadosActivos
                    .includes('LICENCIA'));
                $('#kpi-permisos').closest('.card').toggleClass('opacity-50', !estadosActivos
                    .includes('PERMISO'));
                $('#kpi-suspendidos').closest('.card').toggleClass('opacity-50', !estadosActivos
                    .includes('SUSPENDIDO'));

                // 4. Actualizar Tabla de Ranking por Estudiante
                const rankingEstudiantesData = Object.values(objEstudiantes).map(est => {
                    const disciplinarias = est.ATRASO + est.FALTA + est.SUSPENDIDO;
                    const justificadas = est.LICENCIA + est.PERMISO;
                    const promedioMins = est.ATRASO > 0 ?
                        parseFloat((est.sumaMins / est.ATRASO).toFixed(1)) : 0;

                    return {
                        curso: est.curso,
                        curso_abreviado: est.cursoAbreviado,
                        estudiante: est.nombre,
                        atrasos: est.ATRASO,
                        faltas: est.FALTA,
                        suspendidos: est.SUSPENDIDO,
                        disciplinarias: disciplinarias,
                        licencias: est.LICENCIA,
                        permisos: est.PERMISO,
                        justificadas: justificadas,
                        suma_mins: est.sumaMins,
                        promedio_mins: promedioMins,
                        total: est.Total
                    };
                });

                $('#badge-count-incidencias').text(totalIncidencias);
                $('#badge-count-ranking').text(rankingEstudiantesData.length);

                tablaRanking.clear().rows.add(rankingEstudiantesData).draw();

                // Solo incluir en los gráficos los estados que están activos en los checkboxes
                const configsActivos = ESTADOS_CONFIG.filter(cfg => estadosActivos.includes(cfg
                    .key));

                // Helper para generar los datasets apilados dinámicamente según estados activos
                const buildStackedDatasets = (entriesArray, isObjectValues = false, radius =
                    4) => configsActivos.map(cfg => ({
                    label: cfg.label,
                    data: entriesArray.map(item => isObjectValues ? item[cfg.key] :
                        item[1][
                            cfg.key
                        ]),
                    backgroundColor: cfg.color(),
                    borderRadius: radius
                }));

                // Helpers de utilidades
                const trunc = (str, n) => str.length > n ? str.substring(0, n) + '...' : str;

                // Helper para ordenar por Total y extraer un Top N
                const getTopEntries = (dataMap, limit = undefined) => {
                    let sorted = Object.entries(dataMap).sort((a, b) => b[1].Total - a[1]
                        .Total);
                    return limit ? sorted.slice(0, limit) : sorted;
                };

                // Helper unificado para renderizar CUALQUIER gráfico de barras apiladas
                const renderStackedBar = (chartInst, canvasId, labelsArray, datasetData,
                    isObjValues, indexAxis = 'x') => {
                    return initOrUpdateChart(chartInst, canvasId, {
                        type: 'bar',
                        data: {
                            labels: labelsArray.map(l => trunc(l, 50)),
                            datasets: buildStackedDatasets(datasetData, isObjValues, 4)
                        },
                        options: ChartDefaults.getStackedBarOptions({
                            indexAxis
                        })
                    });
                };

                // b) Proporción General (Dona)
                const kpiPorTipo = {
                    ATRASO: kpiAtrasos,
                    FALTA: kpiFaltas,
                    LICENCIA: kpiLicencias,
                    PERMISO: kpiPermisos,
                    SUSPENDIDO: kpiSuspendidos
                };

                chartProporcion = initOrUpdateChart(chartProporcion, 'chart-proporcion', {
                    type: 'doughnut',
                    data: {
                        labels: configsActivos.map(cfg => cfg.label),
                        datasets: [{
                            data: configsActivos.map(cfg => kpiPorTipo[cfg.key]),
                            backgroundColor: configsActivos.map(cfg => cfg.color()),
                            borderWidth: 0
                        }]
                    },
                    options: ChartDefaults.getDoughnutOptions()
                });

                // c) Evolución por Fechas (Líneas)
                let keysFechas = Object.keys(objFechas).sort((a, b) => moment(a, 'DD/MM/YYYY') -
                    moment(b, 'DD/MM/YYYY'));
                chartFechas = initOrUpdateChart(chartFechas, 'chart-fechas', {
                    type: 'line',
                    data: {
                        labels: keysFechas,
                        datasets: [{
                            label: 'Incidencias',
                            data: keysFechas.map(k => objFechas[k]),
                            borderColor: chartPalettes.ui.primary,
                            backgroundColor: 'rgba(13, 110, 253, 0.2)',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: ChartDefaults.getLineOptions()
                });

                // d) Barras Apiladas por Horario (Orden cronológico especial)
                let keysHorarios = Object.keys(objHorarios).sort();
                chartHorarios = renderStackedBar(chartHorarios, 'chart-horarios', keysHorarios,
                    keysHorarios.map(h => objHorarios[h]), true, 'x');

                // e) Rankings y Cursos (Ordenados por volumen de incidencias)
                const topEstudiantes = getTopEntries(objEstudiantes, 20);
                chartRanking = renderStackedBar(chartRanking, 'chart-ranking', topEstudiantes.map(
                    e => e[1].nombre), topEstudiantes, false, 'x');

                const topDocentes = getTopEntries(objDocentes, 20);
                chartDocentes = renderStackedBar(chartDocentes, 'chart-docentes', topDocentes.map(
                    e => e[0]), topDocentes, false, 'x');

                const topAsignaturas = getTopEntries(objAsignaturas, 30);
                chartAsignaturas = renderStackedBar(chartAsignaturas, 'chart-asignaturas',
                    topAsignaturas.map(e => e[0]), topAsignaturas, false, 'x');

                const topCursos = getTopEntries(objCursos); // Sin límite para mostrar todos
                chartCursos = renderStackedBar(chartCursos, 'chart-cursos', topCursos.map(e => e[
                    0]), topCursos, false, 'x');
            },
            columns: [{
                    data: null,
                    className: "text-center align-middle",
                    render: (data, type, row, meta) => meta.row + 1
                },
                {
                    data: "estudiante_asistencia.fecha",
                    className: "align-middle fw-bold",
                    render: data => data ? moment(data).format('DD/MM/YYYY') : ''
                },
                {
                    data: "estudiante_asistencia.horario_asignatura.hora_inicio",
                    className: "align-middle",
                    createdCell: function(td, cellData, rowData, row, col) {
                        if (rowData.estudiante_asistencia.es_horario_no_regular === 1) {
                            $(td).addClass('table-info');
                        }
                    },
                    render: function(data, type, row, meta) {
                        if (!data) return '';

                        const esHorarioNoRegular = row.estudiante_asistencia
                            .es_horario_no_regular === 1;
                        const textoHorario = moment(data, 'HH:mm:ss').format('HH:mm');

                        if (esHorarioNoRegular) {
                            return `
                                <b>${textoHorario}</b>
                                <br>
                                <span class="badge bg-info text-dark ms-1 shadow-sm">
                                    <i class="fa-solid fa-duotone fa-clock me-1"></i>No regular
                                </span>
                            `;
                        }

                        return textoHorario;
                    }
                },
                {
                    data: "estudiante.curso.curso",
                    className: "align-middle text-truncate",
                    render: data => `<span class="badge bg-info text-dark">${data}</span>`
                },
                {
                    data: null,
                    className: "align-middle fw-bold",
                    render: (data, type, row) => row.estudiante?.persona ?
                        `${row.estudiante.persona.apellidos_nombres}` : '-'
                },
                {
                    data: "estudiante_asistencia.lista_asignatura.asignatura.asignatura",
                    className: "align-middle"
                },
                {
                    data: null,
                    className: "align-middle",
                    visible: AUTH_PERFIL !== 'DOCENTE',
                    render: (data, type, row) => row.estudiante_asistencia?.lista_asignatura
                        ?.docente?.persona ?
                        `${row.estudiante_asistencia.lista_asignatura.docente.persona.nombres_apellidos}` :
                        '<span class="text-danger">Sin docente</span>'
                },
                {
                    data: "tipo",
                    className: "text-center align-middle",
                    render: function(data) {
                        if (data === 'ATRASO')
                            return `<span class="badge bg-warning text-dark px-3 py-2 shadow-sm"><i class="fa-solid fa-duotone fa-hourglass-half me-1"></i>Atraso</span>`;
                        if (data === 'FALTA')
                            return `<span class="badge bg-danger px-3 py-2 shadow-sm"><i class="fa-solid fa-duotone fa-times-circle me-1"></i>Falta</span>`;
                        if (data === 'LICENCIA')
                            return `<span class="badge bg-info text-dark px-3 py-2 shadow-sm"><i class="fa-solid fa-duotone fa-file-certificate me-1"></i>Licencia</span>`;
                        if (data === 'PERMISO')
                            return `<span class="badge bg-primary px-3 py-2 shadow-sm"><i class="fa-solid fa-duotone fa-file-signature me-1"></i>Permiso</span>`;
                        if (data === 'SUSPENDIDO')
                            return `<span class="badge bg-dark text-light border border-danger px-3 py-2 shadow-sm"><i class="fa-solid fa-duotone fa-user-slash me-1"></i>Suspendido</span>`;
                        return `<span class="badge bg-secondary px-3 py-2 shadow-sm">-</span>`;
                    }
                },
                {
                    data: "tiempo_atraso",
                    className: "text-center align-middle",
                    render: (data, type, row) => (row.tipo === 'ATRASO' && data) ?
                        `<span class="text-danger fw-bold">${data} min</span>` : ''
                },
                {
                    data: null,
                    className: "align-middle",
                    render: function(data, type, row) {
                        if (row.tipo === 'LICENCIA' && row.estudiante_licencia) {
                            const l = row.estudiante_licencia;
                            const urlEvidencia = l.evidencia;
                            const btnEvidencia = urlEvidencia ? `
                            <br>
                            <a href="${urlEvidencia}" target="_blank"
                                class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip"
                                title="Abrir archivo en Google Drive">
                                <i class="fa-brands fa-google-drive"></i> Ver
                            </a>` : '';
                            return `
                                <span class="d-inline-block" style="max-width: 220px;"
                                    title="${l.justificacion}">
                                    ${l.justificacion || 'Sin justificación'}
                                </span>
                                ${btnEvidencia}
                            `;
                        }
                        if (row.tipo === 'PERMISO') {
                            const j = row.justificacion_permiso;
                            return `<span class="text-primary fw-semibold">${j || 'Sin justificación'}</span>`;
                        }
                        if (row.tipo === 'SUSPENDIDO' && row.estudiante_suspension) {
                            const s = row.estudiante_suspension;
                            const urlEvidencia = s.evidencia;
                            const btnEvidencia = urlEvidencia ? `
                            <br>
                            <a href="${urlEvidencia}" target="_blank"
                                class="btn btn-sm btn-outline-info mt-1" data-bs-toggle="tooltip"
                                title="Abrir archivo en Google Drive">
                                <i class="fa-brands fa-google-drive"></i> Ver
                            </a>` : '';
                            return `
                                <span class="d-inline-block text-danger fw-semibold" style="max-width: 220px;"
                                    title="${s.motivo}">
                                    ${s.motivo || 'Sin motivo registrado'}
                                </span>
                                ${btnEvidencia}
                            `;
                        }
                        return '';
                    }
                }
            ],
            ...dtDefaults,
            // Ajustes específicos para ver todas las columnas sin que se colapse automáticamente por responsividad, que sobreescriben a la constante dtDefaults.
            responsive: false,
            lengthChange: true,
            autoWidth: false,
            scrollX: true,
        });

        // Sincroniza la visibilidad de columnas según los estados marcados y redibuja todo en cascada
        const aplicarFiltroEstados = () => {
            estadosActivos = getEstadosActivos();

            const verAtraso = estadosActivos.includes('ATRASO');
            const verFalta = estadosActivos.includes('FALTA');
            const verSuspendido = estadosActivos.includes('SUSPENDIDO');
            const verLicencia = estadosActivos.includes('LICENCIA');
            const verPermiso = estadosActivos.includes('PERMISO');

            // Columna "Atraso" (minutos) en la tabla principal
            tablaReportes.column(8).visible(verAtraso, false);

            // Columnas de estados y subtotales en la tabla de ranking
            tablaRanking.column(3).visible(verAtraso, false);
            tablaRanking.column(4).visible(verFalta, false);
            tablaRanking.column(5).visible(verSuspendido, false);
            tablaRanking.column(6).visible(verAtraso || verFalta || verSuspendido, false);
            tablaRanking.column(7).visible(verLicencia, false);
            tablaRanking.column(8).visible(verPermiso, false);
            tablaRanking.column(9).visible(verLicencia || verPermiso, false);
            tablaRanking.column(10).visible(verAtraso, false);

            tablaReportes.columns.adjust().draw();
            tablaRanking.columns.adjust();
        };

        $('.filter-estado').on('change', aplicarFiltroEstados);

        $('#btn-estados-todos').on('click', function() {
            $('.filter-estado').prop('checked', true);
            aplicarFiltroEstados();
        });

        $('#btn-estados-ninguno').on('click', function() {
            $('.filter-estado').prop('checked', false);
            aplicarFiltroEstados();
        });

        // Reajustar anchos de columnas de DataTables al cambiar entre pestañas
        $('#tablasTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
            $.fn.dataTable.tables({
                visible: true,
                api: true
            }).columns.adjust();
        });

        $("#btn-filtrar").on("click", function(e) {
            e.preventDefault();
            tablaReportes.ajax.reload();
        });
    });
</script>
