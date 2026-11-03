<style>

</style>

<script>
    $(document).ready(function() {
        $("#table-cumpleanhos").DataTable({
            processing: true,
            ajax: {
                url: "{{ route('personas.listar_cumpleanhos') }}", // Ruta de Laravel
                type: "GET",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: function(d) {
                    d.tipo_perfil = $('#filter_tipo_perfil').val();
                    d.rango_fechas = $('#filter_rango_fechas').val();
                    d.solo_personal = $('#filter_solo_personal').is(':checked') ? 1 : 0;
                },
                error: function(xhr, error, thrown) {
                    console.error("Error al cargar los datos:", error);
                }
            },
            createdRow: function(row, data, dataIndex) {
                if (data.fecha_nacimiento) {
                    const cumpleHoy = moment(data.fecha_nacimiento).format('MM-DD') === moment()
                        .format('MM-DD');
                    if (cumpleHoy) {
                        $(row).addClass('table-success');
                    }
                }
            },
            columns: [{
                    data: null,
                    className: "align-middle text-center",
                    render: function(data, type, row, meta) {
                        return meta.row + 1; // número de iteración
                    }
                },
                {
                    data: "nombres_apellidos",
                },
                {
                    data: "tipo_perfil",
                    className: "align-middle text-center",
                },
                {
                    data: "estudiante",
                    className: "align-middle text-center",
                    render: function(data, type, row) {
                        return data ?
                            `<span class="badge bg-info text-dark">${data.curso.curso}</span>` :
                            '<span class="badge bg-secondary">N/A</span>';
                    }
                },
                {
                    data: "fecha_nacimiento",
                    className: "align-middle text-center",
                    render: function(data, type, row) {
                        return data ? moment(data).format('DD/MM/YYYY') : '';
                    }
                },
                {
                    data: "fecha_nacimiento",
                    className: "align-middle text-center",
                    render: function(data, type, row) {
                        if (!data) return '';

                        // Calcula la edad que cumple/cumplió en el año actual
                        const anioNacimiento = moment(data).year();
                        const anioActual = moment().year();
                        const edadCumplir = anioActual - anioNacimiento;

                        return `<span class="badge bg-primary fs-6 shadow-sm">${edadCumplir}</span>`;
                    }
                },
            ],
            ...dtDefaults,
        }).buttons();

        $("#btn-filtrar").on("click", function(e) {
            e.preventDefault();
            $("#table-cumpleanhos").DataTable().ajax.reload();
        });
    });
</script>
