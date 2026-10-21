<script>
    const AUTH_PERFIL = '{{ Auth::user()->persona?->tipo_perfil }}';
    const ES_ADMINISTRADOR = AUTH_PERFIL === 'ADMINISTRADOR';
    const ROLES_PERMITIDOS = ['SECRETARIA ACADEMICA'];
    const PUEDE_CREAR = ROLES_PERMITIDOS.includes(AUTH_PERFIL) || ES_ADMINISTRADOR;
    const PUEDE_EDITAR = ROLES_PERMITIDOS.includes(AUTH_PERFIL) || ES_ADMINISTRADOR;
    const PUEDE_ELIMINAR = ROLES_PERMITIDOS.includes(AUTH_PERFIL) || ES_ADMINISTRADOR;

    // Variables globales para gráficos
    let chartCursos, chartTipos;

    function getThemeColors() {
        const temaPreferido = localStorage.getItem('theme') || document.documentElement.getAttribute('data-bs-theme') ||
            'light';
        return {
            textColor: temaPreferido === 'dark' ? '#f8f9fa' : '#212529',
            gridColor: temaPreferido === 'dark' ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)'
        };
    }

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
        // --- Registro de complementos de Chart.js si se usa ---
        if (typeof ChartDataLabels !== 'undefined') {
            Chart.register(ChartDataLabels);
        }


        // --- Inicialización de select2 específica para esta vista, debido a que si se inicializan por separado se rompe. ---
        $('#filter_id_grado, #filter_id_curso, .select2').each(function() {
            let $select = $(this);

            // 1. Definimos la configuración base que comparten todos
            let configuracion = {
                width: '100%', // Agregado para evitar problemas de renderizado invisible
                language: "es",
                dropdownCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' :
                    '',
                selectionCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' :
                    ''
            };

            // 2. Evaluamos la condición: ¿tiene la clase .select2?
            if ($select.hasClass('select2')) {
                configuracion.dropdownParent = $('#modal-formulario');
            }

            // 3. Inicializamos Select2 con la configuración resultante
            $select.select2(configuracion);
        });

        function recargarEstudiantesSelect() {
            $.ajax({
                url: "{{ route('estudiantes.listar') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    let $select = $("#id_estudiante");
                    $select.empty();
                    $select.append('<option value="">-- Seleccione a un estudiante --</option>');

                    $.each(response.data, function(i, estudiante) {
                        // Omitir estudiantes inactivos
                        if (estudiante.estado == '0') return;

                        let fila =
                            `(${estudiante.curso.curso}) ${estudiante.persona.apellidos_nombres}`;

                        $select.append(
                            `<option value="${estudiante.id_estudiante}">${fila}</option>`
                        );
                    });

                    // ====================================================================
                    // Apertura automática del modal desde redirección
                    // ====================================================================
                    const urlParams = new URLSearchParams(window.location.search);
                    const idEstudianteUrl = urlParams.get('id_estudiante');

                    if (idEstudianteUrl) {
                        // 1. Ejecutar la misma lógica de reseteo y apertura del modal
                        $('.btn-crear')
                            .click(); // 2. Sobrescribir el valor del select con el ID recibido en la URL
                        $('#form-crear-o-editar select[name="id_estudiante"]').val(idEstudianteUrl)
                            .trigger('change');

                        // 3. Limpiar la barra de direcciones para que no se vuelva a abrir el modal si el usuario presiona F5
                        window.history.replaceState({}, document.title, window.location.pathname);
                    }
                    // ====================================================================
                }
            });
        }

        recargarEstudiantesSelect();

        $("#dataTable").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('estudiantes_licencias.listar') }}",
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    d.id_nivel = $('#filter_id_nivel').val();
                    d.id_grado = $('#filter_id_grado').val();
                    d.id_curso = $('#filter_id_curso').val();
                    d.id_paralelo = $('#filter_id_paralelo').val();
                    d.estado = $('#filter_estado').val();
                },
                error: function(xhr, error, thrown) {
                    console.error("Error al cargar los datos:", error);
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

                // 1. Contadores
                let kpiTotal = 0;
                let kpiProgramadas = 0;
                let kpiVigentes = 0;
                let kpiFinalizadas = 0;

                let objCursos = {};
                let objTipos = {};
                const hoy = moment();

                // Extracción y agrupación de datos filtrados
                filteredData.forEach(row => {
                    kpiTotal++;

                    // 2. Validación de estados con la nueva lógica
                    const fechaInicio = moment(row.fecha_inicio);
                    const fechaFin = moment(row.fecha_fin);

                    if (hoy.isBefore(fechaInicio)) {
                        kpiProgramadas++;
                    } else if (hoy.isAfter(fechaFin)) {
                        kpiFinalizadas++;
                    } else {
                        kpiVigentes++;
                    }

                    // Agrupación por curso
                    let curso = window.Helpers.abreviarCurso(row.estudiante?.curso
                        ?.curso) || 'Desconocido';
                    objCursos[curso] = (objCursos[curso] || 0) + 1;

                    // Agrupación por tipo (motivo)
                    let tipoBruto = row.tipo || 'desconocido';
                    let tipoFormateado = tipoBruto.replace(/_/g, ' ');
                    tipoFormateado = tipoFormateado.charAt(0).toUpperCase() + tipoFormateado
                        .slice(1);
                    objTipos[tipoFormateado] = (objTipos[tipoFormateado] || 0) + 1;
                });

                // 3. Actualizar tarjetas HTML con los IDs correspondientes
                $('#kpi-total').text(kpiTotal);
                $('#kpi-programadas').text(kpiProgramadas);
                $('#kpi-vigentes').text(kpiVigentes);
                $('#kpi-finalizadas').text(kpiFinalizadas);

                // Configuración estándar para los gráficos (Sin cambios)
                const baseOpts = {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        datalabels: {
                            color: textColor,
                            font: {
                                weight: 'bold'
                            },
                            formatter: v => v > 0 ? v : ''
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

                const sortObjectDesc = (obj) => Object.entries(obj).sort((a, b) => b[1] - a[1]);

                // Renderizar gráfico de cursos (Sin cambios)
                let sortedCursos = sortObjectDesc(objCursos);
                chartCursos = initOrUpdateChart(chartCursos, 'chart-cursos', {
                    type: 'bar',
                    data: {
                        labels: sortedCursos.map(e => e[0]),
                        datasets: [{
                            data: sortedCursos.map(e => e[1]),
                            backgroundColor: '#0dcaf0',
                            borderRadius: 4
                        }]
                    },
                    options: baseOpts
                });

                // Renderizar gráfico de tipos/motivos (Sin cambios)
                let sortedTipos = sortObjectDesc(objTipos);
                chartTipos = initOrUpdateChart(chartTipos, 'chart-tipos', {
                    type: 'bar',
                    data: {
                        labels: sortedTipos.map(e => e[0]),
                        datasets: [{
                            data: sortedTipos.map(e => e[1]),
                            backgroundColor: '#0d6efd',
                            borderRadius: 4
                        }]
                    },
                    options: baseOpts
                });
            },
            columns: [{
                    data: null,
                    render: (data, type, row, meta) => meta.row + 1
                },
                {
                    data: "estudiante.curso.curso",
                    render: data => `<span class="badge bg-info text-dark">${data}</span>`
                },
                {
                    data: null,
                    render: (data, type, row) => `${row.estudiante.persona.apellidos_nombres}`
                },
                {
                    data: "tipo",
                    render: function(data) {
                        let texto = data.replace(/_/g, ' ');
                        return texto.charAt(0).toUpperCase() + texto.slice(1);
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `<span class="badge bg-primary text-white mb-1">Desde ${moment(row.fecha_inicio).format('DD/MM/YYYY HH:mm')}</span><br>
                                <span class="badge bg-danger text-white">Hasta ${moment(row.fecha_fin).format('DD/MM/YYYY HH:mm')}</span>`;
                    }
                },
                {
                    data: "justificacion"
                },
                {
                    data: "evidencia",
                    render: function(data) {
                        if (data) {
                            return `<a href="${data}" target="_blank" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Abrir archivo en Google Drive">
                                        <i class="fa-brands fa-google-drive"></i> Ver
                                    </a>`;
                        }
                        return '<span class="text-muted fst-italic">Sin evidencia</span>';
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        const hoy = moment();
                        const fecha_inicio = moment(row.fecha_inicio);
                        const fecha_fin = moment(row.fecha_fin);

                        if (hoy.isBefore(fecha_inicio)) {
                            // El momento actual es ANTES de la fecha de inicio
                            return `<span class="badge bg-info">PROGRAMADA</span>`;
                        } else if (hoy.isAfter(fecha_fin)) {
                            // El momento actual es DESPUÉS de la fecha de fin
                            return `<span class="badge bg-danger">FINALIZADA</span>`;
                        } else {
                            // El momento actual está ENTRE la fecha de inicio y fin (inclusive)
                            return `<span class="badge bg-success">VIGENTE</span>`;
                        }
                    }
                },
                {
                    data: 'solicitado_por',
                    render: function(data) {
                        const labels = {
                            'PADRE': 'Padre',
                            'MADRE': 'Madre',
                            'TUTOR': 'Tutor'
                        };
                        return labels[data] || data;
                    }
                },
                {
                    data: "estado",
                    visible: ES_ADMINISTRADOR,
                    render: data => data == 1 ? '<span class="badge bg-success">ACTIVO</span>' :
                        '<span class="badge bg-secondary">ARCHIVADO</span>'
                },
                {
                    data: "fecha_registro",
                    visible: ES_ADMINISTRADOR,
                    render: data => data ? moment(data).format('DD/MM/YYYY HH:mm:ss') : ''
                },
                {
                    data: "fecha_actualizacion",
                    visible: ES_ADMINISTRADOR,
                    render: data => data ? moment(data).format('DD/MM/YYYY HH:mm:ss') : ''
                },
                {
                    data: "fecha_eliminacion",
                    visible: ES_ADMINISTRADOR,
                    render: data => data ? moment(data).format('DD/MM/YYYY HH:mm:ss') : ''
                },
                {
                    data: "creado.correo",
                    visible: ES_ADMINISTRADOR,
                    render: data => data || '-'
                },
                {
                    data: "modificado.correo",
                    visible: ES_ADMINISTRADOR,
                    render: data => data || '-'
                },
                {
                    data: "eliminado.correo",
                    visible: ES_ADMINISTRADOR,
                    render: data => data || '-'
                },
                {
                    data: "ip",
                    visible: ES_ADMINISTRADOR,
                    render: data => data || '-'
                },
                {
                    data: "dispositivo",
                    visible: ES_ADMINISTRADOR,
                    render: data => data || '-'
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    visible: PUEDE_EDITAR,
                    render: function(data, type, row) {
                        let botones = `<div class="btn-group" role="group">`;

                        if (PUEDE_EDITAR) {
                            botones +=
                                `<button class="btn btn-sm btn-warning btn-editar" data-id="${row.id_estudiante_licencia}">
                                    <i class="fa-solid fa-duotone fa-pen-to-square"></i>
                                </button>`
                        }

                        if (PUEDE_ELIMINAR) {
                            botones +=
                                `<button class="btn btn-sm btn-danger btn-eliminar" data-id="${row.id_estudiante_licencia}">
                                    <i class="fa-solid fa-duotone fa-trash"></i>
                                </button>`
                        }

                        botones += `</div>`;

                        return botones;
                    }
                }
            ],
            ...dtDefaults,
        }).buttons().container().appendTo('#dataTable-export-buttons-container');

        $("#btn-filtrar").on("click", function(e) {
            e.preventDefault();
            $("#dataTable").DataTable().ajax.reload();
        });

        $(document).on('click', '.btn-crear', function() {
            $('#form-crear-o-editar input[name="id_estudiante_licencia"]').val(0);

            // 1. Nos aseguramos de HABILITAR el select al crear
            $('#form-crear-o-editar select[name="id_estudiante"]').prop('disabled', false).val('')
                .trigger('change');

            $('#form-crear-o-editar select[name="tipo"]').val('').trigger('change');
            $('#form-crear-o-editar input[name="fecha_inicio"]').val('');
            $('#form-crear-o-editar input[name="fecha_fin"]').val('');
            $('#form-crear-o-editar textarea[name="justificacion"]').val('');
            $('#form-crear-o-editar input[name="evidencia"]').val('');

            const titleElement = document.getElementById('modal-formulario-titulo');
            titleElement.innerHTML =
                '<i class="fa-solid fa-duotone fa-plus"></i> CREAR LICENCIA DE ESTUDIANTE';
            $('#modal-formulario').modal('show');
        });

        $(document).on('click', '.btn-editar', function() {
            const id = $(this).data('id');

            $.get("{{ route('estudiantes_licencias.index') . '/' }}" + id, function(
                estudiante_licencia) {
                $('#form-crear-o-editar input[name="id_estudiante_licencia"]').val(
                    estudiante_licencia.data.id_estudiante_licencia);

                // 2. Asignamos el valor y DESHABILITAMOS el select para evitar cambios
                $('#form-crear-o-editar select[name="id_estudiante"]')
                    .val(estudiante_licencia.data.id_estudiante)
                    .trigger('change')
                    .prop('disabled', true);

                $('#form-crear-o-editar select[name="solicitado_por"]').val(estudiante_licencia
                    .data.solicitado_por).trigger('change');
                $('#form-crear-o-editar select[name="tipo"]').val(estudiante_licencia.data.tipo)
                    .trigger('change');
                $('#form-crear-o-editar input[name="fecha_inicio"]').val(estudiante_licencia
                    .data.fecha_inicio);
                $('#form-crear-o-editar input[name="fecha_fin"]').val(estudiante_licencia.data
                    .fecha_fin);
                $('#form-crear-o-editar textarea[name="justificacion"]').val(estudiante_licencia
                    .data.justificacion);
                $('#form-crear-o-editar input[name="evidencia"]').val(estudiante_licencia.data
                    .evidencia);

                const titleElement = document.getElementById('modal-formulario-titulo');
                titleElement.innerHTML =
                    '<i class="fa-solid fa-duotone fa-edit"></i> EDITAR LICENCIA DE ESTUDIANTE';
                $('#modal-formulario').modal('show');
            });
        });

        $('#form-crear-o-editar').submit(function(e) {
            e.preventDefault();

            let id = $('#id_estudiante_licencia').val();
            let url = id == "0" ? "{{ route('estudiantes_licencias.create') }}" :
                `{{ url('estudiantes_licencias') }}/${id}`;
            let method = id == "0" ? "POST" : "PUT";

            let btn = $('#btn-guardar');
            btn.prop('disabled', true).html(
                '<i class="fa-solid fa-duotone fa-spinner fa-spin"></i> Guardando...');

            // 3. Habilitar temporalmente para que serialize() pueda capturar el id_estudiante
            let selectEstudiante = $('#form-crear-o-editar select[name="id_estudiante"]');
            let estabaDeshabilitado = selectEstudiante.prop('disabled');
            if (estabaDeshabilitado) selectEstudiante.prop('disabled', false);

            let formData = $(this).serialize();

            // 4. Restaurar el estado deshabilitado inmediatamente después de serializar
            if (estabaDeshabilitado) selectEstudiante.prop('disabled', true);

            $.ajax({
                url: url,
                type: method,
                data: formData, // Usamos la variable ya serializada
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#modal-formulario').modal('hide');
                    $('#dataTable').DataTable().ajax.reload();
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        title: '¡Éxito!',
                        text: response.message,
                        icon: 'success'
                    });
                },
                error: function(xhr) {
                    let htmlError = "Ocurrió un error inesperado.";
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        htmlError = Object.values(xhr.responseJSON.errors).flat().join(
                            "<br>");
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        htmlError = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        title: 'Error',
                        html: htmlError,
                        icon: 'error'
                    });
                },
                complete: function() {
                    btn.prop('disabled', false).html(
                        '<i class="fa-solid fa-duotone fa-save me-1"></i>Guardar');
                }
            });
        });

        $(document).on('click', '.btn-eliminar', function() {
            let id = $(this).data('id');
            Swal.fire({
                theme: localStorage.getItem('theme') || 'dark',
                title: '¿Estás seguro?',
                text: "Se eliminará esta licencia del registro del estudiante.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('estudiantes_licencias.delete', ':id') }}`
                            .replace(':id',
                                id),
                        type: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            $('#dataTable').DataTable().ajax.reload();
                            Swal.fire({
                                theme: localStorage.getItem('theme') ||
                                    'dark',
                                title: 'Eliminado',
                                html: response.message,
                                icon: 'success'
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                theme: localStorage.getItem('theme') ||
                                    'dark',
                                title: 'Error',
                                text: 'No se pudo eliminar la licencia.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });

        // ----------------------------------------------------------------------
        // OBSERVADOR DINÁMICO DE TEMA 
        // ----------------------------------------------------------------------
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === "data-bs-theme") {
                    const {
                        textColor,
                        gridColor
                    } = getThemeColors();
                    const charts = [chartCursos, chartTipos];

                    charts.forEach(chart => {
                        if (chart) {
                            if (chart.options.scales?.x) {
                                chart.options.scales.x.ticks.color = textColor;
                                chart.options.scales.x.grid.color = gridColor;
                            }
                            if (chart.options.scales?.y) {
                                chart.options.scales.y.ticks.color = textColor;
                                chart.options.scales.y.grid.color = gridColor;
                            }
                            chart.update();
                        }
                    });
                }
            });
        });

        observer.observe(document.documentElement, {
            attributes: true,
            attributeFilter: ['data-bs-theme']
        });
    });
</script>
