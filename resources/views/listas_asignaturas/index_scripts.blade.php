<script>
    $(document).ready(function() {
        $("#dataTable").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('listas_asignaturas.listar') }}", // Ruta de Laravel
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
                    data: "asignatura.asignatura",
                },
                {
                    data: "asignatura.tipo_calificacion",
                    render: function(data, type, row) {
                        const i = data === 'cualitativa' ? 'fa-comments' : 'fa-chart-column';
                        return `<span class="badge bg-info text-dark"><i class="fa-solid fa-duotone ${i} me-1"></i>${data.toUpperCase()}</span>`;
                    }
                },
                {
                    data: "asignatura.tipo_bloque",
                    render: function(data, type, row) {
                        const bg = data === 'curso' ? 'bg-primary' : 'bg-danger';
                        return `<span class="badge ${bg}">${data.toUpperCase()}</span>`;
                    }
                },
                {
                    data: "periodo.periodo",
                    render: function(data, type, row) {
                        return `<span class="badge bg-light text-dark">${data}</span>`;
                    }
                },
                {
                    data: "docente.persona.nombres_apellidos",
                    render: function(data, type, row) {
                        return data ? `${data}` : '<span class="text-danger"><i class="fa-duotone fa-triangle-exclamation"></i> Sin asignar</span>';
                    }
                },
                {
                    data: "periodo.gestion.anio",
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        const url_detalles = "{{ route('listas_asignaturas.detalles', ':id') }}"
                            .replace(':id', row.id_lista_asignatura);

                        return `
                            <div class="btn-group" role="group">
                                <a class="btn btn-info btn-sm" href="${url_detalles}" target="_blank" rel="noopener noreferrer"
                                    data-toggle="tooltip" title="Detalles">
                                    <i class="fa-duotone fa-solid fa-eye"></i>
                                </a>
                            </div>`;
                    }
                }
            ],
            @include('components.datatables.datatables_global_properties')
            @include('components.datatables.datatables_language_property')
        }).buttons().container().appendTo('#dataTable-export-buttons-container');
    });
</script>
