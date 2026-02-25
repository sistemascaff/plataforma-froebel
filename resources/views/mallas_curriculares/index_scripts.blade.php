<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            language: "es",
            dropdownCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
            selectionCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
            dropdownParent: $('#modal-formulario'),
        });

        $("#dataTable").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('mallas_curriculares.listar') }}", // Ruta de Laravel
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
                    data: "grado.grado",
                },
                {
                    data: "materia.materia",
                },
                {
                    data: "materia.abreviatura",
                },
                {
                    data: "area.area",
                },
                {
                    data: "area.abreviatura",
                },
                {
                    data: "gestion.anio",
                },
                {
                    data: "estado",
                    render: function(data, type, row) {
                        if (data == 1) {
                            return '<span class="badge bg-success">ACTIVO</span>';
                        } else if (data == 0) {
                            return '<span class="badge bg-secondary">ARCHIVADO</span>';
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
                        const url_detalles = "{{ route('mallas_curriculares.detalles', ':id') }}"
                            .replace(':id', row.id_malla_curricular);

                        return `
                            <div class="btn-group" role="group">
                                <a class="btn btn-info btn-sm" href="${url_detalles}" target="_blank" rel="noopener noreferrer"
                                    data-toggle="tooltip" title="Detalles">
                                    <i class="fa-duotone fa-solid fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-warning btn-sm btn-editar" 
                                        data-id="${row.id_malla_curricular}" data-toggle="tooltip" title="Editar">
                                    <i class="fa-duotone fa-solid fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-${row.estado == 1 ? 'danger' : 'success'} btn-sm btn-cambiar-estado" 
                                        data-id="${row.id_malla_curricular}" data-estado="${row.estado}" data-nombre="${row.gestion.gestion}" 
                                        data-toggle="tooltip" title="${row.estado == 1 ? 'Deshabilitar' : 'Habilitar'}">
                                    <i class="fa-duotone fa-solid fa-toggle-${row.estado == 1 ? 'off' : 'on'}"></i>
                                </button>
                            </div>`;
                    }
                }
            ],
            @include('components.datatables.datatables_global_properties')
            @include('components.datatables.datatables_language_property')
        }).buttons().container().appendTo('#dataTable-export-buttons-container');

        $(document).on('click', '.btn-crear', function() {
            $('#form-crear-o-editar input[name="id_malla_curricular"]').val(0);
            //se usa trigger('change') para que los select2 actualicen su valor, de normal no sería necesario.
            $('#form-crear-o-editar select[name="id_grado"]').val('').trigger('change');
            $('#form-crear-o-editar select[name="id_materia"]').val('').trigger('change');
            $('#form-crear-o-editar select[name="id_area"]').val('').trigger('change');
            $('#form-crear-o-editar select[name="id_gestion"]').val('').trigger('change');

            const titleElement = document.getElementById('modal-formulario-titulo');
            titleElement.innerHTML = '<i class="fa-solid fa-duotone fa-plus"></i> CREAR MALLA CURRICULAR';
            $('#modal-formulario').modal('show');
        });



        $(document).on('click', '.btn-editar', function() {
            const id = $(this).data('id');

            $.get("{{ route('mallas_curriculares.index') . '/' }}" + id, function(malla_curricular) {
                $('#form-crear-o-editar input[name="id_malla_curricular"]').val(malla_curricular.data.id_malla_curricular);
                //se usa trigger('change') para que los select2 actualicen su valor, de normal no sería necesario.
                $('#form-crear-o-editar select[name="id_grado"]').val(malla_curricular.data.id_grado).trigger('change');
                $('#form-crear-o-editar select[name="id_materia"]').val(malla_curricular.data.id_materia).trigger('change');
                $('#form-crear-o-editar select[name="id_area"]').val(malla_curricular.data.id_area).trigger('change');
                $('#form-crear-o-editar select[name="id_gestion"]').val(malla_curricular.data.id_gestion).trigger('change');

                const titleElement = document.getElementById('modal-formulario-titulo');
                titleElement.innerHTML =
                    '<i class="fa-solid fa-duotone fa-edit"></i> EDITAR MALLA CURRICULAR';
                $('#modal-formulario').modal('show');
            });
        });


        $(document).on('click', '#btn-guardar', function() {
            const btn = $(this);
            // Deshabilitar el botón para evitar múltiples clics y cambiar el texto
            btn.prop('disabled', true);
            btn.html('<i class="fa-solid fa-duotone fa-spinner fa-spin"></i> Guardando...');

            const id_malla_curricular = $('#form-crear-o-editar input[name="id_malla_curricular"]').val();
            const url = id_malla_curricular == 0 ?
                "{{ route('mallas_curriculares.create') }}" // POST -> crear
                :
                "{{ route('mallas_curriculares.update', ':id') }}"
                .replace(':id', id_malla_curricular); // PUT -> actualizar

            const type = id_malla_curricular == 0 ? 'POST' : 'PUT';

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
                            htmlError,
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
            const accion = estadoNuevo == 1 ? 'desarchivar' : 'archivar';

            Swal.fire({
                theme: localStorage.getItem('theme') || 'dark',
                title: `¡ATENCIÓN!`,
                html: `¿Estás seguro de <b>${accion}</b> la malla curricular <span class="text-primary fw-bold">${nombre}</span>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Sí, ${accion}`,
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('mallas_curriculares.index') . '/' }}" + id,
                        type: "PATCH",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id_malla_curricular: id
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
                                text: `No se pudo ${accion} la malla curricular`,
                                icon: 'error'
                            });
                        }
                    });

                }
            });
        });

    });
</script>
