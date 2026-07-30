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
                url: "{{ route('docentes.listar') }}",
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
                    data: "persona.usuario.url_foto_perfil",
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
                    data: "persona.tipo_perfil"
                },
                {
                    data: "persona.usuario.correo"
                },
                {
                    data: "persona.usuario.tiene_acceso",
                    render: function(data) {
                        if (data == 1) return '<span class="badge bg-success">SI</span>';
                        if (data == 0) return '<span class="badge bg-danger">NO</span>';
                        return '<span class="badge bg-warning">DESCONOCIDO</span>';
                    }
                },
                {
                    data: "nivel.nivel",
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: "coordinacion.coordinacion",
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: "especialidad"
                },
                {
                    data: "grado_estudios"
                },
                {
                    data: "domicilio"
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
                        const url_detalles = "{{ route('docentes.detalles', ':id') }}"
                            .replace(':id', row.id_docente);
                        return `
                            <div class="btn-group" role="group">
                                <a class="btn btn-info btn-sm" href="${url_detalles}" target="_blank" rel="noopener noreferrer"
                                    data-toggle="tooltip" title="Detalles">
                                    <i class="fa-duotone fa-solid fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-warning btn-sm btn-editar"
                                        data-id="${row.id_docente}" data-toggle="tooltip" title="Editar">
                                    <i class="fa-duotone fa-solid fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-${row.estado == 1 ? 'danger' : 'success'} btn-sm btn-cambiar-estado"
                                        data-id="${row.id_docente}" data-estado="${row.estado}"
                                        data-nombre="${row.persona.nombres_apellidos}"
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


        // ─── CREAR ───────────────────────────────────────────────────────────────────
        $(document).on('click', '.btn-crear', function() {
            const form = $('#form-crear-o-editar');

            form.find('input[name="id_docente"]').val(0);

            // Datos personales
            form.find('input[name="apellido_paterno"]').val('');
            form.find('input[name="apellido_materno"]').val('');
            form.find('input[name="nombres"]').val('');
            form.find('input[name="documento_identificacion"]').val('');
            form.find('input[name="documento_complemento"]').val('');
            form.find('select[name="documento_expedido"]').val('');
            form.find('input[name="fecha_nacimiento"]').val('');
            form.find('select[name="sexo"]').val('');
            form.find('select[name="idioma"]').val('');
            form.find('input[name="celular"]').val('');
            form.find('input[name="telefono"]').val('');

            // Datos de acceso
            $('#preview_foto_perfil').attr('src', '#').hide();
            form.find('input[name="foto_perfil"]').val('');
            form.find('input[name="correo"]').val('');
            form.find('input[name="contrasenha"]').val('').attr('required', true);
            form.find('input[name="confirmar_contrasenha"]').val('').attr('required', true);
            $('#label-contrasenha-requerida, #label-confirmar-requerida').show();

            // Datos del docente
            form.find('select[name="id_nivel"]').val('');
            form.find('select[name="id_coordinacion"]').val('');
            form.find('input[name="especialidad"]').val('');
            form.find('input[name="grado_estudios"]').val('');
            form.find('input[name="domicilio"]').val('');

            document.getElementById('modal-formulario-titulo').innerHTML =
                '<i class="fa-solid fa-duotone fa-plus"></i> CREAR DOCENTE';
            $('#modal-formulario').modal('show');
        });


        // ─── EDITAR ──────────────────────────────────────────────────────────────────
        $(document).on('click', '.btn-editar', function() {
            const id = $(this).data('id');

            $.get("{{ route('docentes.index') . '/' }}" + id, function(response) {
                const d = response.data;
                const form = $('#form-crear-o-editar');

                form.find('input[name="id_docente"]').val(d.id_docente);

                // Datos personales
                form.find('input[name="apellido_paterno"]').val(d.persona.apellido_paterno);
                form.find('input[name="apellido_materno"]').val(d.persona.apellido_materno);
                form.find('input[name="nombres"]').val(d.persona.nombres);
                form.find('input[name="documento_identificacion"]').val(d.persona
                    .documento_identificacion);
                form.find('input[name="documento_complemento"]').val(d.persona
                    .documento_complemento);
                form.find('select[name="documento_expedido"]').val(d.persona
                .documento_expedido);
                form.find('input[name="fecha_nacimiento"]').val(d.persona.fecha_nacimiento);
                form.find('select[name="sexo"]').val(d.persona.sexo);
                form.find('select[name="idioma"]').val(d.persona.idioma);
                form.find('input[name="celular"]').val(d.persona.celular);
                form.find('input[name="telefono"]').val(d.persona.telefono);

                // Datos de acceso
                const foto = (d.persona.usuario && d.persona.usuario.url_foto_perfil !== '') ?
                    URL_BASE + '/' + d.persona.usuario.url_foto_perfil :
                    URL_BASE + '/public/img/user.png';
                $('#preview_foto_perfil').attr('src', foto).show();
                form.find('input[name="foto_perfil"]').val('');
                form.find('input[name="correo"]').val(d.persona.usuario ? d.persona.usuario
                    .correo : '');

                // Al editar la contraseña es opcional → quitar required
                form.find('input[name="contrasenha"]').val('').removeAttr('required');
                form.find('input[name="confirmar_contrasenha"]').val('').removeAttr('required');
                $('#label-contrasenha-requerida, #label-confirmar-requerida').hide();

                // Datos del docente
                form.find('select[name="id_nivel"]').val(d.id_nivel || '');
                form.find('select[name="id_coordinacion"]').val(d.id_coordinacion || '');
                form.find('input[name="especialidad"]').val(d.especialidad);
                form.find('input[name="grado_estudios"]').val(d.grado_estudios);
                form.find('input[name="domicilio"]').val(d.domicilio);

                document.getElementById('modal-formulario-titulo').innerHTML =
                    '<i class="fa-solid fa-duotone fa-edit"></i> EDITAR DOCENTE';
                $('#modal-formulario').modal('show');
            });
        });


        // ─── GUARDAR (crear o editar) ────────────────────────────────────────────────
        $(document).on('click', '#btn-guardar', function() {
            const btn = $(this);
            btn.prop('disabled', true);
            btn.html('<i class="fa-solid fa-duotone fa-spinner fa-spin"></i> Guardando...');

            const id_docente = $('#form-crear-o-editar input[name="id_docente"]').val();
            const url = id_docente == 0 ?
                "{{ route('docentes.create') }}" :
                "{{ route('docentes.update', ':id') }}".replace(':id', id_docente);

            // FormData para poder enviar el archivo de foto
            const formData = new FormData($('#form-crear-o-editar')[0]);
            if (id_docente != 0) {
                // Laravel no acepta archivos en PUT nativo → method spoofing
                formData.append('_method', 'PUT');
            }

            $.ajax({
                url: url,
                type: 'POST', // siempre POST con FormData + _method spoofing
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                processData: false,
                contentType: false,
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
                    btn.html('<i class="fa-solid fa-duotone fa-save me-1"></i>Guardar');
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
                        htmlError = Object.values(respuesta.errors).flat().join("<br>");
                    } else if (respuesta.message) {
                        htmlError = respuesta.message;
                    } else {
                        htmlError = "Ocurrió un error inesperado.";
                    }

                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        title: 'Error',
                        html: 'Ocurrió un error al intentar la acción:<br>' +
                            htmlError,
                        icon: 'error'
                    });
                    btn.prop('disabled', false);
                    btn.html('<i class="fa-solid fa-duotone fa-save me-1"></i>Guardar');
                }
            });
        });


        // ─── CAMBIAR ESTADO ──────────────────────────────────────────────────────────
        $(document).on('click', '.btn-cambiar-estado', function() {
            const id = $(this).data('id');
            const estadoActual = $(this).data('estado');
            const estadoNuevo = estadoActual == 1 ? 0 : 1;
            const nombre = $(this).data('nombre');
            const accion = estadoNuevo == 1 ? 'desarchivar' : 'archivar';

            Swal.fire({
                theme: localStorage.getItem('theme') || 'dark',
                title: '¡ATENCIÓN!',
                html: `¿Estás seguro de <b>${accion}</b> al/la docente <span class="text-primary fw-bold">${nombre}</span>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Sí, ${accion}`,
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('docentes.index') . '/' }}" + id,
                        type: "PATCH",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id_docente: id
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
                        error: function() {
                            Swal.fire({
                                theme: localStorage.getItem('theme') ||
                                    'dark',
                                title: 'Error',
                                text: `No se pudo ${accion} el/la docente`,
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });

    });
</script>
