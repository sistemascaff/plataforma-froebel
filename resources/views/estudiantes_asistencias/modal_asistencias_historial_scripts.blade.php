<script>
    $(document).ready(function() {
        // Inicializamos DataTable para el modal (opcional, pero mantiene el diseño corporativo)
        let tablaHistorial = $('#tabla-historial').DataTable({
            @include('components.datatables.datatables_language_property'),
            pageLength: 10,
            lengthChange: false,
            searching: false,
            ordering: false, // El orden ya viene jerárquico desde el backend
            info: true
        });

        $(document).on('click', '.btn-historial', function() {
            let idEstudiante = $(this).data('id');
            let btn = $(this);
            let iconoOriginal = btn.html();

            // Animación de carga en el botón
            btn.html('<i class="fa-solid fa-spinner fa-spin"></i>').prop('disabled', true);

            // Construir la URL dinámicamente usando la ruta de Laravel
            let url = "{{ route('estudiantes.mostrar_asistencias', ':id') }}".replace(':id',
                idEstudiante);

            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    let datos = response.data;

                    // Actualizar encabezados
                    $('#historial-nombre-estudiante').text(datos.persona.apellidos_nombres);
                    $('#historial-curso-estudiante').text(datos.curso.curso);

                    // Limpiar la tabla
                    tablaHistorial.clear();

                    // Iterar sobre las asistencias y agregar las filas
                    if (datos.estudiantes_asistencias.length > 0) {
                        datos.estudiantes_asistencias.forEach((asis, index) => {
                            let tipo = asis.pivot.tipo;
                            let atraso = asis.pivot.tiempo_atraso;

                            // 1. Formatear Fecha usando Moment.js
                            let fechaEspanol = moment(asis.fecha).format(
                                'DD/MM/YYYY');

                            // 2. Formatear Horario utilizando Moment.js
                            let horaInicio = moment(asis.horario_asignatura
                                .hora_inicio, 'HH:mm:ss').format('HH:mm:ss');

                            // 3. Determinar Badge de Estado
                            let badgeClass = '';
                            let badgeText = '';
                            let iconClass = '';

                            switch (tipo) {
                                case 'PRESENTE':
                                    badgeClass = 'bg-success';
                                    badgeText = 'Presente';
                                    iconClass = 'fa-check-circle';
                                    break;
                                case 'ATRASO':
                                    badgeClass = 'bg-warning text-dark';
                                    badgeText = atraso ? `Atraso (${atraso} min)` :
                                        'Atraso';
                                    iconClass = 'fa-hourglass-half';
                                    break;
                                case 'FALTA':
                                    badgeClass = 'bg-danger';
                                    badgeText = 'Falta';
                                    iconClass = 'fa-times-circle';
                                    break;
                                case 'LICENCIA':
                                    badgeClass = 'bg-info text-dark';
                                    badgeText = 'Licencia';
                                    iconClass = 'fa-file-certificate';
                                    break;
                                case 'PERMISO':
                                    badgeClass = 'bg-primary';
                                    badgeText = 'Permiso';
                                    iconClass = 'fa-file-signature';
                                    break;
                                case 'OTROS':
                                    badgeClass = 'bg-secondary';
                                    badgeText = 'Otros';
                                    iconClass = 'fa-calendar-star';
                                    break;
                                default:
                                    badgeClass = 'bg-secondary';
                                    badgeText = 'Desconocido';
                                    iconClass = 'fa-question-circle';
                            }

                            let badgeHTML = `
                                <span class="badge ${badgeClass} px-3 py-2 shadow-sm w-100">
                                    <i class="fa-solid fa-duotone ${iconClass} me-1"></i> ${badgeText}
                                </span>`;

                            // Agregar fila al DataTable
                            tablaHistorial.row.add([
                                `<div class="text-center">${index + 1}</div>`,
                                `<div class="fw-bold text-center">${fechaEspanol}</div>`,
                                asis.lista_asignatura.asignatura.asignatura,
                                `<div class="text-center text-muted">${horaInicio}</div>`,
                                `<div class="text-center">${badgeHTML}</div>`
                            ]);
                        });
                    }

                    // Dibujar la tabla y mostrar modal
                    tablaHistorial.draw();
                    new bootstrap.Modal(document.getElementById(
                        'modal-historial-asistencias')).show();
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo obtener el historial. Inténtelo nuevamente.',
                        confirmButtonColor: '#0d6efd'
                    });
                },
                complete: function() {
                    // Restaurar botón
                    btn.html(iconoOriginal).prop('disabled', false);
                }
            });
        });
    });
</script>
