<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%',
            language: "es",
            dropdownCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
            selectionCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
            dropdownParent: $('#modal-formulario'),
        });

        function recargarEstudiantesSelect() {
            $.ajax({
                url: "{{ route('estudiantes.listar') }}",
                type: "GET",
                dataType: "json",
                success: function(response) {
                    let $select = $("#id_estudiante");
                    $select.empty();
                    $select.append('<option value="">-- Seleccione a un estudiante --</option>');

                    $.each(response.data, function(i, estudiante) {
                        // Omitir estudiantes inactivos
                        if (estudiante.estado == '0') return;

                        let nombre_completo = [
                                estudiante.persona.apellido_paterno,
                                estudiante.persona.apellido_materno,
                                estudiante.persona.nombres
                            ]
                            .filter(Boolean)
                            .join(' ');

                        let fila = `(${estudiante.curso.curso}) ${nombre_completo}`;

                        $select.append(
                            `<option value="${estudiante.id_estudiante}">${fila}</option>`
                        );
                    });
                }
            });
        }

        recargarEstudiantesSelect();

        $("#dataTable").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('estudiantes_licencias.listar') }}",
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
                        return meta.row + 1;
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `${row.estudiante.persona.apellido_paterno} ${row.estudiante.persona.apellido_materno} ${row.estudiante.persona.nombres}`;
                    }
                },
                {
                    data: "tipo",
                    render: function(data) {
                        // Formateamos el enum (ej: "motivos_familiares" -> "Motivos Familiares")
                        let texto = data.replace(/_/g, ' ');
                        return texto.charAt(0).toUpperCase() + texto.slice(1);
                    }
                },
                {
                    data: null,
                    render: function(data, type, row) {
                        return `<span class="badge bg-primary text-white mb-1">Desde ${row.fecha_inicio}</span><br>
                                <span class="badge bg-danger text-white">Hasta ${row.fecha_fin}</span>`;
                    }
                },
                {
                    data: "justificacion"
                },
                {
                    data: "evidencia",
                    render: function(data) {
                        if (data) {
                            return `<a href="${data}" target="_blank" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Abrir archivo en Google Drive">
                                        <i class="fa-brands fa-google-drive"></i> Ver
                                    </a>`;
                        }
                        return '<span class="text-muted fst-italic">Sin evidencia</span>';
                    }
                },
                {
                    data: "estado",
                    render: function(data) {
                        if (data == 1) return '<span class="badge bg-success">ACTIVO</span>';
                        if (data == 0)
                            return '<span class="badge bg-secondary">ARCHIVADO</span>';
                        return '<span class="badge bg-warning">DESCONOCIDO</span>';
                    }
                },
                {
                    data: "fecha_registro",
                    render: function(data) {
                        return data ? new Date(data).toLocaleString() : '-';
                    }
                },
                {
                    data: "fecha_actualizacion",
                    render: function(data) {
                        return data ? new Date(data).toLocaleString() : '-';
                    }
                },
                {
                    data: "fecha_eliminacion",
                    render: function(data) {
                        return data ? new Date(data).toLocaleString() : '-';
                    }
                },
                {
                    data: "creado.correo",
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: "modificado.correo",
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: "eliminado.correo",
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: "ip",
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: "dispositivo",
                    render: function(data) {
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
                                <button class="btn btn-sm btn-warning btn-editar">
                                    <i class="fa-solid fa-duotone fa-pen-to-square"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-eliminar" data-id="${row.id_estudiante_licencia}">
                                    <i class="fa-solid fa-duotone fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ],
            @include('components.datatables.datatables_global_properties')
            @include('components.datatables.datatables_language_property')
        }).buttons().container().appendTo('#dataTable-export-buttons-container');

        $(document).on('click', '.btn-crear', function() {
            $('#form-crear-o-editar input[name="id_estudiante_licencia"]').val(0);
            $('#form-crear-o-editar select[name="id_estudiante"]').val('').trigger('change');
            $('#form-crear-o-editar select[name="tipo"]').val('').trigger('change');
            $('#form-crear-o-editar input[name="fecha_inicio"]').val('');
            $('#form-crear-o-editar input[name="fecha_fin"]').val('');
            $('#form-crear-o-editar textarea[name="justificacion"]').val('');
            $('#form-crear-o-editar input[name="evidencia"]').val('');

            const titleElement = document.getElementById('modal-formulario-titulo');
            titleElement.innerHTML =
                '<i class="fa-solid fa-duotone fa-plus"></i> CREAR LICENCIA DE ESTUDIANTE';
            $('#modal-formulario').modal('show');
        });

        $(document).on('click', '.btn-editar', function() {
            const id = $(this).data('id');

            $.get("{{ route('estudiantes_licencias.index') . '/' }}" + id, function(
                estudiante_licencia) {
                $('#form-crear-o-editar input[name="id_estudiante_licencia"]').val(
                    estudiante_licencia.data.id_estudiante_licencia);
                $('#form-crear-o-editar select[name="id_estudiante"]').val(estudiante_licencia
                    .data.id_estudiante).trigger('change');
                $('#form-crear-o-editar select[name="tipo"]').val(estudiante_licencia
                    .data.tipo).trigger('change');
                $('#form-crear-o-editar input[name="fecha_inicio"]').val(estudiante_licencia
                    .data.fecha_inicio);
                $('#form-crear-o-editar input[name="fecha_fin"]').val(estudiante_licencia
                    .data.fecha_fin);
                $('#form-crear-o-editar textarea[name="justificacion"]').val(estudiante_licencia
                    .data.justificacion);
                $('#form-crear-o-editar input[name="evidencia"]').val(estudiante_licencia
                    .data.evidencia);

                const titleElement = document.getElementById('modal-formulario-titulo');
                titleElement.innerHTML =
                    '<i class="fa-solid fa-duotone fa-edit"></i> EDITAR LICENCIA DE ESTUDIANTE';
                $('#modal-formulario').modal('show');
            });
        });

        $('#form-crear-o-editar').submit(function(e) {
            e.preventDefault();

            let id = $('#id_estudiante_licencia').val();
            let url = id == "0" ? "{{ route('estudiantes_licencias.create') }}" :
                `{{ url('estudiantes_licencias') }}/${id}`;
            let method = id == "0" ? "POST" : "PUT";

            let btn = $('#btn-guardar');
            btn.prop('disabled', true).html(
                '<i class="fa-solid fa-duotone fa-spinner fa-spin"></i> Guardando...');

            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#modal-formulario').modal('hide');
                    $('#dataTable').DataTable().ajax.reload();
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        title: '¡Éxito!',
                        text: response.message,
                        icon: 'success'
                    });
                },
                error: function(xhr) {
                    let htmlError = "Ocurrió un error inesperado.";
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        htmlError = Object.values(xhr.responseJSON.errors).flat().join(
                            "<br>");
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        htmlError = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        title: 'Error',
                        html: htmlError,
                        icon: 'error'
                    });
                },
                complete: function() {
                    btn.prop('disabled', false).html(
                        '<i class="fa-solid fa-duotone fa-save"></i> Guardar');
                }
            });
        });

        $(document).on('click', '.btn-eliminar', function() {
            const id = $(this).data('id');
            Swal.fire({
                theme: localStorage.getItem('theme') || 'dark',
                title: '¿Estás seguro?',
                text: "Se eliminará esta licencia del registro del estudiante.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('estudiantes_licencias') }}/${id}`,
                        type: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id_estudiante_licencia: id
                        },
                        success: function(response) {
                            $('#dataTable').DataTable().ajax.reload();
                            Swal.fire({
                                theme: localStorage.getItem('theme') ||
                                    'dark',
                                title: 'Eliminado',
                                text: response.message,
                                icon: 'success'
                            });
                        },
                        error: function(xhr) {
                            Swal.fire({
                                theme: localStorage.getItem('theme') ||
                                    'dark',
                                title: 'Error',
                                text: 'No se pudo eliminar la licencia.',
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });

    });
</script>
