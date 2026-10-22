<script>
    $(document).ready(function() {
        const URL_BASE = "{{ URL::to('/') }}";
        const ID_LISTA_ASIGNATURA = {{ $lista_asignatura->id_lista_asignatura }};
        const ES_MIXTO = "{{ $lista_asignatura->asignatura->tipo_bloque }}" === 'mixto';

        // ─── Estado Inicial y Actual de Estudiantes ──────────────────────────────
        const estudiantesOriginal = [
            @foreach ($lista_asignatura->estudiantes as $estudiante)
                {{ $estudiante->id_estudiante }},
            @endforeach
        ];
        let estudiantesLista = [...estudiantesOriginal];

        // Guardamos copia de las filas originales para poder "Deshacer" sin recargar
        const htmlFilasOriginales = $('#estudiantes tbody').html();

        // ─── Configuración de Columnas según Tipo de Bloque ──────────────────────
        const colIdxNum = ES_MIXTO ? 1 : 0;
        const colNoOrdenables = ES_MIXTO ? [0, 2, 6] : [1, 5];
        const colNoBuscables = ES_MIXTO ? [0, 1, 2, 6] : [0, 1, 5];

        // ─── Inicializar DataTable de Estudiantes ────────────────────────────────
        let dtEstudiantes = $("#estudiantes").DataTable({
            ...dtDefaults,
            pageLength: -1,
            columnDefs: [{
                    orderable: false,
                    targets: colNoOrdenables
                },
                {
                    searchable: false,
                    targets: colNoBuscables
                }
            ]
        });

        // Auto-enumerar la columna (#) dinámicamente
        dtEstudiantes.on('order.dt search.dt draw.dt', function() {
            dtEstudiantes.column(colIdxNum, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();

        // ─── Inicializar DataTable de Asistencias ────────────────────────────────
        let dtAsistencias = $("#asistencias").DataTable({
            ...dtDefaults,
        });

        // Ajustar columnas de DataTables al cambiar de pestaña (evita desalineación de headers)
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
            $.fn.dataTable.tables({
                visible: true,
                api: true
            }).columns.adjust();
        });

        // ─── Modal Automático de Sincronización ──────────────────────────────────
        @if (!empty($cambios) && count($cambios) > 0)
            const modalCambios = new bootstrap.Modal(document.getElementById('modalCambios'));
            modalCambios.show();
        @endif

        // ─── Utilidad: Toast para notificaciones rápidas ─────────────────────────
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            theme: localStorage.getItem('theme') || 'dark'
        });

        // ─── Copiar Nombre y Correo al Portapapeles ──────────────────────────────
        $('#estudiantes tbody').on('click', '.btn-copiar-nombre', function() {
            let texto = $(this).data('nombre');
            navigator.clipboard.writeText(texto).then(() => {
                Toast.fire({
                    icon: 'success',
                    title: 'Nombre copiado'
                });
            });
        });

        $('#estudiantes tbody').on('click', '.btn-copiar-correo', function() {
            let texto = $(this).data('correo');
            navigator.clipboard.writeText(texto).then(() => {
                Toast.fire({
                    icon: 'success',
                    title: 'Correo copiado'
                });
            });
        });

        // =========================================================================
        // LÓGICA EXCLUSIVA PARA BLOQUES MIXTOS
        // =========================================================================
        @if ($lista_asignatura->asignatura->tipo_bloque === 'mixto')

            let catalogoEstudiantes = {};

            // 1. Inicializar Select2 Múltiple
            $('#estudiante').select2({
                width: '100%',
                language: "es",
                placeholder: "Buscar y seleccionar uno o varios estudiantes...",
                allowClear: true,
                closeOnSelect: false, // Permite elegir varios sin que se cierre el desplegable
                dropdownCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
                selectionCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
            });

            // 2. Cargar catálogo de estudiantes y sincronizar opciones disponibles
            function cargarCatalogoEstudiantes() {
                $.ajax({
                    url: "{{ route('estudiantes.listar.asignaturas') }}",
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        catalogoEstudiantes = {};
                        $.each(response.data, function(i, est) {
                            if (est.estado == '0') return;
                            catalogoEstudiantes[est.id_estudiante] = {
                                id: est.id_estudiante,
                                nombre: est.persona.apellidos_nombres,
                                correo: est.persona.usuario.correo,
                                curso: est.curso ? est.curso.curso : 'Sin Curso',
                                foto: est.persona.usuario.url_foto_perfil ?
                                    `${URL_BASE}/${est.persona.usuario.url_foto_perfil}` :
                                    `${URL_BASE}/public/img/user.png`
                            };
                        });
                        sincronizarOpcionesSelect();
                    }
                });
            }

            // Deshabilita en el Select2 los estudiantes que ya están en la tabla
            function sincronizarOpcionesSelect() {
                let $select = $("#estudiante");
                let seleccionadosActuales = $select.val() || [];
                $select.empty();

                Object.values(catalogoEstudiantes).forEach(est => {
                    let yaEnLista = estudiantesLista.includes(est.id);
                    let texto =
                        `${est.nombre} (${est.curso}) - ${est.correo}${yaEnLista ? ' [Ya en lista]' : ''}`;
                    let option = new Option(texto, est.id, false, false);
                    if (yaEnLista) {
                        $(option).prop('disabled', true);
                    }
                    $select.append(option);
                });

                // Mantener seleccionados solo los que aún no están en la lista
                let seleccionadosValidos = seleccionadosActuales.filter(id => !estudiantesLista.includes(
                    parseInt(id)));
                $select.val(seleccionadosValidos).trigger('change');
            }

            cargarCatalogoEstudiantes();

            // 3. Evaluar si hay diferencias respecto a la lista original (Dirty Check)
            function evaluarEstadoCambios() {
                $('#badge-total-estudiantes').text(estudiantesLista.length);

                let agregados = estudiantesLista.filter(id => !estudiantesOriginal.includes(id));
                let eliminados = estudiantesOriginal.filter(id => !estudiantesLista.includes(id));
                let hayCambios = agregados.length > 0 || eliminados.length > 0;

                const $barra = $('#barra-cambios-pendientes');
                if (hayCambios) {
                    let detalles = [];
                    if (agregados.length > 0) detalles.push(
                        `<span class="badge bg-success">+${agregados.length} agregado(s)</span>`);
                    if (eliminados.length > 0) detalles.push(
                        `<span class="badge bg-danger">-${eliminados.length} quitado(s)</span>`);
                    $('#resumen-cambios').html(detalles.join(' '));
                    $barra.removeClass('d-none').addClass('d-flex');
                } else {
                    $barra.removeClass('d-flex').addClass('d-none');
                }

                sincronizarOpcionesSelect();
                actualizarBotonSeleccionMasiva();
            }

            // 4. Agregar uno o varios estudiantes desde el Select2 Múltiple
            $('#btn-agregar-estudiante').click(function() {
                let seleccionados = $('#estudiante').val(); // Retorna array de IDs en Select2 multiple

                if (!seleccionados || seleccionados.length === 0) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Selecciona al menos un estudiante'
                    });
                    return;
                }

                let agregadosCount = 0;

                seleccionados.forEach(idStr => {
                    let idEstudiante = parseInt(idStr);
                    if (estudiantesLista.includes(idEstudiante)) return;

                    let est = catalogoEstudiantes[idEstudiante];
                    if (!est) return;

                    estudiantesLista.push(idEstudiante);
                    agregadosCount++;

                    let checkHtml =
                        `<input class="form-check-input check-estudiante" type="checkbox" value="${est.id}">`;
                    let imgHtml =
                        `<img class="rounded shadow-sm zoomable-image" src="${est.foto}" alt="Foto" style="width:35px; height:35px; object-fit:cover;">`;
                    let accionesHtml = `
                        <div class="d-flex gap-1 justify-content-center align-items-center">
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-copiar-nombre" data-nombre="${est.nombre}" data-bs-toggle="tooltip" title="Copiar nombre completo">
                                <i class="fa-duotone fa-user-tag"></i>
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm btn-copiar-correo" data-correo="${est.correo}" data-bs-toggle="tooltip" title="Copiar correo electrónico">
                                <i class="fa-duotone fa-envelope"></i>
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm btn-remover" data-id="${est.id}" data-bs-toggle="tooltip" title="Quitar de la lista">
                                <i class="fa-duotone fa-trash"></i>
                            </button>
                        </div>
                    `;

                    let nodoFila = dtEstudiantes.row.add([
                        checkHtml,
                        '',
                        imgHtml,
                        est.nombre,
                        est.correo,
                        est.curso,
                        accionesHtml
                    ]).draw(false).node();

                    $(nodoFila)
                        .attr('id', 'fila-' + est.id)
                        .attr('data-id', est.id)
                        .addClass('table-success');

                    $('td:eq(0), td:eq(1), td:eq(2), td:eq(6)', nodoFila).addClass(
                        'text-center align-middle');
                    $('td:eq(3)', nodoFila).addClass('align-middle fw-bold');
                    $('td:eq(4), td:eq(5)', nodoFila).addClass('align-middle');
                });

                $('#estudiante').val(null).trigger('change');
                evaluarEstadoCambios();

                if (agregadosCount > 0) {
                    Toast.fire({
                        icon: 'success',
                        title: `${agregadosCount} estudiante(s) añadido(s) a la tabla`
                    });
                }
            });

            // 5. Quitar estudiante individualmente
            $('#estudiantes tbody').on('click', '.btn-remover', function() {
                let $btn = $(this);
                $btn.tooltip('hide');
                let idRemover = parseInt($btn.data('id'));

                estudiantesLista = estudiantesLista.filter(id => id !== idRemover);
                dtEstudiantes.row($btn.closest('tr')).remove().draw(false);

                evaluarEstadoCambios();
            });

            // 6. Selección Masiva de Estudiantes para Quitar
            function actualizarBotonSeleccionMasiva() {
                let totalMarcados = dtEstudiantes.$('.check-estudiante:checked').length;
                let totalFilas = dtEstudiantes.$('.check-estudiante').length;

                $('#count-seleccionados').text(totalMarcados);
                if (totalMarcados > 0) {
                    $('#btn-remover-seleccionados').removeClass('d-none');
                } else {
                    $('#btn-remover-seleccionados').addClass('d-none');
                }

                $('#check-todos-estudiantes').prop('checked', totalFilas > 0 && totalMarcados === totalFilas);
            }

            $('#check-todos-estudiantes').on('change', function() {
                let isChecked = $(this).is(':checked');
                dtEstudiantes.$('.check-estudiante').prop('checked', isChecked);
                actualizarBotonSeleccionMasiva();
            });

            $('#estudiantes tbody').on('change', '.check-estudiante', function() {
                actualizarBotonSeleccionMasiva();
            });

            $('#btn-remover-seleccionados').click(function() {
                let $marcados = dtEstudiantes.$('.check-estudiante:checked');
                let cantidad = $marcados.length;
                if (cantidad === 0) return;

                $marcados.each(function() {
                    let idRemover = parseInt($(this).val());
                    estudiantesLista = estudiantesLista.filter(id => id !== idRemover);
                    dtEstudiantes.row($(this).closest('tr')).remove();
                });

                dtEstudiantes.draw(false);
                $('#check-todos-estudiantes').prop('checked', false);
                evaluarEstadoCambios();

                Toast.fire({
                    icon: 'info',
                    title: `${cantidad} estudiante(s) quitado(s) de la tabla`
                });
            });

            // 7. Deshacer Cambios sin recargar la página
            $('#btn-deshacer-cambios').click(function() {
                estudiantesLista = [...estudiantesOriginal];
                dtEstudiantes.clear();
                $('#estudiantes tbody').html(htmlFilasOriginales);

                // Re-incorporar filas originales a DataTables
                dtEstudiantes.rows.add($('#estudiantes tbody tr')).draw();
                $('#check-todos-estudiantes').prop('checked', false);
                evaluarEstadoCambios();

                Toast.fire({
                    icon: 'info',
                    title: 'Cambios descartados'
                });
            });

            // 8. Guardar Cambios (AJAX PUT)
            $('#btn-guardar-estudiantes').click(function() {
                const btn = $(this);

                Swal.fire({
                    theme: localStorage.getItem('theme') || 'dark',
                    title: 'Confirmar guardado',
                    html: `Se actualizará la lista mixta con un total de <b>${estudiantesLista.length} estudiantes</b>.<br>¿Deseas guardar los cambios?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa-solid fa-check me-1"></i> Sí, guardar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true).html(
                            '<i class="fa-solid fa-spinner fa-spin me-1"></i> Guardando...');

                        $.ajax({
                            url: "{{ route('listas_asignaturas.update', $lista_asignatura->id_lista_asignatura) }}",
                            type: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                    'content')
                            },
                            data: {
                                id_lista_asignatura: ID_LISTA_ASIGNATURA,
                                estudiantes: estudiantesLista
                            },
                            success: function(response) {
                                Swal.fire({
                                    theme: localStorage.getItem('theme') ||
                                        'dark',
                                    title: '¡Éxito!',
                                    html: response.message,
                                    icon: 'success'
                                }).then(() => {
                                    location.reload();
                                });
                            },
                            error: function(xhr) {
                                let respuesta = xhr.responseJSON || {
                                    message: "Error inesperado"
                                };
                                let errorHtml = respuesta.errors ?
                                    Object.values(respuesta.errors).flat().join(
                                        '<br>') :
                                    respuesta.message;

                                Swal.fire({
                                    theme: localStorage.getItem('theme') ||
                                        'dark',
                                    icon: 'error',
                                    title: 'Error',
                                    html: errorHtml
                                });
                                btn.prop('disabled', false).html(
                                    '<i class="fa-solid fa-duotone fa-floppy-disk me-1"></i> Guardar Cambios'
                                    );
                            }
                        });
                    }
                });
            });

            // ─── Lógica para Importación Masiva por Cursos ────────────────────────
            let dtCursos = $("#tabla-cursos-importar").DataTable({
                ...dtDefaults,
                paging: false,
                columnDefs: [{
                        orderable: false,
                        targets: [0]
                    },
                    {
                        searchable: false,
                        targets: [0]
                    }
                ]
            });

            $('#check-todos-cursos').on('change', function() {
                let isChecked = $(this).is(':checked');
                dtCursos.$('.check-curso').prop('checked', isChecked);
            });

            $('#form-importar-cursos').on('submit', function(e) {
                e.preventDefault();

                let cursosSeleccionados = [];
                dtCursos.$('.check-curso:checked').each(function() {
                    cursosSeleccionados.push($(this).val());
                });

                if (cursosSeleccionados.length === 0) {
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        icon: 'warning',
                        title: 'Atención',
                        text: 'Debes seleccionar al menos un curso de la tabla.'
                    });
                    return;
                }

                const n = cursosSeleccionados.length;

                Swal.fire({
                    theme: localStorage.getItem('theme') || 'dark',
                    icon: 'question',
                    title: 'Confirmar operación',
                    html: `Has seleccionado <b>${n} ${n === 1 ? 'curso' : 'cursos'}</b>, ¿estás seguro de continuar con la operación?`,
                    showCancelButton: true,
                    confirmButtonText: 'Sí, continuar',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        enviarImportacion(cursosSeleccionados);
                    }
                });
            });

            function enviarImportacion(cursosSeleccionados) {
                const btn = $('#btn-submit-importar-cursos');
                btn.prop('disabled', true).html(
                    '<i class="fa-solid fa-spinner fa-spin me-1"></i> Incorporando...');

                $.ajax({
                    url: "{{ route('listas_asignaturas.importar_cursos', $lista_asignatura->id_lista_asignatura) }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        cursos: cursosSeleccionados
                    },
                    success: function(response) {
                        $('#modalImportarCursos').modal('hide');
                        Swal.fire({
                            theme: localStorage.getItem('theme') || 'dark',
                            title: '¡Operación Exitosa!',
                            html: response.message,
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html(
                            '<i class="fa-solid fa-duotone fa-check me-1"></i> Incorporar a la Lista'
                            );
                        let respuesta = xhr.responseJSON || {
                            message: "Error inesperado"
                        };
                        let errorHtml = respuesta.errors ?
                            Object.values(respuesta.errors).flat().join('<br>') :
                            respuesta.message;

                        Swal.fire({
                            theme: localStorage.getItem('theme') || 'dark',
                            icon: 'error',
                            title: 'Error',
                            html: errorHtml
                        });
                    }
                });
            }
        @endif
    });
</script>
