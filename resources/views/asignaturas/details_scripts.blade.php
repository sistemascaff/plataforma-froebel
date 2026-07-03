<script>
    $(document).ready(function() {

        // ─── Inicializar Select2 ───────────────────────────────────────────────────
        $('#horario').select2({
            width: '100%',
            language: "es",
            dropdownCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
            selectionCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
        });

        $('#docente').select2({
            width: '100%',
            language: "es",
            dropdownCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
            selectionCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
            dropdownParent: $('#modal-formulario'),
        });

        // ─── DataTable ────────────────────────────────────────────────────────────
        $(".dataTable").DataTable({
            @include('components.datatables.datatables_global_properties')
            @include('components.datatables.datatables_language_property')
        });

        // ─── Datos del blade ──────────────────────────────────────────────────────
        const idAsignatura = {{ $asignatura->id_asignatura }};
        const idNivel = {{ $asignatura->nivel->id_nivel }};

        // ─── Estado en memoria ────────────────────────────────────────────────────
        // Filas que ya están en BD → { id_horario_asignatura, dia_semana, ..., _estado: 'existente' | 'pendiente_borrar' }
        // Filas nuevas (aún no en BD) → { ..., _estado: 'nuevo' }
        let horarios = [];

        // ─── Cargar horarios existentes desde el blade ────────────────────────────
        @foreach ($asignatura->horarios_asignaturas as $ha)
            horarios.push({
                id_horario_asignatura: {{ $ha->id_horario_asignatura }},
                dia_semana: {{ $ha->pivot->dia_semana }},
                dia_nombre: "{{ helper_dia_semana_a_nombre($ha->pivot->dia_semana) }}",
                denominacion: "{{ $ha->denominacion }}",
                hora_inicio: "{{ $ha->hora_inicio }}",
                hora_fin: "{{ $ha->hora_fin }}",
                anio: "{{ $ha->gestion->anio }}",
                _estado: 'existente',
            });
        @endforeach

        // ─── Helpers de días ──────────────────────────────────────────────────────
        const diasNombre = {
            1: 'LUNES',
            2: 'MARTES',
            3: 'MIÉRCOLES',
            4: 'JUEVES',
            5: 'VIERNES',
            6: 'SÁBADO'
        };

        // ─── Renderizar tabla ─────────────────────────────────────────────────────
        function renderizarTabla() {
            let $tbody = $('#horarios tbody');
            $tbody.empty();
            let contador = 1;

            horarios.forEach(function(h, index) {
                let esPendienteBorrar = h._estado === 'pendiente_borrar';
                let esNuevo = h._estado === 'nuevo';

                let clasesFila = '';
                if (esPendienteBorrar) clasesFila =
                    'table-danger text-decoration-line-through opacity-50';
                if (esNuevo) clasesFila = 'table-success';

                let badgeEstado = '';
                if (esPendienteBorrar) badgeEstado =
                    ' <span class="badge bg-danger ms-1">Pendiente borrar</span>';
                if (esNuevo) badgeEstado = ' <span class="badge bg-success ms-1">Nuevo</span>';

                let btnAccion = '';
                if (esPendienteBorrar) {
                    // Botón para deshacer el marcado
                    btnAccion = `
                            <button type="button" class="btn btn-warning btn-sm btn-deshacer-horario"
                                    data-index="${index}" data-toggle="tooltip" title="Deshacer">
                                <i class="fa fa-rotate-left"></i>
                            </button>`;
                } else {
                    btnAccion = `
                            <button type="button" class="btn btn-danger btn-sm btn-remover-horario"
                                    data-index="${index}" data-toggle="tooltip" title="Remover">
                                <i class="fa fa-trash"></i>
                            </button>`;
                }

                $tbody.append(`
                        <tr class="${clasesFila}">
                            <td>${esPendienteBorrar ? '-' : contador++}</td>
                            <td>${h.dia_nombre}${badgeEstado}</td>
                            <td>${h.denominacion}</td>
                            <td>${h.hora_inicio}</td>
                            <td>${h.hora_fin}</td>
                            <td>${h.anio}</td>
                            <td>${btnAccion}</td>
                        </tr>
                    `);
            });
        }

        renderizarTabla();

        // ─── Cargar select de horarios disponibles ────────────────────────────────
        function cargarHorariosSelect() {
            $.ajax({
                url: "{{ route('horarios_asignaturas.listar') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    let $select = $("#horario");
                    $select.empty();
                    $select.append('<option value="">Selecciona un horario</option>');

                    $.each(response.data, function(i, horario) {
                        // Filtrar: solo del nivel de la asignatura, activos, sin receso/recreo
                        if (
                            horario.id_nivel != idNivel ||
                            horario.estado != 1 ||
                            horario.denominacion.toLowerCase().includes("receso") ||
                            horario.denominacion.toLowerCase().includes("recreo")
                        ) return;

                        let fila =
                            `${horario.denominacion} - ${horario.hora_inicio} - ${horario.hora_fin} - ${horario.gestion.anio}`;
                        $select.append(`<option value="${horario.id_horario_asignatura}" 
                                data-denominacion="${horario.denominacion}"
                                data-hora_inicio="${horario.hora_inicio}"
                                data-hora_fin="${horario.hora_fin}"
                                data-anio="${horario.gestion.anio}">
                                ${fila}
                            </option>`);
                    });
                }
            });
        }

        cargarHorariosSelect();

        // ─── Cargar select de docentes disponibles ────────────────────────────────
        function cargarDocentesSelect() {
            $.ajax({
                url: "{{ route('docentes.listar') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    let $select = $("#docente");
                    $select.empty();
                    $select.append('<option value="">Selecciona un docente</option>');

                    $.each(response.data, function(i, docente) {
                        if (docente.estado != 1) return;

                        let fila =
                            `${docente.persona.apellido_paterno} ${docente.persona.apellido_materno} ${docente.persona.nombres}`
                            .trim();
                        $select.append(
                            `<option value="${docente.id_docente}">${fila}</option>`);
                    });
                }
            });
        }

        cargarDocentesSelect();

        // ─── Abrir modal: guardar id_lista_asignatura en el modal ─────────────────
        $(document).on('click', '.btn-editar-docente', function() {
            const idDocente = $(this).data('id-docente'); // docente actual (para preseleccionar)
            const idLista = $(this).data('id-lista'); // <-- necesitamos este dato

            // Guardar el id_lista_asignatura en el modal para usarlo al guardar
            $('#modal-formulario').data('id-lista', idLista);

            $('#docente').val(idDocente).trigger('change');

            $('#modal-formulario').modal('show');
        });

        // ─── Guardar docente en la lista ──────────────────────────────────────────
        $(document).on('click', '#btn-guardar-docente', function() {
            const btn = $(this);
            const idLista = $('#modal-formulario').data('id-lista');
            const idDocente = $('#docente').val();

            if (!idDocente) {
                Swal.fire({
                    theme: localStorage.getItem('theme') || 'dark',
                    title: 'Atención',
                    text: 'Debes seleccionar un docente.',
                    icon: 'warning'
                });
                return;
            }

            btn.prop('disabled', true)
                .html('<i class="fa-solid fa-spinner fa-spin"></i> Guardando...');

            const url = "{{ route('listas_asignaturas.actualizar_docente', ':id') }}"
                .replace(':id', idLista);

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
                    btn.prop('disabled', false)
                        .html('<i class="fa-solid fa-duotone fa-save"></i> Guardar');

                    // Se obtiene el nuevo docente desde la respuesta y se actualiza la tabla
                    const nuevoDocente = response.nuevoDocente;
                    const nombreCompleto =
                        `${nuevoDocente.persona.apellido_paterno} ${nuevoDocente.persona.apellido_materno} ${nuevoDocente.persona.nombres}`
                        .trim();

                    // 1. Encontrar el botón de edición específico de esta fila usando el data-id-lista
                    const botonEdicion = $(
                        `#listas .btn-editar-docente[data-id-lista="${idLista}"]`);

                    // 2. Navegar hacia arriba en el DOM hasta encontrar la fila (tr) correspondiente
                    const fila = botonEdicion.closest('tr');

                    // 3. Actualizar el texto de la celda del docente (es la 4ta columna, índice 3)
                    fila.find('td:eq(3)').text(nombreCompleto);

                    // 4. Mantenimiento crucial: Actualizar el atributo data-id-docente del botón
                    // Esto asegura que si el usuario vuelve a hacer clic en "Editar" sin recargar la página, 
                    // el modal cargue el ID del nuevo docente y no el antiguo.
                    botonEdicion.data('id-docente', idDocente);
                    botonEdicion.attr('data-id-docente', idDocente);
                },
                error: function(xhr) {
                    let respuesta = {};
                    try {
                        respuesta = JSON.parse(xhr.responseText);
                    } catch (e) {
                        respuesta = {
                            message: "Error desconocido"
                        };
                    }

                    let htmlError = respuesta.errors ?
                        Object.values(respuesta.errors).flat().join("<br>") :
                        (respuesta.message || "Ocurrió un error inesperado.");

                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        title: 'Error',
                        html: 'Ocurrió un error: <br>' + htmlError,
                        icon: 'error'
                    });
                },
                complete: function() {
                    btn.prop('disabled', false)
                        .html('<i class="fa-solid fa-duotone fa-save"></i> Guardar');
                }
            });
        });

        // ─── Agregar horario a memoria ─────────────────────────────────────────────
        $('#btn-agregar-horario').on('click', function() {
            const diaSemana = $('#dia_semana').val();
            const idHorario = $('#horario').val();

            if (!diaSemana) {
                Swal.fire({
                    theme: localStorage.getItem('theme') || 'light',
                    icon: 'info',
                    title: '¡Atención!',
                    html: 'Selecciona un <b>día de la semana</b>.',
                });
                return;
            }

            if (!idHorario) {
                Swal.fire({
                    theme: localStorage.getItem('theme') || 'light',
                    icon: 'info',
                    title: '¡Atención!',
                    html: 'Selecciona un <b>horario</b>.',
                });
                return;
            }

            // Verificar duplicado (combinación horario + día) ignorando los pendiente_borrar
            let duplicado = horarios.some(function(h) {
                return h.id_horario_asignatura == idHorario &&
                    h.dia_semana == diaSemana &&
                    h._estado !== 'pendiente_borrar';
            });

            if (duplicado) {
                Swal.fire({
                    theme: localStorage.getItem('theme') || 'light',
                    icon: 'info',
                    title: '',
                    html: '¡Ese horario ya está asignado para ese día!',
                    showConfirmButton: false,
                    timerProgressBar: true,
                    timer: 1800,
                });
                return;
            }

            // Leer datos del option seleccionado
            let $option = $('#horario option:selected');
            let denominacion = $option.data('denominacion');
            let hora_inicio = $option.data('hora_inicio');
            let hora_fin = $option.data('hora_fin');
            let anio = $option.data('anio');
            let dia_nombre = diasNombre[diaSemana];

            horarios.push({
                id_horario_asignatura: idHorario,
                dia_semana: diaSemana,
                dia_nombre: dia_nombre,
                denominacion: denominacion,
                hora_inicio: hora_inicio,
                hora_fin: hora_fin,
                anio: anio,
                _estado: 'nuevo',
            });

            renderizarTabla();
        });

        // ─── Marcar como "pendiente borrar" (filas existentes o nuevas) ────────────
        $(document).on('click', '.btn-remover-horario', function() {
            let index = $(this).data('index');
            if (horarios[index]._estado === 'nuevo') {
                // Las filas nuevas se eliminan directamente del array
                horarios.splice(index, 1);
            } else {
                // Las filas existentes se marcan para borrar al guardar
                horarios[index]._estado = 'pendiente_borrar';
            }
            renderizarTabla();
        });

        // ─── Deshacer el marcado "pendiente borrar" ───────────────────────────────
        $(document).on('click', '.btn-deshacer-horario', function() {
            let index = $(this).data('index');
            horarios[index]._estado = 'existente';
            renderizarTabla();
        });

        // ─── Guardar ──────────────────────────────────────────────────────────────
        $('#btn-guardar-horarios').on('click', function() {
            let agregar = horarios
                .filter(h => h._estado === 'nuevo')
                .map(h => ({
                    id_horario_asignatura: h.id_horario_asignatura,
                    dia_semana: h.dia_semana
                }));

            let eliminar = horarios
                .filter(h => h._estado === 'pendiente_borrar')
                .map(h => ({
                    id_horario_asignatura: h.id_horario_asignatura,
                    dia_semana: h.dia_semana
                }));

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
                `<b class="text-success">+ ${agregar.length} horario(s) a agregar</b><br>`;
            if (eliminar.length > 0) resumen +=
                `<b class="text-danger">− ${eliminar.length} horario(s) a eliminar</b>`;

            Swal.fire({
                theme: localStorage.getItem('theme') || 'light',
                title: 'Confirmación',
                html: `¿Estás seguro de guardar los cambios?<br><br>${resumen}`,
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

        // ─── AJAX guardar ─────────────────────────────────────────────────────────
        function guardarHorariosAJAX(agregar, eliminar) {
            const btn = $('#btn-guardar-horarios');
            btn.prop('disabled', true);
            btn.html('<i class="fa-solid fa-duotone fa-spinner fa-spin"></i> Guardando...');

            $.ajax({
                url: "{{ route('asignaturas.horarios.sync', $asignatura->id_asignatura) }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    agregar: agregar,
                    eliminar: eliminar,
                },
                success: function(response) {
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'light',
                        title: '¡Éxito!',
                        text: response.message,
                        icon: 'success',
                    });

                    btn.html('<i class="fa-solid fa-duotone fa-circle-check"></i> ¡Guardado!');

                    // Actualizar estado en memoria: los "nuevo" pasan a "existente", los "pendiente_borrar" se eliminan
                    horarios = horarios
                        .filter(h => h._estado !== 'pendiente_borrar')
                        .map(h => ({
                            ...h,
                            _estado: 'existente'
                        }));

                    renderizarTabla();

                    setTimeout(() => {
                        btn.prop('disabled', false);
                        btn.html(
                            '<i class="fa-solid fa-duotone fa-floppy-disk"></i> Guardar'
                        );
                    }, 2000);
                },
                error: function(xhr) {
                    let respuesta = {};
                    try {
                        respuesta = JSON.parse(xhr.responseText);
                    } catch (e) {
                        respuesta = {
                            message: 'Error desconocido'
                        };
                    }

                    let htmlError = '';
                    if (respuesta.errors) {
                        htmlError = Object.values(respuesta.errors).flat().join('<br>');
                    } else if (respuesta.message) {
                        htmlError = respuesta.message;
                    } else {
                        htmlError = 'Ocurrió un error inesperado.';
                    }

                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'light',
                        title: 'Error',
                        html: htmlError,
                        icon: 'error',
                    });

                    btn.prop('disabled', false);
                    btn.html('<i class="fa-solid fa-duotone fa-floppy-disk"></i> Guardar');
                }
            });
        }

    });
</script>
