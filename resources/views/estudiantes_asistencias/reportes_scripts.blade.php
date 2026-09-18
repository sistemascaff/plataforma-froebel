<script>
    Chart.register(ChartDataLabels);

    // Variables globales de gráficos
    let chartProporcion, chartFechas, chartHorarios, chartRanking, chartCursos, chartAsignaturas, chartDocentes;

    // Función para resolver colores según el tema activo en el DOM (o localStorage)
    function getThemeColors() {
        const temaPreferido = localStorage.getItem('theme') || document.documentElement.getAttribute('data-bs-theme') ||
            'light';
        return {
            textColor: temaPreferido === 'dark' ? '#f8f9fa' : '#212529',
            gridColor: temaPreferido === 'dark' ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
        };
    }

    // Colores semánticos de incidencias
    const palette = {
        atraso: '#ffc107',
        falta: '#dc3545',
        licencia: '#0dcaf0'
    };

    // Función core para construir o actualizar un gráfico
    function initOrUpdateChart(chartInstance, canvasId, configOptions) {
        const ctx = document.getElementById(canvasId).getContext('2d');
        if (chartInstance) {
            chartInstance.data = configOptions.data;
            chartInstance.options = configOptions.options;
            chartInstance.update();
            return chartInstance;
        }
        return new Chart(ctx, configOptions);
    }

    $(document).ready(function() {
        $('#id_docente').select2({
            width: '100%',
            language: "es",
            dropdownCssClass: localStorage.getItem('theme') === 'dark' ? 'bg-dark text-white' : '',
            selectionCssClass: localStorage.getItem('theme') === 'dark' ? 'bg-dark text-white' : '',
        });

        // Reajustar gráficos al cambiar de pestaña en Bootstrap (previene canvas comprimidos)
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            if (chartProporcion) chartProporcion.resize();
            if (chartFechas) chartFechas.resize();
            if (chartHorarios) chartHorarios.resize();
            if (chartRanking) chartRanking.resize();
            if (chartCursos) chartCursos.resize();
            if (chartAsignaturas) chartAsignaturas.resize();
            if (chartDocentes) chartDocentes.resize();
        });

        // Inicialización de la Tabla
        let tablaReportes = $("#dataTable").DataTable({
            processing: true,
            @include('components.datatables.datatables_global_properties')
            @include('components.datatables.datatables_language_property'),
            ajax: {
                url: "{{ route('estudiantes_asistencias.listar_reporte_incidencias') }}",
                type: "GET",
                data: function(d) {
                    d.fecha_inicio = $('#fecha_inicio').val();
                    d.fecha_fin = $('#fecha_fin').val();
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
                const {
                    textColor,
                    gridColor
                } = getThemeColors();

                // 1. Variables de agrupación
                let kpiAtrasos = 0,
                    kpiFaltas = 0,
                    kpiLicencias = 0,
                    sumaMins = 0;
                let objFechas = {},
                    objHorarios = {},
                    objEstudiantes = {},
                    objCursos = {},
                    objAsignaturas = {},
                    objDocentes = {};

                // 2. Iteración sobre la data filtrada
                filteredData.forEach(row => {
                    let t = row.tipo;
                    if (t === 'A') {
                        kpiAtrasos++;
                        sumaMins += (parseFloat(row.tiempo_atraso) || 0);
                    } else if (t === 'F') kpiFaltas++;
                    else if (t === 'L') kpiLicencias++;

                    // Extracción segura de datos
                    let fecha = row.estudiante_asistencia?.fecha ? moment(row
                        .estudiante_asistencia.fecha).format('DD/MM/YYYY') : 'S/F';
                    let horario = row.estudiante_asistencia?.horario_asignatura
                        ?.hora_inicio ? moment(row.estudiante_asistencia.horario_asignatura
                            .hora_inicio, 'HH:mm:ss').format('HH:mm') : 'S/H';
                    let estudiante = row.estudiante?.persona ? row.estudiante.persona
                        .apellidos_nombres : 'Desc.';
                    let curso = row.estudiante?.curso?.curso || 'Desc.';
                    let asignatura = row.estudiante_asistencia?.lista_asignatura?.asignatura
                        ?.asignatura || 'Desc.';
                    let docente = row.estudiante_asistencia?.lista_asignatura?.docente
                        ?.persona ? row.estudiante_asistencia.lista_asignatura.docente
                        .persona.nombres_apellidos : 'Sin Asignar';

                    // Acumuladores
                    objFechas[fecha] = (objFechas[fecha] || 0) + 1;
                    if (!objHorarios[horario]) objHorarios[horario] = {
                        A: 0,
                        F: 0,
                        L: 0
                    };
                    if (['A', 'F', 'L'].includes(t)) objHorarios[horario][t]++;

                    objEstudiantes[estudiante] = (objEstudiantes[estudiante] || 0) + 1;
                    objCursos[curso] = (objCursos[curso] || 0) + 1;
                    objAsignaturas[asignatura] = (objAsignaturas[asignatura] || 0) + 1;
                    objDocentes[docente] = (objDocentes[docente] || 0) + 1;
                });

                // 3. Pintar KPIs
                $('#kpi-total').text(filteredData.length);
                $('#kpi-atrasos').text(kpiAtrasos);
                $('#kpi-faltas').text(kpiFaltas);
                $('#kpi-licencias').text(kpiLicencias);
                $('#kpi-minutos-total').text(sumaMins);
                $('#kpi-minutos-promedio').text(kpiAtrasos > 0 ? (sumaMins / kpiAtrasos).toFixed(
                    1) : 0);

                // --- Opciones Globales Base para Gráficos ---
                const baseOpts = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                color: textColor
                            }
                        },
                        datalabels: {
                            color: textColor,
                            font: {
                                weight: 'bold'
                            }
                        }
                    },
                    scales: {
                        x: {
                            ticks: {
                                color: textColor
                            },
                            grid: {
                                color: gridColor
                            }
                        },
                        y: {
                            ticks: {
                                color: textColor,
                                precision: 0
                            },
                            grid: {
                                color: gridColor
                            },
                            beginAtZero: true
                        }
                    }
                };

                // b) Proporción General (Dona)
                chartProporcion = initOrUpdateChart(chartProporcion, 'chart-proporcion', {
                    type: 'doughnut',
                    data: {
                        labels: ['Atrasos', 'Faltas', 'Licencias'],
                        datasets: [{
                            data: [kpiAtrasos, kpiFaltas, kpiLicencias],
                            backgroundColor: Object.values(palette),
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    color: textColor
                                }
                            },
                            datalabels: {
                                color: '#fff',
                                font: {
                                    weight: 'bold',
                                    size: 14
                                },
                                textShadowBlur: 4,
                                textShadowColor: 'rgba(0,0,0,0.8)',
                                formatter: val => val > 0 ? val : ''
                            }
                        }
                    }
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
                            borderColor: '#0d6efd',
                            backgroundColor: 'rgba(13, 110, 253, 0.2)',
                            fill: true,
                            tension: 0.3
                        }]
                    },
                    options: {
                        ...baseOpts,
                        plugins: {
                            ...baseOpts.plugins,
                            datalabels: {
                                align: 'top',
                                color: textColor,
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                });

                // d) Barras Apiladas por Horario
                let keysHorarios = Object.keys(objHorarios).sort();
                chartHorarios = initOrUpdateChart(chartHorarios, 'chart-horarios', {
                    type: 'bar',
                    data: {
                        labels: keysHorarios,
                        datasets: [{
                                label: 'Atrasos',
                                data: keysHorarios.map(h => objHorarios[h].A),
                                backgroundColor: palette.atraso
                            },
                            {
                                label: 'Faltas',
                                data: keysHorarios.map(h => objHorarios[h].F),
                                backgroundColor: palette.falta
                            },
                            {
                                label: 'Licencias',
                                data: keysHorarios.map(h => objHorarios[h].L),
                                backgroundColor: palette.licencia
                            }
                        ]
                    },
                    options: {
                        ...baseOpts,
                        plugins: {
                            ...baseOpts.plugins,
                            datalabels: {
                                color: textColor,
                                formatter: v => v > 0 ? v : ''
                            }
                        },
                        scales: {
                            x: {
                                stacked: true,
                                ticks: {
                                    color: textColor
                                },
                                grid: {
                                    color: gridColor
                                }
                            },
                            y: {
                                stacked: true,
                                ticks: {
                                    color: textColor,
                                    precision: 0
                                },
                                grid: {
                                    color: gridColor
                                },
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Helper para recortes de texto
                const trunc = (str, n) => str.length > n ? str.substring(0, n) + '...' : str;

                // e) Ranking Top 20 Estudiantes (Barras Horizontales)
                let topEst = Object.entries(objEstudiantes).sort((a, b) => b[1] - a[1]).slice(0,
                20);
                chartRanking = initOrUpdateChart(chartRanking, 'chart-ranking', {
                    type: 'bar',
                    data: {
                        labels: topEst.map(e => trunc(e[0], 25)),
                        datasets: [{
                            label: 'Total Incidencias',
                            data: topEst.map(e => e[1]),
                            backgroundColor: '#dc3545',
                            borderRadius: 4
                        }]
                    },
                    options: {
                        ...baseOpts,
                        indexAxis: 'y'
                    }
                });

                // f, g, h) Helper genérico para Cursos, Asignaturas y Docentes
                const renderBar = (chartInst, canvas, dataMap, color, label) => {
                    let sorted = Object.entries(dataMap).sort((a, b) => b[1] - a[1]);
                    return initOrUpdateChart(chartInst, canvas, {
                        type: 'bar',
                        data: {
                            labels: sorted.map(e => trunc(e[0], 25)),
                            datasets: [{
                                label: label,
                                data: sorted.map(e => e[1]),
                                backgroundColor: color,
                                borderRadius: 4
                            }]
                        },
                        options: baseOpts
                    });
                };

                chartCursos = renderBar(chartCursos, 'chart-cursos', objCursos, '#6f42c1',
                    'Incidencias por Curso');
                chartAsignaturas = renderBar(chartAsignaturas, 'chart-asignaturas', objAsignaturas,
                    '#20c997', 'Incidencias por Asignatura');
                chartDocentes = renderBar(chartDocentes, 'chart-docentes', objDocentes, '#fd7e14',
                    'Incidencias por Docente');
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
                    className: "align-middle text-muted",
                    render: data => data ? moment(data, 'HH:mm:ss').format('HH:mm') : ''
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
                    render: (data, type, row) => row.estudiante_asistencia?.lista_asignatura
                        ?.docente?.persona ?
                        `${row.estudiante_asistencia.lista_asignatura.docente.persona.nombres_apellidos}` :
                        '<span class="text-danger">Sin docente</span>'
                },
                {
                    data: "tipo",
                    className: "text-center align-middle",
                    render: function(data) {
                        if (data === 'A')
                        return `<span class="badge bg-warning text-dark px-3 py-2 shadow-sm"><i class="fa-solid fa-duotone fa-hourglass-half me-1"></i> Atraso</span>`;
                        if (data === 'F')
                        return `<span class="badge bg-danger px-3 py-2 shadow-sm"><i class="fa-solid fa-duotone fa-times-circle me-1"></i> Falta</span>`;
                        if (data === 'L')
                        return `<span class="badge bg-info text-dark px-3 py-2 shadow-sm"><i class="fa-solid fa-duotone fa-file-certificate me-1"></i> Licencia</span>`;
                        return `<span class="badge bg-secondary px-3 py-2 shadow-sm">-</span>`;
                    }
                },
                {
                    data: "tiempo_atraso",
                    className: "text-center align-middle",
                    render: (data, type, row) => (row.tipo === 'A' && data) ?
                        `<span class="text-danger fw-bold">${data} min</span>` :
                        '<span class="text-muted">-</span>'
                },
                {
                    data: null,
                    className: "align-middle",
                    render: (data, type, row) => (row.tipo === 'L' && row.estudiante_licencia) ?
                        `<span class="d-inline-block" style="max-width: 200px;" title="${row.estudiante_licencia.justificacion}">${row.estudiante_licencia.justificacion || 'Sin justificación'}</span>` :
                        '<span class="text-muted text-center d-block">-</span>'
                }
            ]
        });

        $("#btn-filtrar").on("click", function(e) {
            e.preventDefault();
            tablaReportes.ajax.reload();
        });

        // ----------------------------------------------------------------------
        // OBSERVADOR DINÁMICO DE TEMA (Actualización en tiempo real sin recargar)
        // ----------------------------------------------------------------------
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === "data-bs-theme") {
                    // 1. Obtener los colores del nuevo tema asignado
                    const {
                        textColor,
                        gridColor
                    } = getThemeColors();
                    const charts = [chartProporcion, chartFechas, chartHorarios, chartRanking,
                        chartCursos, chartAsignaturas, chartDocentes
                    ];

                    // 2. Iterar e inyectar el nuevo color en caliente a cada gráfico existente
                    charts.forEach(chart => {
                        if (chart) {
                            // Etiquetas de los ejes X e Y y Cuadrículas
                            if (chart.options.scales?.x) {
                                chart.options.scales.x.ticks.color = textColor;
                                chart.options.scales.x.grid.color = gridColor;
                            }
                            if (chart.options.scales?.y) {
                                chart.options.scales.y.ticks.color = textColor;
                                chart.options.scales.y.grid.color = gridColor;
                            }
                            // Leyenda inferior
                            if (chart.options.plugins?.legend?.labels) {
                                chart.options.plugins.legend.labels.color = textColor;
                            }
                            // DataLabels flotantes (excepto Dona, que siempre usa texto blanco por contraste interno)
                            if (chart.config.type !== 'doughnut' && chart.options
                                .plugins?.datalabels) {
                                chart.options.plugins.datalabels.color = textColor;
                            }
                            // Aplicar actualización visual
                            chart.update();
                        }
                    });
                }
            });
        });

        // Observamos el <html> porque Bootstrap 5 inyecta el `data-bs-theme` ahí al hacer clic en el toggle
        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-bs-theme']
        });
    });
</script>
