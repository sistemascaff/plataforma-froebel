<script>
    const AUTH_PERFIL = '{{ Auth::user()->persona?->tipo_perfil }}';
    const ES_ADMINISTRADOR = AUTH_PERFIL === 'ADMINISTRADOR';
    const ROLES_PERMITIDOS = ['DIRECTOR DE NIVEL'];
    const PUEDE_CREAR = ROLES_PERMITIDOS.includes(AUTH_PERFIL) || ES_ADMINISTRADOR;
    const PUEDE_EDITAR = ROLES_PERMITIDOS.includes(AUTH_PERFIL) || ES_ADMINISTRADOR;
    const PUEDE_ELIMINAR = ROLES_PERMITIDOS.includes(AUTH_PERFIL) || ES_ADMINISTRADOR;

    // Variables globales para gráficos
    let chartCursos, chartMeses;

    $(document).ready(function() {
        function formatearEstudiante(opcion) {
            if (!opcion.id) {
                return opcion.text;
            }

            let curso = $(opcion.element).data('curso');
            let nombre = $(opcion.element).data('nombre');

            if (!curso || !nombre) {
                return opcion.text;
            }

            return $(`<span><span class="badge bg-info text-dark">${curso}</span> ${nombre}</span>`);
        }

        $('#filter_id_grado, #filter_id_curso, .select2').each(function() {
            let $select = $(this);
            let isDark = localStorage.getItem('theme') === 'dark';

            let configuracion = {
                width: '100%',
                language: "es",
                dropdownCssClass: isDark ? 'bg-dark text-white' : '',
                selectionCssClass: isDark ? 'bg-dark text-white' : '',
                matcher: matchFlexible,
            };

            if ($select.is('#id_estudiante')) {
                configuracion.dropdownParent = $('#modal-formulario');
                configuracion.templateResult = formatearEstudiante;
                configuracion.templateSelection = formatearEstudiante;
            }

            if ($select.hasClass('select2')) {
                configuracion.dropdownParent = $('#modal-formulario');
            }

            $select.select2(configuracion);
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
                        if (estudiante.estado == '0') return;

                        let curso = estudiante.curso.curso;
                        let nombre = estudiante.persona.apellidos_nombres;

                        $select.append(
                            `<option value="${estudiante.id_estudiante}" data-curso="${curso}" data-nombre="${nombre}">
                                (${curso}) ${nombre}
                            </option>`
                        );
                    });

                    // Apertura automática del modal desde redirección
                    const urlParams = new URLSearchParams(window.location.search);
                    const idEstudianteUrl = urlParams.get('id_estudiante');

                    if (idEstudianteUrl) {
                        $('.btn-crear').click();
                        $('#form-crear-o-editar select[name="id_estudiante"]').val(idEstudianteUrl)
                            .trigger('change');
                        window.history.replaceState({}, document.title, window.location.pathname);
                    }
                }
            });
        }

        recargarEstudiantesSelect();

        $("#dataTable").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('estudiantes_suspensiones.listar') }}",
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    d.rango_fechas = $('#filter_rango_fechas').val();
                    d.id_nivel = $('#filter_id_nivel').val();
                    d.id_grado = $('#filter_id_grado').val();
                    d.id_curso = $('#filter_id_curso').val();
                    d.id_paralelo = $('#filter_id_paralelo').val();
                    d.estado = $('#filter_estado').val();
                    if (d.rango_fechas === 'personalizado') {
                        d.fecha_inicio = $('#filter_fecha_inicio').val();
                        d.fecha_fin = $('#filter_fecha_fin').val();
                    }
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

                // 1. Contadores
                let kpiTotal = 0;
                let kpiProgramadas = 0;
                let kpiVigentes = 0;
                let kpiFinalizadas = 0;

                let objCursos = {};
                let objMeses = {};
                const hoy = moment();

                // Extracción y agrupación de datos filtrados
                filteredData.forEach(row => {
                    kpiTotal++;

                    // 2. Validación de estados de vigencia
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

                    // Agrupación por mes (usando clave YYYY-MM para mantener orden cronológico)
                    let claveMes = fechaInicio.isValid() ? fechaInicio.format('YYYY-MM') :
                        'Desconocido';
                    objMeses[claveMes] = (objMeses[claveMes] || 0) + 1;
                });

                // 3. Actualizar tarjetas HTML
                $('#kpi-total').text(kpiTotal);
                $('#kpi-programadas').text(kpiProgramadas);
                $('#kpi-vigentes').text(kpiVigentes);
                $('#kpi-finalizadas').text(kpiFinalizadas);

                const sortObjectDesc = (obj) => Object.entries(obj).sort((a, b) => b[1] - a[1]);

                // 4. Renderizar gráfico de cursos
                let sortedCursos = sortObjectDesc(objCursos);
                chartCursos = initOrUpdateChart(chartCursos, 'chart-cursos', {
                    type: 'bar',
                    data: {
                        labels: sortedCursos.map(e => e[0]),
                        datasets: [{
                            data: sortedCursos.map(e => e[1]),
                            backgroundColor: chartPalettes.ui.info,
                            borderRadius: 4
                        }]
                    },
                    options: ChartDefaults.getBarOptions()
                });

                // 5. Renderizar gráfico por mes (ordenado cronológicamente)
                let sortedMeses = Object.entries(objMeses).sort((a, b) => a[0].localeCompare(b[0]));
                let labelsMeses = sortedMeses.map(e => {
                    if (e[0] === 'Desconocido') return e[0];
                    let texto = moment(e[0], 'YYYY-MM').locale('es').format('MMM YYYY');
                    return texto.charAt(0).toUpperCase() + texto.slice(1);
                });

                chartMeses = initOrUpdateChart(chartMeses, 'chart-meses', {
                    type: 'bar',
                    data: {
                        labels: labelsMeses,
                        datasets: [{
                            data: sortedMeses.map(e => e[1]),
                            backgroundColor: chartPalettes.ui.danger,
                            borderRadius: 4
                        }]
                    },
                    options: ChartDefaults.getBarOptions()
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
                    data: null,
                    render: function(data, type, row) {
                        return `<span class="badge bg-primary text-white mb-1">Desde ${moment(row.fecha_inicio).format('DD/MM/YYYY HH:mm')}</span><br>
                                <span class="badge bg-danger text-white">Hasta ${moment(row.fecha_fin).format('DD/MM/YYYY HH:mm')}</span>`;
                    }
                },
                {
                    data: "motivo"
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
                            return `<span class="badge bg-info">PROGRAMADA</span>`;
                        } else if (hoy.isAfter(fecha_fin)) {
                            return `<span class="badge bg-danger">FINALIZADA</span>`;
                        } else {
                            return `<span class="badge bg-success">VIGENTE</span>`;
                        }
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
                                `<button class="btn btn-sm btn-warning btn-editar" data-id="${row.id_estudiante_suspension}">
                                    <i class="fa-solid fa-duotone fa-edit me-1"></i>Editar
                                </button>`;
                        }

                        if (PUEDE_ELIMINAR) {
                            botones +=
                                `<button class="btn btn-sm btn-danger btn-eliminar" data-id="${row.id_estudiante_suspension}">
                                    <i class="fa-solid fa-duotone fa-trash me-1"></i>Eliminar
                                </button>`;
                        }

                        botones += `</div>`;

                        return botones;
                    }
                }
            ],
            ...dtDefaults,
            responsive: false,
            lengthChange: true,
            autoWidth: false,
            scrollX: true,
        }).buttons().container().appendTo('#dataTable-export-buttons-container');

        $("#btn-filtrar").on("click", function(e) {
            e.preventDefault();
            $("#dataTable").DataTable().ajax.reload();
        });

        $(document).on('click', '.btn-crear', function() {
            $('#form-crear-o-editar input[name="id_estudiante_suspension"]').val(0);
            $('#form-crear-o-editar select[name="id_estudiante"]').prop('disabled', false).val('')
                .trigger('change');
            $('#form-crear-o-editar input[name="fecha_inicio"]').val('');
            $('#form-crear-o-editar input[name="fecha_fin"]').val('');
            $('#form-crear-o-editar textarea[name="motivo"]').val('');
            $('#form-crear-o-editar input[name="evidencia"]').val('');

            const titleElement = document.getElementById('modal-formulario-titulo');
            titleElement.innerHTML =
                '<i class="fa-solid fa-duotone fa-plus"></i> REGISTRAR SUSPENSIÓN DE ESTUDIANTE';
            $('#modal-formulario').modal('show');
        });

        $(document).on('click', '.btn-editar', function() {
            const id = $(this).data('id');

            $.get("{{ route('estudiantes_suspensiones.index') . '/' }}" + id, function(
                estudiante_suspension) {
                const data = estudiante_suspension.data;

                $('#form-crear-o-editar input[name="id_estudiante_suspension"]').val(
                    data.id_estudiante_suspension);

                $('#form-crear-o-editar select[name="id_estudiante"]')
                    .val(data.id_estudiante)
                    .trigger('change')
                    .prop('disabled', true);

                $('#form-crear-o-editar input[name="fecha_inicio"]').val(
                    data.fecha_inicio ? moment(data.fecha_inicio).format(
                    'YYYY-MM-DDTHH:mm') : ''
                );
                $('#form-crear-o-editar input[name="fecha_fin"]').val(
                    data.fecha_fin ? moment(data.fecha_fin).format('YYYY-MM-DDTHH:mm') : ''
                );
                $('#form-crear-o-editar textarea[name="motivo"]').val(data.motivo);
                $('#form-crear-o-editar input[name="evidencia"]').val(data.evidencia);

                const titleElement = document.getElementById('modal-formulario-titulo');
                titleElement.innerHTML =
                    '<i class="fa-solid fa-duotone fa-edit"></i> EDITAR SUSPENSIÓN DE ESTUDIANTE';
                $('#modal-formulario').modal('show');
            });
        });

        $('#form-crear-o-editar').submit(function(e) {
            e.preventDefault();

            let id = $('#id_estudiante_suspension').val();
            let url = id == "0" ? "{{ route('estudiantes_suspensiones.create') }}" :
                `{{ url('estudiantes_suspensiones') }}/${id}`;
            let method = id == "0" ? "POST" : "PUT";

            let btn = $('#btn-guardar');
            btn.prop('disabled', true).html(
                '<i class="fa-solid fa-duotone fa-spinner fa-spin"></i> Guardando...');

            let selectEstudiante = $('#form-crear-o-editar select[name="id_estudiante"]');
            let estabaDeshabilitado = selectEstudiante.prop('disabled');
            if (estabaDeshabilitado) selectEstudiante.prop('disabled', false);

            let formData = $(this).serialize();

            if (estabaDeshabilitado) selectEstudiante.prop('disabled', true);

            $.ajax({
                url: url,
                type: method,
                data: formData,
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
                text: "Se eliminará esta suspensión del registro del estudiante.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ route('estudiantes_suspensiones.delete', ':id') }}`
                            .replace(':id', id),
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
                                text: 'No se pudo eliminar la suspensión.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
