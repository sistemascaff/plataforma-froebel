@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-user-tie"></i> {{ $headTitle }}</h1>

    <button type="button" class="btn btn-success mb-3 btn-crear" data-bs-toggle="modal" data-bs-target="#modalCreateOrEdit">
        <i class="fa-solid fa-duotone fa-plus"></i> Crear usuario</button>

    <h2 class="text-info fw-bold">Lista de usuarios</h2>

    <p>Nota: para que un usuario pueda ser registrado, previamente se le debe crear un <b>empleado</b>, si desea hacerlo
        haga clic <a href="{{ route('empleados.index') }}">aquí.</a></p>

    <div class="card p-3 mb-3">
        <p>Seleccione una opción para <i class="fa-solid fa-duotone fa-file-export"></i> exportar o <i
                class="fa-solid fa-duotone fa-filter"></i> filtrar la tabla:</p>
        <div id="dataTableExportButtonsContainer"></div>
    </div>

    <table class="table table-bordered table-striped" id="dataTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Nombre Usuario</th>
                <th>Empleado</th>
                <th>Tema</th>
                <th>Estado</th>
                <th>F. Registro</th>
                <th>F. Actualización</th>
                <th>Modificado Por</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>

    <div class="mb-3"></div>

    <!-- Modal para crear y editar usuarios -->
    <div class="modal fade" id="modalCreateOrEdit" tabindex="-1" aria-labelledby="modalCreateOrEdit_Title"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalCreateOrEdit_Title"><i class="fa-solid fa-duotone fa-plus"></i>
                        CREAR USUARIO</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formCreateOrEdit">
                        <!-- input de idUsuario en caso de editar -->
                        <input type="hidden" name="idUsuario" value="0">

                        <div class="mb-3">
                            <label for="nombreUsuario" class="form-label">Nombre de usuario <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombreUsuario" name="nombreUsuario" required>
                        </div>

                        <div class="mb-3">
                            <label for="empleado" class="form-label">Empleado <span class="text-danger">*</span></label><br>
                            <select style="width: 100%" class="form-select" id="empleado" name="idEmpleado" required>
                                <option value="" disabled selected>Seleccione un empleado</option>
                                @foreach ($empleados as $empleado)
                                    <option value="{{ $empleado->idEmpleado }}">{{ $empleado->nombreEmpleado }}
                                        {{ $empleado->estado == '2' ? '(Ya tiene usuario)' : '(Sin usuario)' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="temaPreferido" class="form-label">Tema preferido <span
                                    class="text-danger">*</span></label><br>
                            <select style="width: 100%" class="form-select" id="temaPreferido" name="temaPreferido"
                                required>
                                <option value="light" selected>CLARO</option>
                                <option value="dark">OSCURO</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="contrasenha" class="form-label">Contraseña <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="contrasenha" name="contrasenha" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-target="contrasenha">
                                    <i class="fa-solid fa-duotone fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="recontrasenha" class="form-label">Repita contraseña <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="recontrasenha" name="recontrasenha"
                                    required>
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-target="recontrasenha">
                                    <i class="fa-solid fa-duotone fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                            class="fa-solid fa-duotone fa-close"></i>Cerrar</button>
                    <button type="button" id="btnGuardar" class="btn btn-primary"><i
                            class="fa-solid fa-duotone fa-save"></i>
                        Guardar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $("#dataTable").DataTable({
                processing: true,
                ajax: {
                    url: "{{ route('usuarios.listar') }}", // Ruta de Laravel
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
                        data: "nombreUsuario",
                    },
                    {
                        data: "empleado.nombreEmpleado",
                    },
                    {
                        data: "temaPreferido",
                        render: function(data, type, row) {
                            switch (data) {
                                case 'dark':
                                    return 'OSCURO';
                                case 'light':
                                    return 'CLARO';
                                default:
                                    return data;
                            }
                        }
                    },
                    {
                        data: "estado",
                        render: function(data, type, row) {
                            if (data == 1) {
                                return '<span class="badge bg-success">Activo</span>';
                            } else {
                                return '<span class="badge bg-danger">Inactivo</span>';
                            }
                        }
                    },
                    {
                        data: "fechaRegistro",
                        render: function(data, type, row) {
                            return new Date(data).toLocaleString();
                        }
                    },
                    {
                        data: "fechaActualizacion",
                        render: function(data, type, row) {
                            return new Date(data).toLocaleString();
                        }
                    },
                    {
                        data: "editor.nombreUsuario",
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
                                data-id="${row.idUsuario}" data-toggle="tooltip" title="Editar">
                            <i class="fa-duotone fa-solid fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-${row.estado == 1 ? 'danger' : 'success'} btn-sm btn-cambiar-estado" 
                                data-id="${row.idUsuario}" data-estado="${row.estado}" data-nombre="${row.nombreUsuario}" 
                                data-toggle="tooltip" title="${row.estado == 1 ? 'Deshabilitar' : 'Habilitar'}">
                            <i class="fa-duotone fa-solid fa-toggle-${row.estado == 1 ? 'off' : 'on'}"></i>
                        </button>
                    </div>
                `;
                        }
                    }
                ],
                @include('datatables.dataTablesGlobalProperties')
                @include('datatables.dataTablesLanguageProperty')
            }).buttons().container().appendTo('#dataTableExportButtonsContainer');



            $(document).on('click', '.btn-crear', function() {
                const id = 0;
                $('#formCreateOrEdit input[name="idUsuario"]').val(0);
                $('#formCreateOrEdit input[name="nombreUsuario"]').val('');
                $('#formCreateOrEdit select[name="idEmpleado"]').val('')
                    .trigger('change');
                $('#formCreateOrEdit select[name="temaPreferido"]').val('light')
                    .trigger('change');
                $('#formCreateOrEdit input[name="contrasenha"]').val('');
                $('#formCreateOrEdit input[name="recontrasenha"]').val('');

                const titleElement = document.getElementById('modalCreateOrEdit_Title');
                titleElement.innerHTML = '<i class="fa-solid fa-duotone fa-plus"></i> CREAR USUARIO';
                $('#modalCreateOrEdit').modal('show');
            });



            $(document).on('click', '.btn-editar', function() {
                const id = $(this).data('id');

                $.get("{{ route('usuarios.index') . '/' }}" + id, function(usuario) {
                    $('#formCreateOrEdit input[name="idUsuario"]').val(usuario.data.idUsuario);
                    $('#formCreateOrEdit input[name="nombreUsuario"]').val(usuario.data
                        .nombreUsuario);
                    $('#formCreateOrEdit select[name="idEmpleado"]').val(usuario.data.idEmpleado)
                        .trigger('change');
                    $('#formCreateOrEdit select[name="temaPreferido"]').val(usuario.data
                            .temaPreferido)
                        .trigger('change');
                    $('#formCreateOrEdit input[name="contrasenha"]').val(''); // opcional, vacío
                    $('#formCreateOrEdit input[name="recontrasenha"]').val('');

                    const titleElement = document.getElementById('modalCreateOrEdit_Title');
                    titleElement.innerHTML =
                        '<i class="fa-solid fa-duotone fa-edit"></i> EDITAR USUARIO';
                    $('#modalCreateOrEdit').modal('show');
                });
            });


            $(document).on('click', '#btnGuardar', function() {
                const idUsuario = $('#formCreateOrEdit input[name="idUsuario"]').val();
                const url = idUsuario == 0 ?
                    "{{ route('usuarios.create') }}" // POST -> crear
                    :
                    "{{ route('usuarios.index') . '/' }}" + idUsuario; // PUT -> actualizar

                const type = idUsuario == 0 ? 'POST' : 'PUT';

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
                            $('#modalCreateOrEdit').modal('hide');
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
                const estadoActual = $(this).data('estado');
                const nuevoEstado = estadoActual == 1 ? 0 : 1;
                const nombre = $(this).data('nombre');
                const accion = nuevoEstado == 1 ? 'habilitar' : 'deshabilitar';

                Swal.fire({
                    title: `¡ATENCIÓN!`,
                    text: `¿Estás seguro de ${accion} el usuario ${nombre}?`,
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
                                idUsuario: id
                            },
                            success: function(response) {
                                Swal.fire('Actualizado', response.message, 'success');
                                $('#dataTable').DataTable().ajax.reload();
                            },
                            error: function() {
                                Swal.fire('Error', `No se pudo ${accion} el usuario`,
                                    'error');
                            }
                        });

                    }
                });
            });
        });

        $(document).ready(function() {
            $('#empleado').select2({
                language: "es",
                dropdownCssClass: "{{ session('temaPreferido') == 'dark' ? 'bg-dark' : '' }}",
                selectionCssClass: "{{ session('temaPreferido') == 'dark' ? 'bg-dark' : '' }}",
                dropdownParent: $('#modalCreateOrEdit')
            });
            document.querySelectorAll('.toggle-password').forEach(btn => {
                btn.addEventListener('click', function() {
                    const input = document.getElementById(this.dataset.target);
                    const type = input.type === 'password' ? 'text' : 'password';
                    input.type = type;
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            });
        });
    </script>
@endsection
