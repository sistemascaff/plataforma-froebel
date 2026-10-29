<style>

</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Rutas base para reemplazar ID en JS
        const urlDetallesLista = "{{ route('listas_asignaturas.detalles', ':id') }}";

        // Reemplaza estas con los nombres reales de tus rutas si difieren
        const urlAsistenciaCreate = "{{ route('estudiantes_asistencias.crear', ':id') }}";
        const urlAsistenciaUpdate = "{{ route('estudiantes_asistencias.editar', ':id') }}";

        // Mapa de días (1 a 6)
        const diasMapa = {
            1: 'LUNES',
            2: 'MARTES',
            3: 'MIÉRCOLES',
            4: 'JUEVES',
            5: 'VIERNES',
            6: 'SÁBADO'
        };

        fetch("{{ route('listas_asignaturas.horario_docente') }}", {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                if (!res.success) {
                    document.getElementById('cargandoHorario').innerHTML =
                        `<p class="text-danger"><i class="fa-solid fa-triangle-exclamation"></i> ${res.message}</p>`;
                    return;
                }

                let data = res.data;
                let fechaHoy = res.fecha_hoy;
                let inicioSemana = res.fecha_inicio_semana;

                // Actualizar la información visual (Periodo y Fecha)
                document.getElementById('periodo-actual-badge').innerHTML =
                    `<i class="fa-solid fa-duotone fa-info-circle me-1"></i>PERIODO ACTUAL: ${res.periodo_actual}`;
                document.getElementById('fecha-actual-badge').innerHTML =
                    `<i class="fa-duotone fa-calendar-day me-1"></i>${moment(fechaHoy).format('dddd DD/MM/YYYY').toUpperCase()}`;

                let horarioAgrupado = {
                    1: [],
                    2: [],
                    3: [],
                    4: [],
                    5: [],
                    6: []
                };

                data.forEach(lista => {
                    lista.asignatura.horarios_asignaturas.forEach(horario => {
                        let numDia = horario.pivot.dia_semana;
                        if (horarioAgrupado[numDia]) {
                            // Calculamos la fecha exacta de este día en la semana actual
                            let fechaDelDia = moment(inicioSemana).add(numDia - 1, 'days')
                                .format('YYYY-MM-DD');

                            // Verificamos si existe asistencia tomada para ESTE horario EN ESTA fecha exacta
                            let asistenciaDia = lista.estudiantes_asistencias.find(a =>
                                a.id_horario_asignatura == horario
                                .id_horario_asignatura &&
                                a.fecha === fechaDelDia
                            );

                            horarioAgrupado[numDia].push({
                                lista: lista,
                                horario: horario,
                                asistenciaDia: asistenciaDia,
                                fechaDelDia: fechaDelDia
                            });
                        }
                    });
                });

                Object.keys(horarioAgrupado).forEach(dia => {
                    horarioAgrupado[dia].sort((a, b) => a.horario.hora_inicio.localeCompare(b
                        .horario.hora_inicio));
                });

                let hoyIdx = moment().isoWeekday();
                let diaActivo = (hoyIdx >= 1 && hoyIdx <= 6) ? hoyIdx : 1;

                let tabsHtml = '';
                let contentHtml = '';

                Object.entries(diasMapa).forEach(([numDia, nombreDia]) => {
                    let esActivo = (numDia == diaActivo) ? 'active' : '';
                    let showActivo = (numDia == diaActivo) ? 'show active' : '';
                    let badgeHoy = (numDia == diaActivo) ?
                        ' <i class="fa-solid fa-circle text-success fs-8 ms-1"></i>' : '';

                    // 1. Renderizar Nav Item
                    tabsHtml += `<li class="nav-item" role="presentation">
                        <button class="nav-link ${esActivo} fw-bold" id="tab-dia-${numDia}" data-bs-toggle="tab" data-bs-target="#pane-dia-${numDia}" type="button" role="tab">${nombreDia}${badgeHoy}</button>
                    </li>`;

                    // 2. Construir Filas
                    let filasHtml = '';
                    if (horarioAgrupado[numDia].length === 0) {
                        filasHtml = `
                            <tr>
                                <td colspan="5" class="text-center align-middle py-4 text-muted">
                                    <i class="fa-duotone fa-mug-hot fa-2x mb-2 text-secondary"></i>
                                    <div class="mt-1">No tienes clases asignadas este día.</div>
                                </td>
                            </tr>`;
                    } else {
                        horarioAgrupado[numDia].forEach((item, index) => {
                            let horaInicio = moment(item.horario.hora_inicio, 'HH:mm:ss')
                                .format('HH:mm');
                            let horaFin = moment(item.horario.hora_fin, 'HH:mm:ss').format(
                                'HH:mm');
                            let linkLista = urlDetallesLista.replace(':id', item.lista
                                .id_lista_asignatura);

                            let btnAsistencia = '';
                            if (item.asistenciaDia) {
                                let linkAsistencia = urlAsistenciaUpdate.replace(':id', item
                                    .asistenciaDia.id_estudiante_asistencia);
                                btnAsistencia = `<a href="${linkAsistencia}" class="btn btn-sm btn-warning text-dark shadow-sm" title="Modificar Asistencia">
                                    <i class="fa-solid fa-duotone fa-edit me-1"></i> Ver/Editar Asistencia
                                </a>`;
                            } else {
                                let linkAsistencia = urlAsistenciaCreate.replace(':id', item
                                    .lista.id_lista_asignatura);
                                btnAsistencia = `<a href="${linkAsistencia}" class="btn btn-sm btn-success shadow-sm" title="Registrar Asistencia">
                                    <i class="fa-solid fa-duotone fa-plus me-1"></i> Tomar Asistencia
                                </a>`;
                            }

                            // Subrayado sutil y badge si la fecha de esta pestaña ya pasó o es futura (opcional visual)
                            let dateStr = moment(item.fechaDelDia).format('DD/MM');
                            let rowClass = (numDia == diaActivo) ? 'table-primary' : '';

                            filasHtml += `<tr class="${rowClass}">
                                <td class="text-center align-middle">${index + 1}</td>
                                <td class="align-middle fw-bold">${item.horario.denominacion}</td>
                                <td class="text-center align-middle">
                                    ${horaInicio} - ${horaFin}<br>
                                    <span class="badge bg-primary">${dateStr}</span>
                                </td>
                                <td class="align-middle">
                                    <div class="fw-bold">${item.lista.asignatura.asignatura}</div>
                                    <span class="badge bg-${item.lista.asignatura.tipo_bloque === 'curso' ? 'primary' : 'danger'} mt-1"><i class="fa-duotone fa-cubes me-1"></i>Bloque ${item.lista.asignatura.tipo_bloque.toUpperCase()}</span>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                                        <a href="${linkLista}" class="btn btn-sm btn-primary shadow-sm" title="Ver información">
                                            <i class="fa-solid fa-duotone fa-list-check me-1"></i> Ver lista
                                        </a>
                                        ${btnAsistencia}
                                    </div>
                                </td>
                            </tr>`;
                        });
                    }

                    // 3. Renderizar Contenedor
                    contentHtml += `<div class="tab-pane fade ${showActivo}" id="pane-dia-${numDia}" role="tabpanel" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped w-100 mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 5%;">#</th>
                                        <th>Periodo</th>
                                        <th class="text-center">Horario</th>
                                        <th>Asignatura</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>${filasHtml}</tbody>
                            </table>
                        </div>
                    </div>`;
                });

                document.getElementById('tabsHorario').innerHTML = tabsHtml;
                document.getElementById('contenidoHorario').innerHTML = contentHtml;
            })
            .catch(error => {
                console.error("Error cargando el horario:", error);
                document.getElementById('cargandoHorario').innerHTML =
                    `<p class="text-danger"><i class="fa-solid fa-server me-1"></i> Error al comunicarse con el servidor.</p>`;
            });
    });
</script>
