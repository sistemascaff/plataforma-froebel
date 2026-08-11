<script>
    $(document).ready(function() {
        const URL_BASE = "{{ URL::to('/') }}";
        const ID_LISTA_ASIGNATURA = {{ $lista_asignatura->id_lista_asignatura }};
        const TIPO_BLOQUE = "{{ $lista_asignatura->asignatura->tipo_bloque }}";

        // ─── Arreglo dinámico de estudiantes actuales ─────────────────────────
        let estudiantesLista = [
            @foreach ($lista_asignatura->estudiantes as $estudiante)
                {{ $estudiante->id_estudiante }},
            @endforeach
        ];

        // ─── Inicializar DataTable ──────────────────────────────────────────────
        let dtEstudiantes = $("#estudiantes").DataTable({
            @include('components.datatables.datatables_global_properties')
            @include('components.datatables.datatables_language_property'),
            columnDefs: [{
                    orderable: false,
                    targets: [1, 5]
                }, // Deshabilita orden en Foto y Acciones
                {
                    searchable: false,
                    targets: [0, 1, 5]
                }
            ]
        });

        // ─── Inicializar DataTable de asistencias ────────────────────────────────
        $("#asistencias").DataTable({
            @include('components.datatables.datatables_global_properties')
            @include('components.datatables.datatables_language_property')
        });

        // Auto-enumerar la primera columna (#)
        dtEstudiantes.on('order.dt search.dt draw.dt', function() {
            dtEstudiantes.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        }).draw();

        // ─── Modal Automático ──────────────────────────────────────────────────
        @if (!empty($cambios) && count($cambios) > 0)
            const modalCambios = new bootstrap.Modal(document.getElementById('modalCambios'));
            modalCambios.show();
        @endif

        // ─── Utilidad: Toast para notificaciones rápidas ────────────────────────────
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            theme: localStorage.getItem('theme') || 'dark'
        });

        // ─── Funciones para copiar al portapapeles ──────────────────────────────────
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

        // ─── Lógica Exclusiva para Bloques Mixtos ──────────────────────────────
        @if ($lista_asignatura->asignatura->tipo_bloque === 'mixto')

            let modoEdicion = false;

            // 1. Activar / Desactivar Modo Edición
            $('#btn-toggle-edicion').click(function() {
                modoEdicion = !modoEdicion;

                if (modoEdicion) {
                    // Activar
                    $(this).html('<i class="fa-solid fa-duotone fa-times me-1"></i> Cancelar Edición')
                        .removeClass('btn-warning').addClass('btn-secondary');
                    $('#panel-edicion').slideDown();
                    $('.btn-remover').prop('disabled', false); // Habilitar basureros
                    $('#btn-guardar-estudiantes').prop('disabled', false);
                } else {
                    // Cancelar: Recargar página para restaurar tabla original (rollback visual)
                    location.reload();
                }
            });

            // 2. Select2 de Estudiantes
            $('#estudiante').select2({
                width: '100%',
                language: "es",
                dropdownCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
                selectionCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
            });

            function recargarEstudiantesSelect() {
                $.ajax({
                    url: "{{ route('estudiantes.listar') }}", // Asegurate que esta ruta exista
                    type: "GET",
                    dataType: "json",
                    success: function(response) {
                        let $select = $("#estudiante");
                        $select.empty().append(
                            '<option value="">-- Seleccione un estudiante --</option>');

                        $.each(response.data, function(i, est) {
                            if (est.estado == '0') return; // Omitir inactivos

                            let nombre = est.persona.apellidos_nombres;
                            let correo = est.persona.usuario.correo;
                            let curso = est.curso ? est.curso.curso : 'Sin Curso';
                            let foto = est.persona.usuario.url_foto_perfil ? URL_BASE +
                                '/' + est.persona.usuario.url_foto_perfil : URL_BASE +
                                '/public/img/user.png';

                            $select.append(`
                                    <option value="${est.id_estudiante}" 
                                            data-nombre="${nombre}" 
                                            data-correo="${correo}" 
                                            data-curso="${curso}" 
                                            data-foto="${foto}">
                                        ${nombre} - ${correo} - ${curso}
                                    </option>
                                `);
                        });
                    }
                });
            }
            recargarEstudiantesSelect();

            // 3. Añadir a la tabla (DOM + Array)
            $('#btn-agregar-estudiante').click(function() {
                let idSelect = $('#estudiante').val();

                if (!idSelect) {
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        icon: 'warning',
                        title: 'Atención',
                        text: 'Debes seleccionar un estudiante de la lista.'
                    });
                    return;
                }

                let idEstudiante = parseInt(idSelect);

                if (estudiantesLista.includes(idEstudiante)) {
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        icon: 'info',
                        title: 'Aviso',
                        text: 'El estudiante ya se encuentra en la lista.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    return;
                }

                // Obtener datos del option seleccionado
                let $op = $('#estudiante option:selected');
                let imgHtml =
                    `<img class="rounded shadow-sm" src="${$op.data('foto')}" alt="Foto" style="width:35px; height:35px; object-fit:cover;">`;
                let nombreFormat = `<span class="fw-bold">${$op.data('nombre')}</span>`;
                let btnRemover =
                    `<button type="button" class="btn btn-danger btn-sm btn-remover" data-id="${idEstudiante}" data-bs-toggle="tooltip" title="Remover de la lista"><i class="fa-duotone fa-trash"></i></button>`;

                // Agregar al array maestro
                estudiantesLista.push(idEstudiante);

                // Agregar a DataTables
                let nodoFila = dtEstudiantes.row.add([
                    '', // El número se autocalcula en el draw
                    imgHtml,
                    nombreFormat,
                    $op.data('correo'),
                    $op.data('curso'),
                    btnRemover
                ]).draw(false).node();

                $(nodoFila).attr('id', 'fila-' + idEstudiante).addClass(
                    'table-success'); // Efecto visual

                // Resetear Select2
                $('#estudiante').val(null).trigger('change');

                // Reinicializar tooltips (Bootstrap 5)
                $('[data-bs-toggle="tooltip"]').tooltip();
            });

            // 4. Remover de la tabla (DOM + Array)
            $('#estudiantes tbody').on('click', '.btn-remover', function() {
                let idRemover = parseInt($(this).data('id'));

                // Sacar del array
                estudiantesLista = estudiantesLista.filter(id => id !== idRemover);

                // Sacar del DataTable
                dtEstudiantes.row($(this).parents('tr')).remove().draw(false);
            });

            // 5. Guardar Cambios (AJAX BULK UPDATE)
            $('#btn-guardar-estudiantes').click(function() {
                const btn = $(this);

                Swal.fire({
                    theme: localStorage.getItem('theme') || 'dark',
                    title: 'Confirmar guardado',
                    text: `Se actualizará la lista con un total de ${estudiantesLista.length} estudiantes. ¿Deseas continuar?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa-solid fa-check"></i> Sí, guardar',
                    cancelButtonText: 'Cancelar'
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
                                estudiantes: estudiantesLista // Se envía el array limpio
                            },
                            success: function(response) {
                                Swal.fire({
                                    theme: localStorage.getItem('theme') ||
                                        'dark',
                                    title: '¡Éxito!',
                                    text: response.message,
                                    icon: 'success'
                                }).then(() => {
                                    location
                                        .reload(); // Recargar para volver al estado de solo lectura y limpiar CSS
                                });
                            },
                            error: function(xhr) {
                                let respuesta = xhr.responseJSON || {
                                    message: "Error inesperado"
                                };
                                let errorHtml = respuesta.errors ? Object.values(
                                        respuesta.errors).flat().join('<br>') :
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
        @endif
    });
</script>
