<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtenemos el ID del estudiante (desde Blade si es vista de detalles, o vacío si es el estudiante autenticado)
        const idEstudiante = "{{ $estudiante->id_estudiante ?? '' }}";

        // Mapa de días (1 a 6)
        const diasMapa = {
            1: 'LUNES',
            2: 'MARTES',
            3: 'MIÉRCOLES',
            4: 'JUEVES',
            5: 'VIERNES',
            6: 'SÁBADO'
        };

        // Construcción de la URL dinámica
        const urlHorario = new URL("{{ route('listas_asignaturas.horario_estudiante') }}", window.location
            .origin);
        if (idEstudiante) {
            urlHorario.searchParams.append('id_estudiante', idEstudiante);
        }

        fetch(urlHorario, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                const cargandoEl = document.getElementById('cargandoHorarioEstudiante');
                if (!res.success) {
                    cargandoEl.innerHTML =
                        `<p class="text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> ${res.message}</p>`;
                    return;
                }

                const data = res.data;
                const fechaHoy = res.fecha_hoy;
                const inicioSemana = res.fecha_inicio_semana;

                // Actualizar badges superiores
                document.getElementById('periodo-actual-badge').innerHTML =
                    `<i class="fa-solid fa-duotone fa-layer-group me-1"></i>PERIODO ACTUAL: ${res.periodo_actual}`;
                document.getElementById('fecha-actual-badge').innerHTML =
                    `<i class="fa-duotone fa-calendar-day me-1"></i>${moment(fechaHoy).format('dddd DD/MM/YYYY').toUpperCase()}`;

                // Agrupación por días (Lunes a Sábado)
                let horarioAgrupado = {
                    1: [],
                    2: [],
                    3: [],
                    4: [],
                    5: [],
                    6: []
                };

                data.forEach(lista => {
                    const horarios = lista.asignatura?.horarios_asignaturas || [];

                    horarios.forEach(horario => {
                        let numDia = horario.pivot ? horario.pivot.dia_semana : null;
                        if (numDia && horarioAgrupado[numDia]) {
                            // Fecha exacta correspondiente al día de la semana
                            let fechaDelDia = moment(inicioSemana).add(numDia - 1, 'days')
                                .format('YYYY-MM-DD');

                            // Buscamos si hubo asistencia registrada para esta materia en este horario y fecha
                            let asistenciaClase = (lista.estudiantes_asistencias || [])
                                .find(a =>
                                    a.id_horario_asignatura == horario
                                    .id_horario_asignatura &&
                                    a.fecha === fechaDelDia
                                );

                            // Si existe la asistencia de la clase, extraemos el detalle de este estudiante
                            let detalleAsistencia = null;
                            if (asistenciaClase && asistenciaClase
                                .detalles_estudiantes_asistencias) {
                                detalleAsistencia = asistenciaClase
                                    .detalles_estudiantes_asistencias.find(d =>
                                        d.id_estudiante == (idEstudiante || d.id_estudiante)
                                    );
                            }

                            horarioAgrupado[numDia].push({
                                lista: lista,
                                horario: horario,
                                asistenciaClase: asistenciaClase,
                                detalleAsistencia: detalleAsistencia,
                                fechaDelDia: fechaDelDia
                            });
                        }
                    });
                });

                // Ordenar de forma cronológica por hora de inicio
                Object.keys(horarioAgrupado).forEach(dia => {
                    horarioAgrupado[dia].sort((a, b) => a.horario.hora_inicio.localeCompare(b
                        .horario.hora_inicio));
                });

                // Determinar día activo (si es domingo se posiciona en lunes)
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
                    tabsHtml += `
                    <li class="nav-item" role="presentation">
                        <button class="nav-link ${esActivo} fw-bold" id="tab-est-dia-${numDia}" data-bs-toggle="tab" data-bs-target="#pane-est-dia-${numDia}" type="button" role="tab">
                            ${nombreDia}${badgeHoy}
                        </button>
                    </li>`;

                    // 2. Construir Filas de la Tabla
                    let filasHtml = '';
                    if (horarioAgrupado[numDia].length === 0) {
                        filasHtml = `
                        <tr>
                            <td colspan="6" class="text-center align-middle py-4 text-muted">
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
                            let dateStr = moment(item.fechaDelDia).format('DD/MM');
                            let rowClass = (numDia == diaActivo) ? 'table-primary' : '';

                            // Información de Docente y Aula
                            let nombreDocente = item.lista.docente?.persona ?
                                `${item.lista.docente.persona.apellido_paterno} ${item.lista.docente.persona.nombres}` :
                                '<span class="text-muted">Docente no asignado</span>';
                            let aula = item.lista.asignatura?.aula?.aula ?
                                `<span class="badge bg-light text-dark border ms-1"><i class="fa-duotone fa-door-open me-1"></i>${item.lista.asignatura.aula.aula}</span>` :
                                '';

                            // Renderizado de Estado de Asistencia
                            let estadoAsistenciaHtml = '';
                            if (!item.asistenciaClase) {
                                estadoAsistenciaHtml =
                                    `<span class="badge bg-secondary-subtle text-muted border px-2 py-1"><i class="fa-duotone fa-clock me-1"></i>Sin registrar</span>`;
                            } else if (!item.detalleAsistencia) {
                                estadoAsistenciaHtml =
                                    `<span class="badge bg-light text-dark border px-2 py-1">No evaluado</span>`;
                            } else {
                                let tipo = item.detalleAsistencia.tipo;
                                let tiempoAtraso = item.detalleAsistencia.tiempo_atraso;

                                switch (tipo) {
                                    case 'PRESENTE':
                                        estadoAsistenciaHtml =
                                            `<span class="badge bg-success">
                                                <i class="fa fa-solid fa-duotone fa-check-circle me-1"></i>${tipo}
                                            </span>`;
                                        break;
                                    case 'ATRASO':
                                        let txtMin = tiempoAtraso ?
                                            `(${tiempoAtraso} min)` : '';
                                        estadoAsistenciaHtml =
                                            `<span class="badge bg-warning text-dark">
                                                <i class="fa fa-solid fa-duotone fa-hourglass-half me-1"></i>${tipo} ${txtMin}
                                            </span>`;
                                        break;
                                    case 'FALTA':
                                        estadoAsistenciaHtml =
                                            `<span class="badge bg-danger">
                                                <i class="fa fa-solid fa-duotone fa-times-circle me-1"></i>${tipo}
                                            </span>`;
                                        break;
                                    case 'LICENCIA':
                                        estadoAsistenciaHtml =
                                            `<span class="badge bg-info text-dark">
                                                <i class="fa fa-solid fa-duotone fa-file-medical me-1"></i>${tipo}
                                            </span>`;
                                        break;
                                    case 'PERMISO':
                                        estadoAsistenciaHtml =
                                            `<span class="badge bg-primary">
                                                <i class="fa fa-solid fa-duotone fa-file-signature me-1"></i>${tipo}
                                            </span>`;
                                        break;
                                    case 'OTROS':
                                        estadoAsistenciaHtml =
                                            `<span class="badge bg-secondary">
                                                <i class="fa-duotone fa-calendar-star me-1"></i>${tipo}
                                            </span>`;
                                        break;
                                    default:
                                        estadoAsistenciaHtml =
                                            `<span class="badge bg-secondary">
                                                <i class="fa fa-solid fa-duotone fa-question-circle me-1"></i>${tipo}
                                            </span>`;
                                }
                            }

                            filasHtml += `
                            <tr class="${rowClass}">
                                <td class="text-center align-middle">${index + 1}</td>
                                <td class="align-middle fw-bold text-info">${item.horario.denominacion}</td>
                                <td class="text-center align-middle">
                                    ${horaInicio} - ${horaFin}<br>
                                    <span class="badge bg-primary mt-1">${dateStr}</span>
                                </td>
                                <td class="align-middle">
                                    <div class="fw-bold">${item.lista.asignatura.asignatura} ${aula}</div>
                                    <span class="badge bg-${item.lista.asignatura.tipo_bloque === 'curso' ? 'primary' : 'danger'} mt-1">
                                        <i class="fa-duotone fa-cubes me-1"></i>Bloque ${item.lista.asignatura.tipo_bloque.toUpperCase()}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    ${nombreDocente}
                                </td>
                                <td class="text-center align-middle">
                                    ${estadoAsistenciaHtml}
                                </td>
                            </tr>`;
                        });
                    }

                    // 3. Renderizar Contenedor de la Tabla
                    contentHtml += `
                    <div class="tab-pane fade ${showActivo}" id="pane-est-dia-${numDia}" role="tabpanel" tabindex="0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped w-100 mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 5%;">#</th>
                                        <th>Periodo</th>
                                        <th class="text-center">Horario</th>
                                        <th>Asignatura</th>
                                        <th>Docente</th>
                                        <th class="text-center">Mi Asistencia</th>
                                    </tr>
                                </thead>
                                <tbody>${filasHtml}</tbody>
                            </table>
                        </div>
                    </div>`;
                });

                document.getElementById('tabsHorarioEstudiante').innerHTML = tabsHtml;
                document.getElementById('contenidoHorarioEstudiante').innerHTML = contentHtml;
            })
            .catch(error => {
                console.error("Error cargando el horario del estudiante:", error);
                document.getElementById('cargandoHorarioEstudiante').innerHTML =
                    `<p class="text-danger"><i class="fa-solid fa-server me-1"></i>Error al comunicarse con el servidor.</p>`;
            });
    });
</script>
