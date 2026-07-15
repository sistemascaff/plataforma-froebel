<script>
    const URL_BASE = "{{ URL::to('/') }}";

    $(document).ready(function() {

        // ─── Preview de foto de perfil al seleccionar archivo ───────────────────────
        $('#foto_perfil').on('change', function() {
            const file = this.files[0];
            const preview = $('#preview_foto_perfil');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
            } else {
                preview.attr('src', '#').hide();
            }
        });

        // ─── DataTable ───────────────────────────────────────────────────────────────
        $("#dataTable").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('usuarios.listar') }}",
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
                    data: "persona.tipo_perfil"
                },
                {
                    data: "url_foto_perfil",
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        const src = (data && data !== '') ?
                            URL_BASE + '/' + data :
                            URL_BASE + '/public/img/user.png';
                        return `<img class="rounded" src="${src}" alt="Foto" style="width:40px; height:40px; object-fit:cover;">`;
                    }
                },
                {
                    data: "persona.apellido_paterno"
                },
                {
                    data: "persona.apellido_materno"
                },
                {
                    data: "persona.nombres"
                },
                {
                    data: "persona.documento_identificacion"
                },
                {
                    data: "persona.documento_complemento"
                },
                {
                    data: "persona.documento_expedido"
                },
                {
                    data: "persona.fecha_nacimiento",
                    render: function(data) {
                        return data ? new Date(data).toLocaleDateString() : '-';
                    }
                },
                {
                    data: "persona.sexo",
                    render: function(data) {
                        return data == 'M' ? 'MASCULINO' : 'FEMENINO';
                    }
                },
                {
                    data: "persona.idioma"
                },
                {
                    data: "persona.celular"
                },
                {
                    data: "persona.telefono"
                },
                {
                    data: "correo"
                },
                {
                    data: "tiene_acceso",
                    render: function(data) {
                        if (data == 1) return '<span class="badge bg-success">SI</span>';
                        if (data == 0) return '<span class="badge bg-danger">NO</span>';
                        return '<span class="badge bg-warning">DESCONOCIDO</span>';
                    }
                },
                {
                    data: "ultima_conexion",
                    render: function(data) {
                        return data ? new Date(data).toLocaleString() : '-';
                    }
                },
                {
                    data: "ultimo_dispositivo",
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: "ultima_ip",
                    render: function(data) {
                        return data || '-';
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
                        const TIPO_PERFIL = row.persona.tipoPerfil;
                        return `
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-${row.estado == 1 ? 'danger' : 'success'} btn-sm btn-cambiar-estado"
                                        data-id="${row.id_usuario}" data-estado="${row.estado}"
                                        data-nombre="${row.persona.apellido_paterno} ${row.persona.apellido_materno} ${row.persona.nombres}"
                                        data-tipo-perfil="${TIPO_PERFIL}"
                                        data-toggle="tooltip" title="${row.estado == 1 ? 'Archivar' : 'Activar'}">
                                    <i class="fa-duotone fa-solid fa-toggle-${row.estado == 1 ? 'off' : 'on'}"></i>
                                </button>
                            </div>`;
                    }
                }
            ],
            @include('components.datatables.datatables_global_properties')
            @include('components.datatables.datatables_language_property')
        }).buttons().container().appendTo('#dataTable-export-buttons-container');

        // ─── CAMBIAR ESTADO ──────────────────────────────────────────────────────────
        $(document).on('click', '.btn-cambiar-estado', function() {
            const id = $(this).data('id');
            const estadoActual = $(this).data('estado');
            const estadoNuevo = estadoActual == 1 ? 0 : 1;
            const nombre = $(this).data('nombre');
            const tipoPerfil = $(this).data('tipo-perfil');
            const accion = estadoNuevo == 1 ? 'desarchivar' : 'archivar';

            Swal.fire({
                theme: localStorage.getItem('theme') || 'dark',
                title: '¡ATENCIÓN!',
                html: `¿Estás seguro de <b>${accion}</b> al usuario <span class="text-primary fw-bold">${nombre}</span> con tipo de perfil <span class="text-info fw-bold">${tipoPerfil}</span>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Sí, ${accion}`,
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('usuarios.index') . '/' }}" + id,
                        type: "PATCH",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id_usuario: id
                        },
                        success: function(response) {
                            Swal.fire({
                                theme: localStorage.getItem('theme') ||
                                    'dark',
                                title: 'Actualizado',
                                html: response.message ||
                                    'La operación se realizó con éxito.',
                                icon: 'success'
                            });
                            $('#dataTable').DataTable().ajax.reload();
                        },
                        // Se agregan los parámetros jqXHR, textStatus, errorThrown
                        error: function(jqXHR, textStatus, errorThrown) {
                            // Mensaje por defecto si no podemos extraer uno del servidor
                            let mensajeError = `No se pudo ${accion} el usuario.`;

                            // Comprobamos si el servidor devolvió un JSON con un mensaje específico
                            if (jqXHR.responseJSON) {
                                if (jqXHR.responseJSON.errors) {
                                    // Si son errores de validación de Laravel (array de errores)
                                    // Extraemos todos los mensajes y los unimos con saltos de línea
                                    mensajeError = Object.values(jqXHR.responseJSON
                                        .errors).flat().join('<br>');
                                } else if (jqXHR.responseJSON.message) {
                                    // Si es un mensaje de error general (ej. abort(403, 'No autorizado'))
                                    mensajeError = jqXHR.responseJSON.message;
                                }
                            }

                            Swal.fire({
                                theme: localStorage.getItem('theme') ||
                                    'dark',
                                title: 'Error de la operación',
                                html: mensajeError,
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });

    });
</script>
