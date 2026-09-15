<script>
    const URL_BASE = "{{ URL::to('/') }}";

    $(document).ready(function() {
        // Preview de foto
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

        // DataTable
        $("#dataTable").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('personas.listar') }}",
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            },
            columns: [{
                    data: null,
                    className: "text-center align-middle",
                    render: (data, type, row, meta) => meta.row + 1
                },
                {
                    data: "usuario.url_foto_perfil",
                    className: "text-center align-middle",
                    orderable: false,
                    searchable: false,
                    render: function(data) {
                        const src = (data && data !== '') ? URL_BASE + '/' + data : URL_BASE +
                            '/public/img/user.png';
                        return `<img class="rounded zoomable-image shadow-sm" src="${src}" alt="Foto" style="width:40px; height:40px; object-fit:cover;">`;
                    }
                },
                {
                    data: "apellido_paterno",
                    className: "align-middle"
                },
                {
                    data: "apellido_materno",
                    className: "align-middle"
                },
                {
                    data: "nombres",
                    className: "align-middle fw-bold"
                },
                {
                    data: "documento_identificacion",
                    className: "align-middle"
                },
                {
                    data: "documento_complemento",
                    className: "align-middle"
                },
                {
                    data: "documento_expedido",
                    className: "align-middle"
                },
                {
                    data: "fecha_nacimiento",
                    className: "align-middle",
                    render: data => data ? new Date(data).toLocaleDateString() : '-'
                },
                {
                    data: "sexo",
                    className: "text-center align-middle",
                    render: data => data == 'M' ? 'MASCULINO' : 'FEMENINO'
                },
                {
                    data: "idioma",
                    className: "align-middle"
                },
                {
                    data: "celular",
                    className: "align-middle"
                },
                {
                    data: "telefono",
                    className: "align-middle"
                },
                {
                    data: "tipo_perfil",
                    className: "align-middle fw-bold text-secondary"
                },
                {
                    data: "usuario.correo",
                    className: "align-middle"
                },
                {
                    data: "usuario.tiene_acceso",
                    className: "text-center align-middle",
                    render: function(data) {
                        if (data == 1) return '<span class="badge bg-success">SI</span>';
                        if (data == 0) return '<span class="badge bg-danger">NO</span>';
                        return '<span class="badge bg-warning">DESCONOCIDO</span>';
                    }
                },
                {
                    data: "estado",
                    className: "text-center align-middle",
                    render: function(data) {
                        if (data == 1) return '<span class="badge bg-success">ACTIVO</span>';
                        if (data == 0)
                        return '<span class="badge bg-secondary">ARCHIVADO</span>';
                        return '<span class="badge bg-warning">DESCONOCIDO</span>';
                    }
                },
                {
                    data: "fecha_registro",
                    className: "align-middle",
                    render: data => data ? new Date(data).toLocaleString() : '-'
                },
                {
                    data: "fecha_actualizacion",
                    className: "align-middle",
                    render: data => data ? new Date(data).toLocaleString() : '-'
                },
                {
                    data: "fecha_eliminacion",
                    className: "align-middle",
                    render: data => data ? new Date(data).toLocaleString() : '-'
                },
                {
                    data: "creado.correo",
                    className: "align-middle",
                    render: data => data || '-'
                },
                {
                    data: "modificado.correo",
                    className: "align-middle",
                    render: data => data || '-'
                },
                {
                    data: "eliminado.correo",
                    className: "align-middle",
                    render: data => data || '-'
                },
                {
                    data: "ip",
                    className: "align-middle",
                    render: data => data || '-'
                },
                {
                    data: "dispositivo",
                    className: "align-middle",
                    render: data => data || '-'
                },
                {
                    data: null,
                    className: "text-center align-middle",
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        const url_detalles = "{{ route('personas.detalles', ':id') }}".replace(
                            ':id', row.id_persona);
                        return `
                            <div class="btn-group shadow-sm" role="group">
                                <a class="btn btn-info btn-sm" href="${url_detalles}" target="_blank" rel="noopener noreferrer" data-toggle="tooltip" title="Detalles">
                                    <i class="fa-duotone fa-solid fa-eye"></i>
                                </a>
                                <button type="button" class="btn btn-warning btn-sm btn-editar" data-id="${row.id_persona}" data-toggle="tooltip" title="Editar">
                                    <i class="fa-duotone fa-solid fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-${row.estado == 1 ? 'danger' : 'success'} btn-sm btn-cambiar-estado" 
                                        data-id="${row.id_persona}" data-estado="${row.estado}" data-nombre="${row.nombres} ${row.apellido_paterno}" 
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

        // CREAR
        $(document).on('click', '.btn-crear', function() {
            const form = $('#form-crear-o-editar');
            form.find('input[name="id_persona"]').val(0);
            form[0].reset();

            $('#preview_foto_perfil').attr('src', '#').hide();
            form.find('input[name="contrasenha"]').attr('required', true);
            form.find('input[name="confirmar_contrasenha"]').attr('required', true);
            $('#label-contrasenha-requerida, #label-confirmar-requerida').show();

            document.getElementById('modal-formulario-titulo').innerHTML =
                '<i class="fa-solid fa-duotone fa-user-plus me-2"></i> CREAR PERSONAL';
        });

        // EDITAR
        $(document).on('click', '.btn-editar', function() {
            const id = $(this).data('id');
            $.get("{{ route('personas.index') . '/' }}" + id, function(response) {
                const d = response.data;
                const form = $('#form-crear-o-editar');
                form.find('input[name="id_persona"]').val(d.id_persona);

                form.find('input[name="apellido_paterno"]').val(d.apellido_paterno);
                form.find('input[name="apellido_materno"]').val(d.apellido_materno);
                form.find('input[name="nombres"]').val(d.nombres);
                form.find('input[name="documento_identificacion"]').val(d
                    .documento_identificacion);
                form.find('input[name="documento_complemento"]').val(d.documento_complemento);
                form.find('select[name="documento_expedido"]').val(d.documento_expedido);
                form.find('input[name="fecha_nacimiento"]').val(d.fecha_nacimiento);
                form.find('select[name="sexo"]').val(d.sexo);
                form.find('select[name="idioma"]').val(d.idioma);
                form.find('input[name="celular"]').val(d.celular);
                form.find('input[name="telefono"]').val(d.telefono);
                form.find('select[name="tipo_perfil"]').val(d.tipo_perfil);

                const foto = (d.usuario && d.usuario.url_foto_perfil !== '') ? URL_BASE + '/' +
                    d.usuario.url_foto_perfil : URL_BASE + '/public/img/user.png';
                $('#preview_foto_perfil').attr('src', foto).show();
                form.find('input[name="foto_perfil"]').val('');
                form.find('input[name="correo"]').val(d.usuario ? d.usuario.correo : '');

                form.find('input[name="contrasenha"]').val('').removeAttr('required');
                form.find('input[name="confirmar_contrasenha"]').val('').removeAttr('required');
                $('#label-contrasenha-requerida, #label-confirmar-requerida').hide();

                document.getElementById('modal-formulario-titulo').innerHTML =
                    '<i class="fa-solid fa-duotone fa-user-pen me-2"></i> EDITAR PERSONAL';
                $('#modal-formulario').modal('show');
            });
        });

        // GUARDAR
        $(document).on('click', '#btn-guardar', function() {
            const btn = $(this);
            btn.prop('disabled', true).html(
                '<i class="fa-solid fa-duotone fa-spinner fa-spin"></i> Guardando...');

            const id_persona = $('#form-crear-o-editar input[name="id_persona"]').val();
            // Asegúrate de que las rutas 'personas.create' y 'personas.update' existan o cámbialas a las tuyas.
            const url = id_persona == 0 ? "{{ route('personas.create') }}" :
                "{{ route('personas.update', ':id') }}".replace(':id', id_persona);

            const formData = new FormData($('#form-crear-o-editar')[0]);
            if (id_persona != 0) formData.append('_method', 'PUT');

            $.ajax({
                url: url,
                type: 'POST',
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
                    btn.prop('disabled', false).html(
                        '<i class="fa-solid fa-duotone fa-save me-1"></i> Guardar');
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
                    let htmlError = respuesta.errors ? Object.values(respuesta.errors)
                    .flat().join("<br>") : (respuesta.message ||
                        "Ocurrió un error inesperado.");

                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        title: 'Error',
                        html: 'Ocurrió un error:<br>' + htmlError,
                        icon: 'error'
                    });
                    btn.prop('disabled', false).html(
                        '<i class="fa-solid fa-duotone fa-save me-1"></i> Guardar');
                }
            });
        });

        // CAMBIAR ESTADO
        $(document).on('click', '.btn-cambiar-estado', function() {
            const id = $(this).data('id');
            const estadoActual = $(this).data('estado');
            const estadoNuevo = estadoActual == 1 ? 0 : 1;
            const nombre = $(this).data('nombre');
            const accion = estadoNuevo == 1 ? 'desarchivar' : 'archivar';

            Swal.fire({
                theme: localStorage.getItem('theme') || 'dark',
                title: '¡ATENCIÓN!',
                html: `¿Estás seguro de <b>${accion}</b> a <span class="text-primary fw-bold">${nombre}</span>?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `Sí, ${accion}`,
                cancelButtonText: 'No, cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        // Asegúrate de que tu ruta base personas mapee correctamente al método de eliminar
                        url: "{{ route('personas.index') . '/' }}" + id,
                        type: "PATCH",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id_persona: id
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'Actualizado',
                                text: response.message,
                                icon: 'success'
                            });
                            $('#dataTable').DataTable().ajax.reload();
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error',
                                text: `No se pudo ${accion} al personal`,
                                icon: 'error'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
