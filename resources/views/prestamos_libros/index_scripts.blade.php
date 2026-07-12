<script>
    $(document).ready(function() {
        $("#dataTable").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('prestamos_libros.listar') }}", // Ruta de Laravel
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(xhr, error, thrown) {
                    console.error("Error al cargar los datos:", error);
                }
            },
            columns: [{
                    data: "id_prestamo_libro",
                    render: function(data, type, row, meta) {
                        return `<b>${data}</b>`;
                    }
                },
                {
                    data: "persona",
                    render: function(data, type, row) {
                        persona = data.apellido_paterno + ' ' + data.apellido_materno + ' ' +
                            data.nombres;
                        return `<b class="text-info">${persona.trim()}</b>`;
                    }
                },
                {
                    data: "persona.tipo_perfil",
                    render: function(data) {
                        return `<span class="badge bg-secondary bg-opacity-10 text-muted">${data}</span>`;
                    }
                },
                {
                    data: "curso",
                },
                {
                    data: "celular",
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                {
                    data: "libros",
                    render: function(data, type, row) {
                        if (!data || data.length === 0) return "-";
                        if (row.estado == 0) {
                            return data.map((libro, index) =>
                                `<small class="text-muted">${index + 1}.</small> <span class="fw-bold text-danger"><i class="fa-solid fa-circle-xmark me-1"></i>(ANULADO)</span> <code class="text-muted">${libro.codigo}</code> - ${libro.titulo}`
                            ).join("<br>");
                        }
                        return data.map((libro, index) => {
                            let estadoTexto = "";
                            let css = "";
                            if (libro.estado == 2 && libro.pivot.fecha_retorno ===
                                null) {
                                estadoTexto = "(EN USO)";
                                css = "text-primary fw-bold";
                            } else if (libro.pivot.fecha_retorno !== null) {
                                const fecha = new Date(libro.pivot.fecha_retorno)
                                    .toLocaleString();
                                estadoTexto = `(DEVUELTO EL ${fecha})`;
                                css = "text-success opacity-75";
                            } else {
                                estadoTexto = "(DISPONIBLE)";
                                css = "text-muted";
                            }
                            return `<small class="text-muted">${index + 1}.</small> <span class="${css}">${estadoTexto}</span> <code class="text-dark-aquamarine">${libro.codigo}</code> - ${libro.titulo}`;
                        }).join("<br>");
                    }
                },
                {
                    data: "fecha_devolucion",
                    render: function(data, type, row) {
                        const fecha = new Date(data);
                        const anio = fecha.getFullYear();
                        const mes = String(fecha.getMonth() + 1).padStart(2, '0');
                        const dia = String(fecha.getDate()).padStart(2, '0');
                        //return `${anio}-${mes}-${dia}`;
                        return `${dia}/${mes}/${anio}`;
                    }
                },
                {
                    // dias de retraso
                    data: "libros",
                    render: function(data, type, row) {
                        if (!data || data.length === 0) return "-";
                        if (row.estado == 0) return '<span class="text-muted small">N/A</span>';

                        return data.map((libro, index) => {
                            const fechaDevolucion = new Date(row.fecha_devolucion);
                            const fechaRetorno = libro.pivot.fecha_retorno;
                            let diasAtraso = 0;
                            let mensaje = "";
                            let clase = "";

                            if (!fechaRetorno) {
                                const hoy = new Date();
                                const diferenciaMilisegundos = hoy - fechaDevolucion;
                                diasAtraso = Math.floor(diferenciaMilisegundos / (1000 *
                                    60 * 60 * 24));

                                if (diasAtraso > 0) {
                                    mensaje = `${diasAtraso} días retraso`;
                                    clase = "text-danger fw-bold";
                                } else if (diasAtraso === 0) {
                                    mensaje = "Vence hoy";
                                    clase = "text-warning fw-bold";
                                } else {
                                    mensaje = `${Math.abs(diasAtraso)} día(s) rest.`;
                                    clase = "text-primary";
                                }
                            } else {
                                const fechaRetornoDate = new Date(fechaRetorno);
                                const diferenciaMilisegundos = fechaRetornoDate -
                                    fechaDevolucion;
                                diasAtraso = Math.floor(diferenciaMilisegundos / (1000 *
                                    60 * 60 * 24));
                                mensaje = diasAtraso > 0 ?
                                    `${diasAtraso} d. con retraso` : "A tiempo";
                                clase = diasAtraso > 0 ? "text-danger" : "text-success";
                            }
                            return `<small class="text-muted">${index + 1}.</small> <span class="${clase}">${mensaje}</span>`;
                        }).join("<br>");
                    }
                },
                {
                    data: "estado",
                    render: function(data, type, row) {
                        if (data == 1) {
                            return '<span class="badge bg-success">ACTIVO</span>';
                        } else if (data == 0) {
                            return '<span class="badge bg-danger">ANULADO</span>';
                        } else {
                            return '<span class="badge bg-warning">DESCONOCIDO</span>';
                        }
                    }
                },
                {
                    data: "fecha_registro",
                    render: function(data, type, row) {
                        return data ? new Date(data).toLocaleString() : '-';
                    }
                },
                {
                    data: "fecha_actualizacion",
                    render: function(data, type, row) {
                        return data ? new Date(data).toLocaleString() : '-';
                    }
                },
                {
                    data: "fecha_eliminacion",
                    render: function(data, type, row) {
                        return data ? new Date(data).toLocaleString() : '-';
                    }
                },
                {
                    data: "creado.correo",
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                {
                    data: "modificado.correo",
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                {
                    data: "eliminado.correo",
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                {
                    data: "ip",
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                {
                    data: "dispositivo",
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    className: 'text-center',
                    render: function(data, type, row) {
                        const url_detalles = "{{ route('prestamos_libros.detalles', ':id') }}"
                            .replace(':id', row.id_prestamo_libro);
                        const url_editar = "{{ route('prestamos_libros.editar', ':id') }}"
                            .replace(':id', row.id_prestamo_libro);
                        const url_imprimir = "{{ route('prestamos_libros.imprimir', ':id') }}"
                            .replace(':id', row.id_prestamo_libro);
                        return `
                            <div class="btn-group shadow-sm" role="group">
                                <a class="btn btn-outline-info btn-sm" href="${url_detalles}" target="_blank" rel="noopener noreferrer" data-bs-toggle="tooltip" title="Ver Detalles">
                                    <i class="fa-solid fa-duotone fa-eye"></i>
                                </a>
                                <a class="btn btn-outline-warning btn-sm" href="${url_editar}" target="_blank" rel="noopener noreferrer" data-bs-toggle="tooltip" title="Editar">
                                    <i class="fa-solid fa-duotone fa-pen-to-square"></i>
                                </a>
                                <a class="btn btn-outline-primary btn-sm" href="${url_imprimir}" target="_blank" rel="noopener noreferrer" data-bs-toggle="tooltip" title="Imprimir Comprobante">
                                    <i class="fa-solid fa-duotone fa-print"></i>
                                </a>
                            </div>`;
                    }
                }
            ],
            columnDefs: [{
                    targets: [1, 3],
                    width: '200px'
                },
                {
                    targets: [5],
                    width: '350px'
                },
                {
                    targets: [7],
                    width: '150px'
                },
                /* Ocultamos las columnas técnicas (índices 9 al 16) para no saturar la pantalla */
                {
                    targets: [10, 11, 12, 13, 14, 15, 16],
                    visible: false
                }
            ],
            responsive: false,
            lengthChange: true,
            autoWidth: false,
            scrollX: true,
            colReorder: true,
            order: [],
            pageLength: 25,
            dom: 'Blfrtip',
            buttons: [{
                    extend: 'copy',
                    className: 'btn btn-secondary'
                },
                {
                    extend: 'csv',
                    className: 'btn btn-success'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-success'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-danger'
                },
                {
                    extend: 'colvis',
                    className: 'btn btn-info'
                },
                {
                    extend: 'searchBuilder',
                    className: 'btn btn-warning'
                },
            ],
            /* @include('components.datatables.datatables_global_properties') */
            @include('components.datatables.datatables_language_property')
        }).buttons().container().appendTo('#dataTable-export-buttons-container');

        function actualizarEstadisticas() {
            // Obtener referencia al DataTable y sus datos
            const dataTable = $("#dataTable").DataTable();
            const allData = dataTable.rows().data();

            // Total (compatibilizar .count() y .length)
            const cantidadPrestamosTotal = (typeof allData.count === 'function') ? allData.count() : allData
                .length || 0;

            let cantidadPrestamosCompletados = 0;
            let cantidadPrestamosPendientes = 0;
            let cantidadPrestamosAnulados = 0;

            for (let i = 0; i < (allData.length || cantidadPrestamosTotal); i++) {
                const row = allData[i];
                // Si no existe fila, continuar
                if (!row) continue;

                if (row.estado == 0) {
                    cantidadPrestamosAnulados++;
                    continue;
                }

                const libros = row.libros || [];

                // Si no hay libros, consideramos el préstamo como completado
                if (!Array.isArray(libros) || libros.length === 0) {
                    cantidadPrestamosCompletados++;
                    continue;
                }

                // Verificar si todos los libros fueron devueltos
                const todosDevueltos = libros.every(libro => {
                    // Si existe pivot y fecha_retorno no es null -> devuelto
                    return libro && libro.pivot && libro.pivot.fecha_retorno !== null;
                });

                if (todosDevueltos) {
                    cantidadPrestamosCompletados++;
                } else {
                    cantidadPrestamosPendientes++;
                }
            }

            // Actualizar los elementos HTML
            $('#cantidad-prestamos-total').text(cantidadPrestamosTotal);
            $('#cantidad-prestamos-completados').text(cantidadPrestamosCompletados);
            $('#cantidad-prestamos-pendientes').text(cantidadPrestamosPendientes);
            $('#cantidad-prestamos-anulados').text(cantidadPrestamosAnulados);
        }

        $('#dataTable').on('draw.dt', function() {
            actualizarEstadisticas();
        });
    });
</script>
