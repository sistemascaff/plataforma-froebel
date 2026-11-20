<style>

</style>

<script>
    $(document).ready(function() {
        const table = $("#table-cumpleanhos").DataTable({
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
                dataSrc: function(json) {
                    const registros = json.data || [];
                    const hoyMD = moment().format('MM-DD');
                    const anioActual = moment().year();

                    // Filtramos los que cumplen años hoy
                    const cumpleanherosHoy = registros.filter(item => {
                        return item.fecha_nacimiento && moment(item.fecha_nacimiento)
                            .format('MM-DD') === hoyMD;
                    });

                    // Si hay al menos uno, construimos la lista y mostramos el Swal
                    if (cumpleanherosHoy.length > 0) {
                        const listaHtml = cumpleanherosHoy.map(persona => {
                            const edad = anioActual - moment(persona.fecha_nacimiento)
                            .year();
                            const cursoInfo = persona.estudiante ?
                                ` (${persona.estudiante.curso.curso})` : '';

                            return `
                                <li class="list-group-item d-flex justify-content-between align-items-center text-start bg-transparent">
                                    <div>
                                        <span class="fw-bold d-block">${persona.nombres_apellidos}</span>
                                        <small class="text-muted">${persona.tipo_perfil}${cursoInfo}</small>
                                    </div>
                                    <span class="badge bg-success rounded-pill fs-6">${edad} años</span>
                                </li>
                            `;
                        }).join('');

                        Swal.fire({
                            theme: localStorage.getItem('theme') || 'dark',
                            title: '¡Cumpleaños de Hoy!',
                            icon: 'info',
                            html: `
                                <p class="mb-2">Las siguientes personas cumplen años el día de hoy:</p>
                                <ul class="list-group list-group-flush border rounded">
                                    ${listaHtml}
                                </ul>
                            `,
                            confirmButtonText: '¡Genial!',
                            confirmButtonColor: '#198754'
                        });
                    }

                    // Retornamos los datos intactos para que DataTables renderice la tabla
                    return registros;
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
                {
                    data: "fecha_nacimiento",
                    className: "align-middle text-center",
                    render: function(data, type, row) {
                        if (!data) return '';

                        const hoy = moment().startOf('day');
                        const nacimiento = moment(data);

                        // Proyectamos el mes y día de nacimiento al año actual
                        const cumpleEsteAnio = moment({
                            year: hoy.year(),
                            month: nacimiento.month(),
                            day: nacimiento.date()
                        }).startOf('day');

                        // Calcula la diferencia exacta en días
                        const diasDiferencia = cumpleEsteAnio.diff(hoy, 'days');

                        if (diasDiferencia === 0) {
                            return '<span class="badge bg-success fs-6 shadow-sm">Hoy</span>';
                        } else if (diasDiferencia > 0) {
                            const textoDias = diasDiferencia === 1 ? '1 día' :
                                `${diasDiferencia} días`;
                            return `<span class="badge bg-warning text-dark fs-6 shadow-sm">En ${textoDias}</span>`;
                        } else {
                            const absDias = Math.abs(diasDiferencia);
                            const textoDias = absDias === 1 ? '1 día' : `${absDias} días`;
                            return `<span class="badge bg-secondary fs-6 shadow-sm">Hace ${textoDias}</span>`;
                        }
                    }
                }
            ],
            ...dtDefaults,
        }).buttons();

        $("#btn-filtrar").on("click", function(e) {
            e.preventDefault();
            $("#table-cumpleanhos").DataTable().ajax.reload();
        });
    });
</script>
