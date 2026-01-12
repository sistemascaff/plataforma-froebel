<script>
    $(document).ready(function() {
        var libro_insert_codigo = '{{ $libro_insert_codigo }}';
        $("#dataTable").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('libros.listar') }}", // Ruta de Laravel
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                error: function(xhr, error, thrown) {
                    console.error("Error al cargar los datos:", error);
                }
            },
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return meta.row + 1; // número de iteración
                    }
                },
                {
                    data: "categoria",
                },
                {
                    data: "codigo",
                    render: function(data, type, rowData, row) {
                        if (rowData.estado == 1) {
                            return `<span class="text-success fw-bold">${data}</span>`;
                        } else if (rowData.estado == 0) {
                            return `<span class="text-secondary fw-bold">${data}</span>`;
                        } else if (rowData.estado == 2) {
                            return `<span class="text-primary fw-bold">${data}</span>`;
                        } else {
                            return `<span class="text-warning fw-bold">${data}</span>`;
                        }
                    },
                    createdCell: function(td, cellData, rowData, row, col) {
                        if (rowData.estado == 1) {
                            $(td).addClass('table-success border border-success'); // Disponible
                        } else if (rowData.estado == 0) {
                            $(td).addClass(
                                'table-secondary border border-secondary'); // Eliminado
                        } else if (rowData.estado == 2) {
                            $(td).addClass('table-primary border border-primary'); // En uso
                        }
                    }
                },
                {
                    data: "titulo",
                },
                {
                    data: "autor",
                },
                {
                    data: "editorial",
                },
                {
                    data: "anio",
                },
                {
                    data: "descripcion",
                    render: function(data, type, row) {
                        return data || '-';
                    }
                },
                {
                    data: "costo",
                },
                {
                    data: "adquisicion",
                    render: function(data, type, row) {
                        adquisicion = data == 1 ? 'COMPRA' : data == 2 ? 'DONACIÓN' : 'OTRO';
                        return adquisicion;
                    }
                },
                {
                    data: "presentacion",
                },
                {
                    data: "observacion",
                },
                {
                    data: "fecha_ingreso_cooperativa",
                    render: function(data, type, row) {
                        const fecha = new Date(data);
                        const anio = fecha.getFullYear();
                        const mes = String(fecha.getMonth() + 1).padStart(2, '0');
                        const dia = String(fecha.getDate()).padStart(2, '0');
                        return `${anio}-${mes}-${dia}`;
                        //return `${dia}/${mes}/${anio}`;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        const cursoRaw = row.prestado?.estudiante?.curso?.curso || '';

                        const abreviar = (window.Helpers && typeof window.Helpers
                                .abreviarCurso === 'function') ?
                            window.Helpers.abreviarCurso(cursoRaw) :
                            cursoRaw;

                        const curso = abreviar ? ` (${abreviar})` : '';

                        const prestado_a = row.prestado ?
                            `${row.prestado.tipo_perfil} ${curso} - ${row.prestado.apellido_paterno} ${row.prestado.apellido_materno} ${row.prestado.nombres}` :
                            '-';

                        return `<b class="text-info">${prestado_a.trim()}</b>`;
                    }
                },
                {
                    data: "prestamos_libros_count",
                },
                {
                    data: "estado",
                    render: function(data, type, row) {
                        if (data == 1) {
                            return '<span class="badge bg-success">DISPONIBLE</span>';
                        } else if (data == 0) {
                            return '<span class="badge bg-secondary">ELIMINADO</span>';
                        } else if (data == 2) {
                            return '<span class="badge bg-primary">EN USO</span>';
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
                        const url_detalles = "{{ route('libros.detalles', ':id') }}"
                            .replace(':id', row.id_libro);

                        if (row.estado != 2) {
                            return `
                            <div class="btn-group" role="group">
                                <a class="btn btn-info btn-sm" href="${url_detalles}" target="_blank" rel="noopener noreferrer"
                                    data-toggle="tooltip" title="Editar">
                                    <i class="fa-duotone fa-solid fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-warning btn-sm btn-editar" 
                                        data-id="${row.id_libro}" data-toggle="tooltip" title="Editar">
                                    <i class="fa-duotone fa-solid fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-${row.estado == 1 ? 'danger' : 'success'} btn-sm btn-cambiar-estado" 
                                        data-id="${row.id_libro}" data-estado="${row.estado}" data-nombre="${row.codigo} - ${row.titulo}" 
                                        data-toggle="tooltip" title="${row.estado == 1 ? 'Deshabilitar' : 'Habilitar'}">
                                    <i class="fa-duotone fa-solid fa-toggle-${row.estado == 1 ? 'off' : 'on'}"></i>
                                </button>
                            </div>`;
                        } else {
                            return `
                            <div class="btn-group" role="group">
                                <a class="btn btn-info btn-sm" href="${url_detalles}" target="_blank" rel="noopener noreferrer"
                                    data-toggle="tooltip" title="Editar">
                                    <i class="fa-duotone fa-solid fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-warning btn-sm btn-editar" 
                                        data-id="${row.id_libro}" data-toggle="tooltip" title="Editar">
                                    <i class="fa-duotone fa-solid fa-edit"></i>
                                </button>
                            </div>
                        `;
                        }
                    }
                }
            ],
            columnDefs: [{
                targets: [3, 4, 5, 13],
                width: '500px',
            }, ],
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

        $(document).on('click', '.btn-crear', function() {
            $('#form-crear-o-editar input[name="id_libro"]').val(0);
            $('#form-crear-o-editar input[name="titulo"]').val('');
            $('#form-crear-o-editar input[name="codigo"]').val(libro_insert_codigo);
            $('#form-crear-o-editar input[name="autor"]').val('');
            $('#form-crear-o-editar input[name="categoria"]').val('');
            $('#form-crear-o-editar input[name="editorial"]').val('');
            $('#form-crear-o-editar input[name="presentacion"]').val('');
            $('#form-crear-o-editar input[name="anio"]').val('{{ date('Y') }}');
            $('#form-crear-o-editar input[name="costo"]').val('');
            $('#descripcion').val('');
            $('#form-crear-o-editar input[name="adquisicion"]').val(1);
            $('#form-crear-o-editar input[name="fecha_ingreso_cooperativa"]').val('{{ date('Y-m-d') }}');
            $('#observacion').val('');

            const titleElement = document.getElementById('modal-formulario-titulo');
            titleElement.innerHTML = '<i class="fa-solid fa-duotone fa-plus"></i> CREAR LIBRO';
            $('#modal-formulario').modal('show');
        });



        $(document).on('click', '.btn-editar', function() {
            const id = $(this).data('id');

            $.get("{{ route('libros.index') . '/' }}" + id, function(libro) {
                $('#form-crear-o-editar input[name="id_libro"]').val(libro.data.id_libro);
                $('#form-crear-o-editar input[name="titulo"]').val(libro.data.titulo);
                $('#form-crear-o-editar input[name="codigo"]').val(libro.data.codigo);
                $('#form-crear-o-editar input[name="autor"]').val(libro.data.autor);
                $('#form-crear-o-editar input[name="categoria"]').val(libro.data.categoria);
                $('#form-crear-o-editar input[name="editorial"]').val(libro.data.editorial);
                $('#form-crear-o-editar input[name="presentacion"]').val(libro.data.presentacion);
                $('#form-crear-o-editar input[name="anio"]').val(libro.data.anio);
                $('#form-crear-o-editar input[name="costo"]').val(libro.data.costo);
                $('#descripcion').val(libro.data.descripcion);
                $('#form-crear-o-editar input[name="adquisicion"]').val(libro.data.adquisicion);
                $('#form-crear-o-editar input[name="fecha_ingreso_cooperativa"]').val(new Date(
                    libro.data.fecha_ingreso_cooperativa).toISOString().split('T')[0]);
                $('#observacion').val(libro.data.observacion);

                const titleElement = document.getElementById('modal-formulario-titulo');
                titleElement.innerHTML =
                    '<i class="fa-solid fa-duotone fa-edit"></i> EDITAR LIBRO';
                $('#modal-formulario').modal('show');
            });
        });


        $(document).on('click', '#btn-guardar', function() {
            const btn = $(this);
            // Deshabilitar el botón para evitar múltiples clics y cambiar el texto
            btn.prop('disabled', true);
            btn.html('<i class="fa-solid fa-duotone fa-spinner fa-spin"></i> Guardando...');

            const id_libro = $('#form-crear-o-editar input[name="id_libro"]').val();
            const url = id_libro == 0 ?
                "{{ route('libros.create') }}" // POST -> crear
                :
                "{{ route('libros.update', ':id') }}"
                .replace(':id', id_libro); // PUT -> actualizar

            const type = id_libro == 0 ? 'POST' : 'PUT';

            $.ajax({
                url: url,
                type: type,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $('#form-crear-o-editar').serialize(),
                success: function(response) {
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        title: 'Éxito',
                        text: response.message,
                        icon: 'success'
                    });
                    $('#modal-formulario').modal('hide');
                    $('#dataTable').DataTable().ajax.reload();
                    if (type === 'POST') {
                        // Incrementar el código para el próximo libro
                        libro_insert_codigo++;
                    }
                    btn.prop('disabled', false);
                    btn.html('<i class="fa-solid fa-duotone fa-save"></i> Guardar');
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

                    let htmlError = "";

                    if (respuesta.errors) {
                        // Errores de validación (422)
                        htmlError = Object.values(respuesta.errors)
                            .flat()
                            .join("<br>");
                    } else if (respuesta.message) {
                        // Errores manuales (400, 403, 500...)
                        htmlError = respuesta.message;
                    } else {
                        htmlError = "Ocurrió un error inesperado.";
                    }
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        title: 'Error',
                        html: 'Ocurrió un error al intentar la acción: <br>' +
                            erroresConcatenados,
                        icon: 'error'
                    });
                    btn.prop('disabled', false);
                    btn.html('<i class="fa-solid fa-duotone fa-save"></i> Guardar');
                }
            });
        });

        $(document).on('click', '.btn-cambiar-estado', function() {
            const id = $(this).data('id');
            const estadoActual = $(this).data('estado');
            const estadoNuevo = estadoActual == 1 ? 0 : 1;
            const nombre = $(this).data('nombre');
            const accion = estadoNuevo == 1 ? 'habilitar' : 'deshabilitar';

            Swal.fire({
                theme: localStorage.getItem('theme') || 'dark',
                title: `¡ATENCIÓN!`,
                html: `¿Estás seguro de <b>${accion}</b> el libro <span class="text-primary fw-bold">${nombre}</span>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Sí, ${accion}`,
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('libros.index') . '/' }}" + id,
                        type: "PATCH",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id_libro: id
                        },
                        success: function(response) {
                            Swal.fire({
                                theme: localStorage.getItem('theme') ||
                                    'dark',
                                title: 'Actualizado',
                                text: response.message,
                                icon: 'success'
                            });
                            $('#dataTable').DataTable().ajax.reload();
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

                            let htmlError = "";

                            if (respuesta.errors) {
                                // Errores de validación (422)
                                htmlError = Object.values(respuesta.errors)
                                    .flat()
                                    .join("<br>");
                            } else if (respuesta.message) {
                                // Errores manuales (400, 403, 500...)
                                htmlError = respuesta.message;
                            } else {
                                htmlError = "Ocurrió un error inesperado.";
                            }
                            Swal.fire({
                                theme: localStorage.getItem('theme') ||
                                    'dark',
                                title: 'Error',
                                text: `No se pudo ${accion} el/la libro`,
                                icon: 'error'
                            });
                        }
                    });

                }
            });
        });

        function actualizar_estadisticas() {
            // Obtener todos los datos del DataTable
            const dataTable = $("#dataTable").DataTable();
            const allData = dataTable.rows().data();

            // Calcular totales
            let cantidadLibrosTotal = allData.count();
            let cantidadLibrosDisponibles = 0;
            let cantidadLibrosPrestados = 0;
            let cantidadLibrosEliminados = 0;

            // Iterar sobre los datos para contar según el estado
            allData.each(function(libro) {
                if (libro.estado == 1) {
                    cantidadLibrosDisponibles++;
                } else if (libro.estado == 2) {
                    cantidadLibrosPrestados++;
                } else if (libro.estado == 0) {
                    cantidadLibrosEliminados++;
                }
            });

            // Actualizar los elementos HTML
            $('#cantidad-libros-total').text(cantidadLibrosTotal);
            $('#cantidad-libros-disponibles').text(cantidadLibrosDisponibles);
            $('#cantidad-libros-prestados').text(cantidadLibrosPrestados);
            $('#cantidad-libros-eliminados').text(cantidadLibrosEliminados);
        }

        $('#dataTable').on('draw.dt', function() {
            actualizar_estadisticas();
        });

    });
</script>
