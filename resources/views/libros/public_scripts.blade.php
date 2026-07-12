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
        }).buttons().container().appendTo('#dataTable-export-buttons-container');

        function actualizarEstadisticas() {
            const dataTable = $("#dataTable").DataTable();
            const allData = dataTable.rows().data();

            let cantidadLibrosTotal = allData.count();
            let cantidadLibrosDisponibles = 0;
            let cantidadLibrosPrestados = 0;
            let cantidadLibrosEliminados = 0;

            allData.each(function(libro) {
                if (libro.estado == 1) {
                    cantidadLibrosDisponibles++;
                } else if (libro.estado == 2) {
                    cantidadLibrosPrestados++;
                } else if (libro.estado == 0) {
                    cantidadLibrosEliminados++;
                }
            });

            $('#cantidad-libros-total').text(cantidadLibrosTotal);
            $('#cantidad-libros-disponibles').text(cantidadLibrosDisponibles);
            $('#cantidad-libros-prestados').text(cantidadLibrosPrestados);
            $('#cantidad-libros-eliminados').text(cantidadLibrosEliminados);
        }

        $('#dataTable').on('draw.dt', function() {
            actualizarEstadisticas();
        });
    });
</script>
