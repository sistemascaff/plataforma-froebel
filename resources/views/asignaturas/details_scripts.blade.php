<script>
    $(document).ready(function() {

        // ─── Inicializar Select2 y DataTable para otras secciones ─────────────────
        $('#docente').select2({
            width: '100%',
            language: "es",
            dropdownCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
            selectionCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
            dropdownParent: $('#modal-formulario'),
            matcher: matchFlexible,
        });

        $(".dataTable").DataTable({
            ...dtDefaults,
        });

        // ─── Datos del blade ──────────────────────────────────────────────────────
        const idAsignatura = {{ $asignatura->id_asignatura }};
        const idNivel = {{ $asignatura->nivel->id_nivel }};
        const anioActual = {{ $anio_actual }};
        const esAdmin = @json($es_admin);
        const puedeEditarAsignatura = @json($puede_editar_horarios);

        // ─── Estado base (BD) para calcular deltas de guardado ────────────────────
        let horariosBD = [];
        @foreach ($asignatura->horarios_asignaturas as $ha)
            horariosBD.push({
                id_horario_asignatura: {{ $ha->id_horario_asignatura }},
                id_gestion: {{ $ha->id_gestion }},
                dia_semana: {{ $ha->pivot->dia_semana }}
            });
        @endforeach

        let todosLosHorarios = [];
        let gestionActualSeleccionada = $('#filtro-gestion-horarios').val();

        // ─── Funciones y Renderizado de Cuadrícula ────────────────────────────────
        cargarDocentesSelect();
        cargarCuadriculaHorarios();

        function cargarDocentesSelect() {
            $.ajax({
                url: "{{ route('docentes.listar') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    let $select = $("#docente");
                    $select.empty().append('<option value="">Ninguno</option>');
                    $.each(response.data, function(i, docente) {
                        if (docente.estado == 1) {
                            $select.append(
                                `<option value="${docente.id_docente}">${docente.persona.nombres_apellidos}</option>`
                                );
                        }
                    });
                }
            });
        }
        
        function obtenerGestionSeleccionada() {
            const $opt = $('#filtro-gestion-horarios option:selected');
            return {
                id_gestion: $opt.val() ? parseInt($opt.val(), 10) : null,
                anio: $opt.data('anio') ? parseInt($opt.data('anio'), 10) : 0,
                estado: $opt.data('estado') !== undefined ? parseInt($opt.data('estado'), 10) : 0
            };
        }

        function puedeEditarGestionSeleccionada() {
            const {
                id_gestion,
                anio
            } = obtenerGestionSeleccionada();
            if (!id_gestion || !puedeEditarAsignatura) return false;
            return esAdmin || anio >= anioActual;
        }

        function calcularDeltasActuales() {
            let agregar = [];
            let eliminar = [];
            const {
                id_gestion
            } = obtenerGestionSeleccionada();

            $('.check-horario').each(function() {
                let id_horario = $(this).data('horario');
                let dia_semana = $(this).data('dia');
                let isChecked = $(this).is(':checked');

                let wasChecked = horariosBD.some(h => h.id_horario_asignatura == id_horario && h
                    .dia_semana == dia_semana);

                if (isChecked && !wasChecked) {
                    agregar.push({
                        id_horario_asignatura: id_horario,
                        id_gestion: id_gestion,
                        dia_semana: dia_semana
                    });
                } else if (!isChecked && wasChecked) {
                    eliminar.push({
                        id_horario_asignatura: id_horario,
                        id_gestion: id_gestion,
                        dia_semana: dia_semana
                    });
                }
            });

            return {
                agregar,
                eliminar
            };
        }

        function cargarCuadriculaHorarios() {
            $.ajax({
                url: "{{ route('horarios_asignaturas.listar') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    todosLosHorarios = response.data || [];
                    renderizarCuadriculaHorarios();
                },
                error: function() {
                    $('#horarios-grid-body').html(`
                        <tr>
                            <td colspan="7" class="py-4 text-danger">
                                <i class="fa-duotone fa-triangle-exclamation me-2"></i>
                                No se pudo cargar la cuadrícula de horarios.
                            </td>
                        </tr>
                    `);
                }
            });
        }

        function renderizarCuadriculaHorarios() {
            const $tbody = $('#horarios-grid-body');
            $tbody.empty();

            const {
                id_gestion,
                anio
            } = obtenerGestionSeleccionada();
            const puedeEditar = puedeEditarGestionSeleccionada();

            // Mostrar/ocultar controles según permisos de la gestión seleccionada
            $('#badge-solo-lectura-horarios').toggleClass('d-none', puedeEditar);

            if (!id_gestion) {
                $('#btn-guardar-horarios').addClass('d-none');
                $tbody.html(`
                    <tr>
                        <td colspan="7" class="py-4 text-muted">
                            <i class="fa-duotone fa-calendar-xmark me-2"></i>
                            No existen gestiones registradas.
                        </td>
                    </tr>
                `);
                return;
            }

            // Filtrar por nivel y gestión seleccionada:
            // Incluimos activos (estado == 1) y, para no perder historial, cualquier bloque ya asignado a esta asignatura
            let horariosFiltrados = todosLosHorarios.filter(h =>
                h.id_nivel == idNivel &&
                h.id_gestion == id_gestion &&
                (h.estado == 1 || horariosBD.some(hb => hb.id_horario_asignatura == h
                    .id_horario_asignatura))
            );

            // Asegurar orden cronológico
            horariosFiltrados.sort((a, b) => a.hora_inicio.localeCompare(b.hora_inicio));

            if (horariosFiltrados.length === 0) {
                $('#btn-guardar-horarios').addClass('d-none');
                $tbody.html(`
                    <tr>
                        <td colspan="7" class="py-4 text-muted">
                            <i class="fa-duotone fa-calendar-xmark me-2"></i>
                            No existen bloques horarios configurados para este nivel en la gestión <b>${anio}</b>.
                        </td>
                    </tr>
                `);
                return;
            }

            $('#btn-guardar-horarios').toggleClass('d-none', !puedeEditar);

            horariosFiltrados.forEach(function(horario) {
                let desc = horario.denominacion.toLowerCase();
                let esReceso = desc.includes("receso") || desc.includes("recreo");
                let estaArchivado = horario.estado != 1;
                let badgeArchivado = estaArchivado ?
                    ' <span class="badge bg-secondary ms-1">Archivado</span>' :
                    '';

                let filaHTML = `<tr class="${estaArchivado ? 'table-secondary opacity-75' : ''}">
                    <td class="text-start ps-3 fw-bold border-end">
                        ${horario.denominacion}${badgeArchivado}<br>
                        <small class="text-muted fw-normal"><i class="fa-duotone fa-clock me-1"></i>${horario.hora_inicio} - ${horario.hora_fin}</small>
                    </td>`;

                if (esReceso) {
                    filaHTML += `<td colspan="6" class="bg-secondary-subtle text-secondary fw-bold align-middle" style="pointer-events:none;">
                                    <i class="fa-duotone fa-mug-hot me-1"></i>${horario.denominacion.toUpperCase()}
                                 </td>`;
                } else {
                    for (let dia = 1; dia <= 6; dia++) {
                        let isChecked = horariosBD.some(h => h.id_horario_asignatura == horario
                            .id_horario_asignatura && h.dia_semana == dia);
                        let checkedAttr = isChecked ? 'checked' : '';

                        // Un bloque archivado no debe poder marcarse de nuevo (solo desmarcarse si ya estaba marcado y el usuario puede editar)
                        let bloquearCasilla = !puedeEditar || (estaArchivado && !isChecked);
                        let disabledAttr = bloquearCasilla ? 'disabled' : '';
                        let cursorStyle = bloquearCasilla ? 'cursor: not-allowed;' : 'cursor: pointer;';

                        filaHTML += `<td class="align-middle">
                                        <input class="form-check-input check-horario fs-4 shadow-sm" type="checkbox"
                                            data-horario="${horario.id_horario_asignatura}"
                                            data-dia="${dia}"
                                            ${checkedAttr} ${disabledAttr} style="${cursorStyle}">
                                     </td>`;
                    }
                }
                filaHTML += `</tr>`;
                $tbody.append(filaHTML);
            });
        }

        // Cambio de gestión en el selector con protección de cambios sin guardar
        $('#filtro-gestion-horarios').on('change', function() {
            const nuevaGestion = $(this).val();
            const {
                agregar,
                eliminar
            } = calcularDeltasActuales();

            if (agregar.length > 0 || eliminar.length > 0) {
                Swal.fire({
                    theme: localStorage.getItem('theme') || 'light',
                    title: 'Cambios sin guardar',
                    text: 'Tienes modificaciones pendientes en la gestión actual. Si cambias de gestión, se descartarán.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, cambiar y descartar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        gestionActualSeleccionada = nuevaGestion;
                        renderizarCuadriculaHorarios();
                    } else {
                        $('#filtro-gestion-horarios').val(gestionActualSeleccionada);
                    }
                });
            } else {
                gestionActualSeleccionada = nuevaGestion;
                renderizarCuadriculaHorarios();
            }
        });

        // ─── Guardar Cuadrícula (Delta Calculator) ────────────────────────────────
        $('#btn-guardar-horarios').on('click', function() {
            if (!puedeEditarGestionSeleccionada()) {
                Swal.fire({
                    theme: localStorage.getItem('theme') || 'light',
                    icon: 'error',
                    title: 'Acción no permitida',
                    text: 'No tienes permisos para modificar los horarios de esta gestión.',
                });
                return;
            }

            const {
                agregar,
                eliminar
            } = calcularDeltasActuales();

            if (agregar.length === 0 && eliminar.length === 0) {
                Swal.fire({
                    theme: localStorage.getItem('theme') || 'light',
                    icon: 'info',
                    title: '¡Sin cambios!',
                    text: 'No hay modificaciones pendientes para guardar.',
                });
                return;
            }

            const {
                anio
            } = obtenerGestionSeleccionada();
            let resumen = `<span class="badge bg-info text-dark mb-2">Gestión ${anio}</span><br>`;
            if (agregar.length > 0) resumen +=
                `<b class="text-success">+ ${agregar.length} periodo(s) agregado(s)</b><br>`;
            if (eliminar.length > 0) resumen +=
                `<b class="text-danger">− ${eliminar.length} periodo(s) eliminado(s)</b>`;

            Swal.fire({
                theme: localStorage.getItem('theme') || 'light',
                title: 'Confirmación',
                html: `¿Estás seguro de guardar los cambios en la cuadrícula?<br><br>${resumen}`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, guardar',
                cancelButtonText: 'No, cancelar',
            }).then((result) => {
                if (result.isConfirmed) {
                    guardarHorariosAJAX(agregar, eliminar);
                }
            });
        });

        function guardarHorariosAJAX(agregar, eliminar) {
            const btn = $('#btn-guardar-horarios');
            const htmlOriginal = btn.html();
            btn.prop('disabled', true).html(
                '<i class="fa-solid fa-duotone fa-spinner fa-spin"></i> Guardando...');

            $.ajax({
                url: "{{ route('asignaturas.horarios.sync', $asignatura->id_asignatura) }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    agregar: agregar,
                    eliminar: eliminar
                },
                success: function(response) {
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'light',
                        title: '¡Éxito!',
                        text: response.message,
                        icon: 'success',
                    });

                    // Actualizar estado base en memoria para reflejar lo guardado
                    agregar.forEach(a => horariosBD.push(a));
                    eliminar.forEach(e => {
                        horariosBD = horariosBD.filter(h => !(h.id_horario_asignatura == e
                            .id_horario_asignatura && h.dia_semana == e.dia_semana));
                    });

                    btn.html('<i class="fa-solid fa-duotone fa-circle-check"></i> ¡Guardado!');
                    setTimeout(() => {
                        btn.prop('disabled', false).html(htmlOriginal);
                    }, 2000);
                },
                error: function(xhr) {
                    let respuesta = xhr.responseJSON || {
                        message: 'Error desconocido'
                    };
                    let htmlError = respuesta.errors ? Object.values(respuesta.errors).flat().join(
                        '<br>') : (respuesta.message || 'Error inesperado.');

                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'light',
                        title: 'Error',
                        html: htmlError,
                        icon: 'error',
                    });
                    btn.prop('disabled', false).html(htmlOriginal);
                }
            });
        }

        // ─── Acciones de Lista Asignaturas (Modal Docente) ────────────────────────
        $(document).on('click', '.btn-editar-docente', function() {
            const idDocente = $(this).data('id-docente');
            const idLista = $(this).data('id-lista');
            $('#modal-formulario').data('id-lista', idLista);
            $('#docente').val(idDocente).trigger('change');
            $('#modal-formulario').modal('show');
        });

        $(document).on('click', '#btn-guardar-docente', function() {
            const btn = $(this);
            const idLista = $('#modal-formulario').data('id-lista');
            const idDocente = $('#docente').val();

            btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Guardando...');

            const url = "{{ route('listas_asignaturas.actualizar_docente', ':id') }}".replace(':id',
                idLista);

            $.ajax({
                url: url,
                type: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    docente: idDocente
                },
                success: function(response) {
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        title: 'Éxito',
                        text: response.message,
                        icon: 'success'
                    });
                    $('#modal-formulario').modal('hide');

                    const botonEdicion = $(
                        `#listas .btn-editar-docente[data-id-lista="${idLista}"]`);
                    const fila = botonEdicion.closest('tr');
                    const nombreMostrar = response.nuevoDocente?.persona
                        ?.nombres_apellidos ??
                        '<i class="fa-solid fa-duotone fa-exclamation-triangle"></i> No asignado';

                    fila.find('td:eq(3)').html(nombreMostrar);
                    botonEdicion.data('id-docente', idDocente).attr('data-id-docente',
                        idDocente);
                },
                error: function(xhr) {
                    let respuesta = xhr.responseJSON || {
                        message: 'Error desconocido'
                    };
                    let htmlError = respuesta.errors ? Object.values(respuesta.errors)
                        .flat().join('<br>') : (respuesta.message || 'Error inesperado.');
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

    });
</script>
