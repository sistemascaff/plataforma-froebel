<script>
    $(document).ready(function() {

        // ─── Inicializar Select2 y DataTable para otras secciones ─────────────────
        $('#docente').select2({
            width: '100%',
            language: "es",
            dropdownCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
            selectionCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
            dropdownParent: $('#modal-formulario'),
        });

        $(".dataTable").DataTable({
            @include('components.datatables.datatables_global_properties')
            @include('components.datatables.datatables_language_property')
        });

        // ─── Datos del blade ──────────────────────────────────────────────────────
        const idAsignatura = {{ $asignatura->id_asignatura }};
        const idNivel = {{ $asignatura->nivel->id_nivel }};

        // ─── Estado base (BD) para calcular deltas de guardado ────────────────────
        let horariosBD = [];
        @foreach ($asignatura->horarios_asignaturas as $ha)
            horariosBD.push({
                id_horario_asignatura: {{ $ha->id_horario_asignatura }},
                dia_semana: {{ $ha->pivot->dia_semana }}
            });
        @endforeach

        // ─── Funciones y Renderizado de Cuadrícula ────────────────────────────────
        cargarDocentesSelect();
        cargarCuadriculaHorarios();

        function cargarCuadriculaHorarios() {
            $.ajax({
                url: "{{ route('horarios_asignaturas.listar') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    let $tbody = $('#horarios-grid-body');
                    $tbody.empty();

                    // Filtrar activos y del nivel correspondiente
                    let horariosNivel = response.data.filter(h => h.id_nivel == idNivel && h
                        .estado == 1);

                    // Asegurar orden cronológico
                    horariosNivel.sort((a, b) => a.hora_inicio.localeCompare(b.hora_inicio));

                    horariosNivel.forEach(function(horario) {
                        let desc = horario.denominacion.toLowerCase();
                        let esReceso = desc.includes("receso") || desc.includes("recreo");

                        let filaHTML = `<tr>
                            <td class="text-start ps-3 fw-bold border-end">
                                ${horario.denominacion}<br>
                                <small class="text-muted fw-normal"><i class="fa-duotone fa-clock me-1"></i>${horario.hora_inicio} - ${horario.hora_fin}</small>
                            </td>`;

                        if (esReceso) {
                            // Fila completa bloqueada para el receso
                            filaHTML += `<td colspan="6" class="bg-secondary-subtle text-secondary fw-bold align-middle" style="pointer-events:none;">
                                            <i class="fa-duotone fa-mug-hot me-1"></i> ${horario.denominacion.toUpperCase()}
                                         </td>`;
                        } else {
                            // Columnas de días interactivos (1=Lunes a 6=Sábado)
                            for (let dia = 1; dia <= 6; dia++) {
                                let isChecked = horariosBD.some(h => h
                                    .id_horario_asignatura == horario
                                    .id_horario_asignatura && h.dia_semana == dia);
                                let checkedAttr = isChecked ? 'checked' : '';

                                filaHTML += `<td class="align-middle">
                                                <input class="form-check-input check-horario fs-4 shadow-sm" type="checkbox" 
                                                    data-horario="${horario.id_horario_asignatura}" 
                                                    data-dia="${dia}" 
                                                    ${checkedAttr} style="cursor: pointer;">
                                             </td>`;
                            }
                        }
                        filaHTML += `</tr>`;
                        $tbody.append(filaHTML);
                    });
                }
            });
        }

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

        // ─── Guardar Cuadrícula (Delta Calculator) ────────────────────────────────
        $('#btn-guardar-horarios').on('click', function() {
            let agregar = [];
            let eliminar = [];

            // Analizamos todas las casillas renderizadas
            $('.check-horario').each(function() {
                let id_horario = $(this).data('horario');
                let dia_semana = $(this).data('dia');
                let isChecked = $(this).is(':checked');

                let wasChecked = horariosBD.some(h => h.id_horario_asignatura == id_horario && h
                    .dia_semana == dia_semana);

                if (isChecked && !wasChecked) {
                    agregar.push({
                        id_horario_asignatura: id_horario,
                        dia_semana: dia_semana
                    });
                } else if (!isChecked && wasChecked) {
                    eliminar.push({
                        id_horario_asignatura: id_horario,
                        dia_semana: dia_semana
                    });
                }
            });

            if (agregar.length === 0 && eliminar.length === 0) {
                Swal.fire({
                    theme: localStorage.getItem('theme') || 'light',
                    icon: 'info',
                    title: '¡Sin cambios!',
                    text: 'No hay modificaciones pendientes para guardar.',
                });
                return;
            }

            let resumen = '';
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
