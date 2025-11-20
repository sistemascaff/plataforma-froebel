<script>
    $(document).ready(function() {
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
                        return new Date(data).toDateString();
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        prestado_a = row.prestado ? row.prestado.tipo_perfil + ' - ' + row
                            .prestado.apellido_paterno + ' ' + row.prestado.apellido_materno +
                            ' ' + row.prestado.nombres : '-';
                        return prestado_a.trim();
                    }
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
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return `
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-warning btn-sm btn-editar" 
                                data-id="${row.id_libro}" data-toggle="tooltip" title="Editar">
                            <i class="fa-duotone fa-solid fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-${row.estado == 1 ? 'danger' : 'success'} btn-sm btn-cambiar-estado" 
                                data-id="${row.id_libro}" data-estado="${row.estado}" data-nombre="${row.codigo} - ${row.titulo}" 
                                data-toggle="tooltip" title="${row.estado == 1 ? 'Deshabilitar' : 'Habilitar'}">
                            <i class="fa-duotone fa-solid fa-toggle-${row.estado == 1 ? 'off' : 'on'}"></i>
                        </button>
                    </div>
                `;
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
        }).buttons().container().appendTo('#datatable_export_buttons_container');

        $(document).on('click', '.btn-crear', function() {
            $('#formCreateOrEdit input[name="id_libro"]').val(0);
            $('#formCreateOrEdit input[name="titulo"]').val('');
            $('#formCreateOrEdit input[name="codigo"]').val('');
            $('#formCreateOrEdit input[name="autor"]').val('');
            $('#formCreateOrEdit input[name="categoria"]').val('');
            $('#formCreateOrEdit input[name="editorial"]').val('');
            $('#formCreateOrEdit input[name="presentacion"]').val('');
            $('#formCreateOrEdit input[name="anio"]').val('{{ date('Y') }}');
            $('#formCreateOrEdit input[name="costo"]').val('');
            $('#formCreateOrEdit input[name="descripcion"]').val('');
            $('#formCreateOrEdit input[name="adquisicion"]').val(1);
            $('#formCreateOrEdit input[name="fecha_ingreso_cooperativa"]').val('{{ date('Y-m-d') }}');
            $('#formCreateOrEdit input[name="observacion"]').val('');

            const titleElement = document.getElementById('modal_form_title');
            titleElement.innerHTML = '<i class="fa-solid fa-duotone fa-plus"></i> CREAR CLIENTE';
            $('#modal_form').modal('show');
        });



        $(document).on('click', '.btn-editar', function() {
            const id = $(this).data('id');

            $.get("{{ route('libros.index') . '/' }}" + id, function(libro) {
                $('#formCreateOrEdit input[name="id_libro"]').val(libro.data.id_libro);
                $('#titulo').val(libro.data.titulo);
                $('#formCreateOrEdit input[name="codigo"]').val(libro.data.codigo);
                $('#formCreateOrEdit input[name="autor"]').val(libro.data.autor);
                $('#formCreateOrEdit input[name="categoria"]').val(libro.data.categoria);
                $('#formCreateOrEdit input[name="editorial"]').val(libro.data.editorial);
                $('#formCreateOrEdit input[name="presentacion"]').val(libro.data.presentacion);
                $('#formCreateOrEdit input[name="anio"]').val(libro.data.anio);
                $('#formCreateOrEdit input[name="costo"]').val(libro.data.costo);
                $('#descripcion').val(libro.data.descripcion);
                $('#formCreateOrEdit input[name="adquisicion"]').val(libro.data.adquisicion);
                $('#formCreateOrEdit input[name="fecha_ingreso_cooperativa"]').val(new Date(libro.data.fecha_ingreso_cooperativa).toISOString().split('T')[0]);
                $('#observacion').val(libro.data.observacion);

                const titleElement = document.getElementById('modal_form_title');
                titleElement.innerHTML =
                    '<i class="fa-solid fa-duotone fa-edit"></i> EDITAR CLIENTE';
                $('#modal_form').modal('show');
            });
        });


        $(document).on('click', '#btnSave', function() {
            const id_libro = $('#formCreateOrEdit input[name="id_libro"]').val();
            const url = id_libro == 0 ?
                "{{ route('libros.create') }}" // POST -> crear
                :
                "{{ route('libros.index') . '/' }}" + id_libro; // PUT -> actualizar

            const type = id_libro == 0 ? 'POST' : 'PUT';

            $.ajax({
                url: url,
                type: type,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $('#formCreateOrEdit').serialize(),
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Éxito', response.message, 'success');
                        $('#modal_form').modal('hide');
                        $('#dataTable').DataTable().ajax.reload();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    //console.error(xhr.responseText);
                    //console.error(JSON.parse(xhr.responseText));

                    const erroresConcatenados = Object.values(JSON.parse(xhr.responseText)
                            .errors)
                        .flatMap(errores => errores)
                        .join('<br>');

                    Swal.fire('Error', 'Ocurrió un error al intentar la acción: <br>' +
                        erroresConcatenados, 'error');
                }
            });
        });

        $(document).on('click', '.btn-cambiar-estado', function() {
            const id = $(this).data('id');
            const estado_actual = $(this).data('estado');
            const nuevo_estado = estado_actual == 1 ? 0 : 1;
            const nombre = $(this).data('nombre');
            const accion = nuevo_estado == 1 ? 'habilitar' : 'deshabilitar';

            Swal.fire({
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
                            Swal.fire('Actualizado', response.message, 'success');
                            $('#dataTable').DataTable().ajax.reload();
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText);
                            Swal.fire('Error', `No se pudo ${accion} el/la libro`,
                                'error');
                        }
                    });

                }
            });
        });

        function actualizar_estadisticas() {
            // Obtener todos los datos del DataTable
            const dataTable = $("#dataTable").DataTable();
            const all_data = dataTable.rows().data();

            // Calcular totales
            let cantidad_libros_total = all_data.count();
            let cantidad_libros_disponibles = 0;
            let cantidad_libros_prestados = 0;
            let cantidad_libros_eliminados = 0;

            // Iterar sobre los datos para contar según el estado
            all_data.each(function(libro) {
                if (libro.estado == 1) {
                    cantidad_libros_disponibles++;
                } else if (libro.estado == 2) {
                    cantidad_libros_prestados++;
                } else if (libro.estado == 0) {
                    cantidad_libros_eliminados++;
                }
            });

            // Actualizar los elementos HTML
            $('#cantidad_libros_total').text(cantidad_libros_total);
            $('#cantidad_libros_disponibles').text(cantidad_libros_disponibles);
            $('#cantidad_libros_prestados').text(cantidad_libros_prestados);
            $('#cantidad_libros_eliminados').text(cantidad_libros_eliminados);
        }

        $('#dataTable').on('draw.dt', function() {
            actualizar_estadisticas();
        });

    });
</script>
