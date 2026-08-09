<script>
    $(document).ready(function() {
        $("#dataTable").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('estudiantes_asistencias.listar') }}", // Ruta de Laravel
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
                    data: "fecha",
                },
                {
                    data: "horario_asignatura",
                    render: function(data, type, row, meta) {
                        return `${data.denominacion} (${data.hora_inicio} - ${data.hora_fin})`
                    }
                },
                {
                    data: "lista_asignatura.asignatura.asignatura",
                },
                {
                    data: "lista_asignatura.docente.persona.nombres_apellidos",
                },
                {
                    data: "lista_asignatura.periodo",
                    render: function(data, type, row, meta) {
                        return `${data.gestion.anio} - ${data.periodo}`
                    }
                },
                {
                    data: "detalles_estudiantes_asistencias_count",
                    render: function(data, type, row, meta) {
                        return `<span class="badge bg-primary">${data}</span>` || 0;
                    }
                },
                {
                    data: "presentes_count",
                    render: function(data, type, row, meta) {
                        return `<span class="badge bg-success">${data}</span>` || 0;
                    }
                },
                {
                    data: "atrasos_count",
                    render: function(data, type, row, meta) {
                        return data > 0 ?
                            `<span class="badge bg-warning text-dark">${data}</span>` :
                            `<span class="text-muted">0</span>`;
                    }
                },
                {
                    data: "faltas_count",
                    render: function(data, type, row, meta) {
                        return data > 0 ?
                            `<span class="badge bg-danger">${data}</span>` :
                            `<span class="text-muted">0</span>`;
                    }
                },
                {
                    data: "licencias_count",
                    render: function(data, type, row, meta) {
                        return data > 0 ?
                            `<span class="badge bg-info">${data}</span>` :
                            `<span class="text-muted">0</span>`;
                    }
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
                        const url_detalles =
                            "{{ route('estudiantes_asistencias.detalles', ':id') }}"
                            .replace(':id', row.id_estudiante_asistencia);

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
