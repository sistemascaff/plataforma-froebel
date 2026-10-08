<script>
    const AUTH_PERFIL = '{{ Auth::user()->persona?->tipo_perfil }}';
    const ES_ADMINISTRADOR = AUTH_PERFIL === 'ADMINISTRADOR';
    const ROLES_PERMITIDOS = ['SECRETARIA ACADEMICA'];
    const PUEDE_EDITAR = ROLES_PERMITIDOS.includes(AUTH_PERFIL) || ES_ADMINISTRADOR;
    const PUEDE_ELIMINAR = ROLES_PERMITIDOS.includes(AUTH_PERFIL) || ES_ADMINISTRADOR;
    const PUEDE_CREAR_LICENCIAS = ROLES_PERMITIDOS.includes(AUTH_PERFIL) || ES_ADMINISTRADOR;
    const URL_BASE = "{{ URL::to('/') }}";

    $(document).ready(function() {
        $('#filter_id_grado, #filter_id_curso').select2({
            language: "es",
            dropdownCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
            selectionCssClass: localStorage.getItem('theme') == 'dark' ? 'bg-dark text-white' : '',
        });

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
                url: "{{ route('estudiantes.listar') }}",
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    d.id_nivel = $('#filter_id_nivel').val();
                    d.id_grado = $('#filter_id_grado').val();
                    d.id_curso = $('#filter_id_curso').val();
                    d.id_paralelo = $('#filter_id_paralelo').val();
                    d.estado = $('#filter_estado').val();
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
                    data: "curso.curso",
                    render: function(data) {
                        return `<span class="badge bg-info text-dark">${data}</span>`;
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
                        return `<img class="rounded zoomable-image" src="${src}" alt="Foto" style="width:40px; height:40px; object-fit:cover;">`;
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
                        return data ? moment(data).format('DD/MM/YYYY') : '';
                    }
                },
                {
                    data: "persona.sexo",
                    render: function(data) {
                        return data == 'M' ? 'MASCULINO' : 'FEMENINO';
                    }
                },
                {
                    data: "persona.idioma",
                    visible: ES_ADMINISTRADOR,
                },
                {
                    data: "persona.celular",
                    visible: ES_ADMINISTRADOR,
                },
                {
                    data: "persona.telefono",
                    visible: ES_ADMINISTRADOR,
                },
                {
                    data: "persona.tipo_perfil",
                    visible: ES_ADMINISTRADOR,
                },
                {
                    data: "persona.usuario.correo"
                },
                {
                    data: "persona.usuario.contrasenha_descifrada",
                    render: function(data) {
                        return data ? data : '<span class="text-muted">Sin contraseña</span>';
                    }
                },
                {
                    data: "codigo_interno"
                },
                {
                    data: "codigo_rude"
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
                    data: "nacimiento_pais",
                    visible: ES_ADMINISTRADOR,
                },
                {
                    data: "nacimiento_departamento",
                    visible: ES_ADMINISTRADOR,
                },
                {
                    data: "nacimiento_provincia",
                    visible: ES_ADMINISTRADOR,
                },
                {
                    data: "nacimiento_localidad",
                    visible: ES_ADMINISTRADOR,
                },
                {
                    data: "salud_tipo_sangre",
                    visible: ES_ADMINISTRADOR,
                },
                {
                    data: "salud_alergias",
                    visible: ES_ADMINISTRADOR,
                },
                {
                    data: "salud_datos",
                    visible: ES_ADMINISTRADOR,
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
                    visible: ES_ADMINISTRADOR,
                    render: function(data) {
                        return data ? moment(data).format('DD/MM/YYYY HH:mm:ss') : '';
                    }
                },
                {
                    data: "fecha_actualizacion",
                    visible: ES_ADMINISTRADOR,
                    render: function(data) {
                        return data ? moment(data).format('DD/MM/YYYY HH:mm:ss') : '';
                    }
                },
                {
                    data: "fecha_eliminacion",
                    visible: ES_ADMINISTRADOR,
                    render: function(data) {
                        return data ? moment(data).format('DD/MM/YYYY HH:mm:ss') : '';
                    }
                },
                {
                    data: "creado.correo",
                    visible: ES_ADMINISTRADOR,
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: "modificado.correo",
                    visible: ES_ADMINISTRADOR,
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: "eliminado.correo",
                    visible: ES_ADMINISTRADOR,
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: "ip",
                    visible: ES_ADMINISTRADOR,
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: "dispositivo",
                    visible: ES_ADMINISTRADOR,
                    render: function(data) {
                        return data || '-';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        const url_detalles = "{{ route('estudiantes.detalles', ':id') }}"
                            .replace(':id', row.id_estudiante);
                        const url_licencia =
                            `{{ route('estudiantes_licencias.index') }}?id_estudiante=${row.id_estudiante}`;

                        let botones = `<div class="btn-group" role="group">`;

                        if (PUEDE_CREAR_LICENCIAS) {
                            botones += `
                            <a class="btn btn-info btn-sm" href="${url_licencia}" target="_blank" rel="noopener noreferrer"
                                data-toggle="tooltip" title="Registrar licencia">
                                <i class="fa-duotone fa-solid fa-file-medical"></i>
                            </a>`;
                        }

                        // El botón de detalles suele ser público/accesible por defecto
                        botones += `
                            <a class="btn btn-primary btn-sm" href="${url_detalles}" target="_blank" rel="noopener noreferrer"
                                data-toggle="tooltip" title="Detalles">
                                <i class="fa-duotone fa-solid fa-eye"></i>
                            </a>`;

                        if (PUEDE_EDITAR) {
                            botones += `
                            <button type="button" class="btn btn-warning btn-sm btn-editar"
                                    data-id="${row.id_estudiante}" data-toggle="tooltip" title="Editar">
                                <i class="fa-duotone fa-solid fa-edit"></i>
                            </button>`;
                        }

                        if (PUEDE_ELIMINAR) {
                            let colorBtn = row.estado == 1 ? 'danger' : 'success';
                            let iconBtn = row.estado == 1 ? 'off' : 'on';
                            let titleBtn = row.estado == 1 ? 'Archivar' : 'Activar';

                            botones += `
                            <button type="button" class="btn btn-${colorBtn} btn-sm btn-cambiar-estado"
                                    data-id="${row.id_estudiante}" data-estado="${row.estado}"
                                    data-nombre="${row.persona.apellidos_nombres}"
                                    data-toggle="tooltip" title="${titleBtn}">
                                <i class="fa-duotone fa-solid fa-toggle-${iconBtn}"></i>
                            </button>`;
                        }

                        botones += `</div>`;

                        return botones;
                    }
                }
            ],
            @include('components.datatables.datatables_global_properties')
            @include('components.datatables.datatables_language_property')
        }).buttons().container().appendTo('#dataTable-export-buttons-container');

        $("#btn-filtrar").on("click", function(e) {
            e.preventDefault();
            $("#dataTable").DataTable().ajax.reload();
        });

        // ─── CREAR ───────────────────────────────────────────────────────────────────
        $(document).on('click', '.btn-crear', function() {
            const form = $('#form-crear-o-editar');

            form.find('input[name="id_estudiante"]').val(0);

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

            // Datos del estudiante
            form.find('select[name="id_curso"]').val('');
            form.find('input[name="nacimiento_pais"]').val('');
            form.find('input[name="nacimiento_departamento"]').val('');
            form.find('input[name="nacimiento_provincia"]').val('');
            form.find('input[name="nacimiento_localidad"]').val('');
            form.find('select[name="salud_tipo_sangre]').val('');
            form.find('input[name="salud_alergias"]').val('');
            $('#salud_datos').val('');


            document.getElementById('modal-formulario-titulo').innerHTML =
                '<i class="fa-solid fa-duotone fa-plus"></i> CREAR ESTUDIANTE';
            $('#modal-formulario').modal('show');
        });


        // ─── EDITAR ──────────────────────────────────────────────────────────────────
        $(document).on('click', '.btn-editar', function() {
            const id = $(this).data('id');

            $.get("{{ route('estudiantes.index') . '/' }}" + id, function(response) {
                const d = response.data;
                const form = $('#form-crear-o-editar');

                form.find('input[name="id_estudiante"]').val(d.id_estudiante);

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

                // Datos del estudiante
                form.find('select[name="id_curso"]').val(d.id_curso);
                form.find('input[name="nacimiento_pais"]').val(d.nacimiento_pais);
                form.find('input[name="nacimiento_departamento"]').val(d
                    .nacimiento_departamento);
                form.find('input[name="nacimiento_provincia"]').val(d.nacimiento_provincia);
                form.find('input[name="nacimiento_localidad"]').val(d.nacimiento_localidad);
                form.find('select[name="salud_tipo_sangre]').val(d.salud_tipo_sangre);
                form.find('input[name="salud_alergias"]').val(d.salud_alergias);
                $('#salud_datos').val(d.salud_datos);

                document.getElementById('modal-formulario-titulo').innerHTML =
                    '<i class="fa-solid fa-duotone fa-edit"></i> EDITAR ESTUDIANTE';
                $('#modal-formulario').modal('show');
            });
        });


        // ─── GUARDAR (crear o editar) ────────────────────────────────────────────────
        $(document).on('click', '#btn-guardar', function() {
            const btn = $(this);
            btn.prop('disabled', true);
            btn.html('<i class="fa-solid fa-duotone fa-spinner fa-spin"></i> Guardando...');

            const id_estudiante = $('#form-crear-o-editar input[name="id_estudiante"]').val();
            const url = id_estudiante == 0 ?
                "{{ route('estudiantes.create') }}" :
                "{{ route('estudiantes.update', ':id') }}".replace(':id', id_estudiante);

            // FormData para poder enviar el archivo de foto
            const formData = new FormData($('#form-crear-o-editar')[0]);
            if (id_estudiante != 0) {
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
                html: `¿Estás seguro de <b>${accion}</b> al/la estudiante <span class="text-primary fw-bold">${nombre}</span>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Sí, ${accion}`,
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('estudiantes.index') . '/' }}" + id,
                        type: "PATCH",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id_estudiante: id
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
                                text: `No se pudo ${accion} el/la estudiante`,
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });

    });
</script>
