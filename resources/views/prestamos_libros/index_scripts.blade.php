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
                    render: function(data, type, row) {
                        return `<b class="text-info">${data}</b>`;
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
                        if (!data || data.length === 0) {
                            return "-";
                        }

                        if (row.estado == 0) {
                            return data.map((libro, index) =>
                                `<b class="text-info">${index + 1}.</b> 
                                    <span class="fw-bold text-danger">(ANULADO)</span>  
                                    ${libro.codigo} - ${libro.titulo}`
                            ).join("<br>");
                        }

                        // SI EL PRÉSTAMO ESTÁ ACTIVO O CERRADO
                        return data.map((libro, index) => {
                            let estadoTexto = "";
                            let css = "";

                            // Libro actualmente prestado
                            if (libro.estado == 2 && libro.pivot.fecha_retorno ===
                                null) {
                                estadoTexto = "(EN USO)";
                                css = "text-primary";
                            }
                            // Libro devuelto
                            else if (libro.pivot.fecha_retorno !== null) {
                                const fecha = new Date(libro.pivot.fecha_retorno)
                                    .toLocaleString();
                                estadoTexto = "(DEVUELTO EL " + fecha + ")";
                                css = "text-success";
                            }
                            // Situación rara pero posible (estado disponible sin fecha de retorno)
                            else {
                                estadoTexto = "(DISPONIBLE)";
                                css = "text-secondary";
                            }

                            return `<b class="text-info">${index + 1}.</b> 
                    <span class="fw-bold ${css}">${estadoTexto}</span>  
                    ${libro.codigo} - ${libro.titulo}`;
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
                        if (!data || data.length === 0) {
                            return "-";
                        }

                        if (row.estado == 0) {
                            return 'N/A';
                        }

                        return data.map((libro, index) => {
                            // Obtener las fechas
                            const fechaDevolucion = new Date(row.fecha_devolucion);
                            const fechaRetorno = libro.pivot.fecha_retorno;

                            let diasAtraso = 0;
                            let mensaje = "";
                            let clase = "";

                            if (!fechaRetorno) {
                                // Libro NO devuelto - calcular desde hoy
                                const hoy = new Date();
                                const diferenciaMilisegundos = hoy - fechaDevolucion;
                                diasAtraso = Math.floor(diferenciaMilisegundos / (1000 *
                                    60 * 60 * 24));

                                if (diasAtraso > 0) {
                                    mensaje = `${diasAtraso} y contando...`;
                                    clase = "text-danger";
                                } else if (diasAtraso === 0) {
                                    mensaje = "Vence hoy";
                                    clase = "text-warning";
                                } else {
                                    mensaje =
                                        `${Math.abs(diasAtraso)} día(s) restantes`;
                                    clase = "text-primary";
                                }
                            } else {
                                // Libro YA devuelto - calcular diferencia fija
                                const fechaRetornoDate = new Date(fechaRetorno);
                                const diferenciaMilisegundos = fechaRetornoDate -
                                    fechaDevolucion;
                                diasAtraso = Math.floor(diferenciaMilisegundos / (1000 *
                                    60 * 60 * 24));

                                if (diasAtraso > 0) {
                                    mensaje = `${diasAtraso}`;
                                    clase = "text-danger";
                                } else {
                                    mensaje = "A tiempo";
                                    clase = "text-success";
                                }
                            }

                            return `<b class="text-info">${index + 1}.</b> <b class="${clase}">${mensaje}</b>`;
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
                    render: function(data, type, row) {
                        const url_detalles = "{{ route('prestamos_libros.detalles', ':id') }}"
                            .replace(':id', row.id_prestamo_libro);
                        const url_editar = "{{ route('prestamos_libros.editar', ':id') }}"
                            .replace(':id', row.id_prestamo_libro);
                        const url_imprimir = "{{ route('prestamos_libros.imprimir', ':id') }}"
                            .replace(':id', row.id_prestamo_libro);
                        return `
                            <div class="btn-group" role="group">
                                <a class="btn btn-info btn-sm" href="${url_detalles}" target="_blank" rel="noopener noreferrer"
                                    data-toggle="tooltip" title="Editar">
                                    <i class="fa-duotone fa-solid fa-eye"></i>
                                </a>
                                <a class="btn btn-warning btn-sm" href="${url_editar}" target="_blank" rel="noopener noreferrer"
                                    data-toggle="tooltip" title="Editar">
                                    <i class="fa-duotone fa-solid fa-edit"></i>
                                </a>
                                <a class="btn btn-primary btn-sm" href="${url_imprimir}" target="_blank" rel="noopener noreferrer"
                                    data-toggle="tooltip" title="Imprimir">
                                    <i class="fa-duotone fa-solid fa-print"></i>
                                </a>
                            </div>`;
                    }
                }
            ],
            columnDefs: [{
                    targets: [1, 3],
                    width: '350px',
                },
                {
                    targets: [5],
                    width: '600px',
                },
                {
                    targets: [7],
                    width: '185px',
                },
            ],
            responsive: false,
            lengthChange: true,
            autoWidth: false,
            scrollX: true,
            colReorder: true,
            order: [],
            pageLength: 10,
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
            const cantidadPrestamosTotal = (typeof allData.count === 'function') ? allData.count() : allData.length || 0;

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
