@extends('layouts.public')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-book-open"></i> {{ $head_title }}</h1>

    <h2 class="text-info fw-bold">Lista de libros</h2>

    <h5>En esta sección se encuentran todos los libros registrados en la biblioteca. Puede consultar su estado actual y otra información relevante.</h5>

    <h5>LEYENDA DE <b>ESTADO</b>:</h5>

    <p>
        <span class="badge bg-success">DISPONIBLE</span>: El libro está disponible para su préstamo.<br>
        <span class="badge bg-primary">EN USO</span>: El libro está actualmente prestado a una persona.<br>
        <span class="badge bg-secondary">ELIMINADO</span>: El libro ha sido dado de baja del inventario.<br>
    </p>

    <div class="card p-3 mb-3">
        <div class="row">
            <div class="col-12">
                <h4 class="text-dark-aquamarine fw-bold">Estadísticas de libros</h4>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card info-card shadow-sm border-info">
                    <div class="card-body d-flex align-items-center bg-info bg-opacity-10">
                        <div class="icon-box bg-info bg-opacity-10 me-3">
                            <i class="text-info fa-solid fa-duotone fa-book-open fa-xl"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Total</h6>
                            <h3 id="cantidad_libros_total" class="fw-bold">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card info-card shadow-sm border-success">
                    <div class="card-body d-flex align-items-center bg-success bg-opacity-10">
                        <div class="icon-box bg-success bg-opacity-10 me-3">
                            <i class="text-success fa-solid fa-duotone fa-book-open fa-xl"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Disponibles</h6>
                            <h3 id="cantidad_libros_disponibles" class="fw-bold">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card info-card shadow-sm border-primary">
                    <div class="card-body d-flex align-items-center bg-primary bg-opacity-10">
                        <div class="icon-box bg-primary bg-opacity-10 me-3">
                            <i class="text-primary fa-solid fa-duotone fa-book-open fa-xl"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Prestados</h6>
                            <h3 id="cantidad_libros_prestados" class="fw-bold">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card info-card shadow-sm border-secondary">
                    <div class="card-body d-flex align-items-center bg-secondary bg-opacity-10">
                        <div class="icon-box bg-secondary bg-opacity-10 me-3">
                            <i class="text-secondary fa-solid fa-duotone fa-book-open fa-xl"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Bajas</h6>
                            <h3 id="cantidad_libros_eliminados" class="fw-bold">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-3 mb-3">
        <p>Seleccione una opción para <i class="fa-solid fa-duotone fa-file-export"></i> exportar o <i
                class="fa-solid fa-duotone fa-filter"></i> filtrar la tabla:</p>
        <div id="datatable_export_buttons_container"></div>
    </div>

    <table class="table table-bordered table-striped" id="dataTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Categoria</th>
                <th>Editorial</th>
                <th>Año</th>
                <th>Presentación</th>
                <th>Estado</th>
            </tr>
        </thead>
    </table>

    <div class="mb-3"></div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $("#dataTable").DataTable({
                processing: true,
                ajax: {
                    url: "{{ route('libros.public.listar') }}",
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
                        data: "categoria",
                    },
                    {
                        data: "editorial",
                    },
                    {
                        data: "anio",
                    },
                    {
                        data: "presentacion",
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
                ],
                @include('components.datatables.datatables_global_properties')
                @include('components.datatables.datatables_language_property')
            }).buttons().container().appendTo('#datatable_export_buttons_container');

            function actualizar_estadisticas() {
                const dataTable = $("#dataTable").DataTable();
                const all_data = dataTable.rows().data();

                let cantidad_libros_total = all_data.count();
                let cantidad_libros_disponibles = 0;
                let cantidad_libros_prestados = 0;
                let cantidad_libros_eliminados = 0;

                all_data.each(function(libro) {
                    if (libro.estado == 1) {
                        cantidad_libros_disponibles++;
                    } else if (libro.estado == 2) {
                        cantidad_libros_prestados++;
                    } else if (libro.estado == 0) {
                        cantidad_libros_eliminados++;
                    }
                });

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
@endsection
