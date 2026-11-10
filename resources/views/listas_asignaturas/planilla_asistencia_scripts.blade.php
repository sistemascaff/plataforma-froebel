{{-- Librería ExcelJS para construir el archivo .xlsx con estilos, logo, rotación a 90° y celdas combinadas --}}
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const PLANILLA = @json($planilla);

        // Función auxiliar para escapar caracteres HTML por seguridad
        function escaparHtml(texto) {
            if (!texto) return '';
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(texto).replace(/[&<>"']/g, m => map[m]);
        }

        // Evento de clic sobre las celdas de asistencia registradas
        const tablaPlanillaWeb = document.getElementById('tabla-planilla-web');
        if (tablaPlanillaWeb) {
            tablaPlanillaWeb.addEventListener('click', function(e) {
                const celdaTd = e.target.closest('.celda-interactiva');
                if (!celdaTd) return;

                const estIdx = parseInt(celdaTd.getAttribute('data-est-idx'), 10);
                const colIdx = parseInt(celdaTd.getAttribute('data-col-idx'), 10);

                const estudiante = PLANILLA.estudiantes?.[estIdx];
                const columna = PLANILLA.columnas?.[colIdx];
                const celda = estudiante?.asistencias?.[colIdx];

                if (!estudiante || !columna || !celda || !celda.registrado) return;

                // Configuración visual según el tipo de asistencia
                const configTipo = {
                    'PRESENTE': {
                        badge: 'bg-success',
                        icono: 'fa-check-circle',
                        texto: 'PRESENTE (.)'
                    },
                    'FALTA': {
                        badge: 'bg-danger',
                        icono: 'fa-times-circle',
                        texto: 'FALTA (F)'
                    },
                    'ATRASO': {
                        badge: 'bg-warning text-dark',
                        icono: 'fa-hourglass-half',
                        texto: 'ATRASO (A)'
                    },
                    'LICENCIA': {
                        badge: 'bg-info text-dark',
                        icono: 'fa-file-certificate',
                        texto: 'LICENCIA (L)'
                    },
                    'PERMISO': {
                        badge: 'bg-primary',
                        icono: 'fa-file-signature',
                        texto: 'PERMISO (P)'
                    },
                    'OTROS': {
                        badge: 'bg-secondary',
                        icono: 'fa-calendar-star',
                        texto: 'OTROS (O)'
                    }
                } [celda.tipo] || {
                    badge: 'bg-secondary',
                    icono: 'fa-question-circle',
                    texto: celda.tipo
                };

                const rangoHoras = columna.hora_inicio && columna.hora_fin ?
                    ` (${columna.hora_inicio} - ${columna.hora_fin})` :
                    (columna.hora_inicio ? ` (${columna.hora_inicio})` : '');

                const periodoHorario = columna.denominacion_horario ?
                    `${escaparHtml(columna.denominacion_horario)}${rangoHoras}` :
                    'N/A';

                // Construcción de subdatos condicionales según el tipo de asistencia
                let subdatosHtml = '';

                if (celda.tipo === 'ATRASO') {
                    const minutos = celda.tiempo_atraso > 0 ?
                        `${celda.tiempo_atraso} minutos` :
                        'Sin minutos especificados';
                    subdatosHtml = `
                        <div class="alert alert-warning mb-0 mt-3 py-2 px-3 text-start shadow-sm">
                            <div class="fw-bold mb-1">
                                <i class="fa-solid fa-duotone fa-hourglass-half me-1"></i>Detalle del Atraso:
                            </div>
                            <div><b>Tiempo de atraso:</b> ${minutos}</div>
                        </div>`;
                } else if (celda.tipo === 'PERMISO') {
                    const motivoPermiso = celda.justificacion_permiso ?
                        escaparHtml(celda.justificacion_permiso) :
                        'Sin justificación registrada';
                    subdatosHtml = `
                        <div class="alert alert-primary mb-0 mt-3 py-2 px-3 text-start shadow-sm">
                            <div class="fw-bold mb-1">
                                <i class="fa-solid fa-duotone fa-file-signature me-1"></i>Detalle del Permiso:
                            </div>
                            <div><b>Justificación:</b> ${motivoPermiso}</div>
                        </div>`;
                } else if (celda.tipo === 'LICENCIA') {
                    if (celda.licencia) {
                        const btnEvidencia = celda.licencia.evidencia ?
                            `<div class="mt-2 pt-2 border-top border-info-subtle text-end">
                                <a href="${escaparHtml(celda.licencia.evidencia)}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-info text-dark fw-bold shadow-sm">
                                    <i class="fa-brands fa-google-drive me-1"></i>Ver Evidencia
                                </a>
                            </div>` :
                            `<div class="mt-1 small text-muted fst-italic">Sin evidencia adjunta</div>`;

                        subdatosHtml = `
                            <div class="alert alert-info mb-0 mt-3 py-2 px-3 text-start shadow-sm">
                                <div class="fw-bold mb-1">
                                    <i class="fa-solid fa-duotone fa-file-certificate me-1"></i>Detalles de la Licencia:
                                </div>
                                <div class="mb-1"><b>Motivo:</b> ${escaparHtml(celda.licencia.tipo)}</div>
                                <div class="mb-1"><b>Justificación:</b> ${escaparHtml(celda.licencia.justificacion)}</div>
                                <div class="mb-1">
                                    <span class="badge bg-primary me-1">Desde: ${escaparHtml(celda.licencia.fecha_inicio)}</span>
                                    <span class="badge bg-danger">Hasta: ${escaparHtml(celda.licencia.fecha_fin)}</span>
                                </div>
                                ${btnEvidencia}
                            </div>`;
                    } else {
                        subdatosHtml = `
                            <div class="alert alert-info mb-0 mt-3 py-2 px-3 text-start shadow-sm">
                                <i class="fa-solid fa-duotone fa-circle-info me-1"></i>Licencia registrada sin detalles adicionales vinculados.
                            </div>`;
                    }
                } else if (celda.tipo === 'OTROS' && columna.justificacion_otros) {
                    subdatosHtml = `
                        <div class="alert alert-secondary mb-0 mt-3 py-2 px-3 text-start shadow-sm">
                            <div class="fw-bold mb-1">
                                <i class="fa-solid fa-duotone fa-calendar-star me-1"></i>Modalidad OTROS:
                            </div>
                            <div><b>Actividad:</b> ${escaparHtml(columna.justificacion_otros)}</div>
                        </div>`;
                }

                const badgeRetirado = !estudiante.en_lista_actual ?
                    `<span class="badge bg-secondary ms-1">RETIRADO</span>` :
                    '';

                const contenidoSwal = `
                    <div class="text-start fs-6">
                        <div class="p-3 rounded border bg-body-tertiary mb-3 shadow-sm">
                            <div class="mb-2">
                                <span class="text-muted small fw-bold d-block">ESTUDIANTE:</span>
                                <span class="fw-bold text-info">${escaparHtml(estudiante.apellidos_nombres)}</span>
                                ${badgeRetirado}
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <span class="text-muted small fw-bold d-block">FECHA:</span>
                                    <span class="fw-semibold">${escaparHtml(columna.fecha_completa)}</span>
                                </div>
                                <div class="col-6">
                                    <span class="text-muted small fw-bold d-block">DÍA:</span>
                                    <span class="fw-semibold">${escaparHtml(columna.dia_semana)}</span>
                                </div>
                                <div class="col-12 mt-2">
                                    <span class="text-muted small fw-bold d-block">PERIODO / HORARIO:</span>
                                    <span class="fw-semibold">${periodoHorario}</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center px-1">
                            <span class="fw-bold text-muted">TIPO DE ASISTENCIA:</span>
                            <span class="badge ${configTipo.badge} px-3 py-2 fs-6 shadow-sm">
                                <i class="fa-solid fa-duotone ${configTipo.icono} me-1"></i>${configTipo.texto}
                            </span>
                        </div>

                        ${subdatosHtml}
                    </div>
                `;

                Swal.fire({
                    theme: localStorage.getItem('theme') || 'dark',
                    title: '<span class="fs-5 fw-bold text-info"><i class="fa-solid fa-duotone fa-clipboard-user me-2"></i>Detalle de Registro</span>',
                    html: contenidoSwal,
                    confirmButtonText: '<i class="fa-solid fa-check me-1"></i>Cerrar',
                    confirmButtonColor: '#0d6efd',
                });
            });
        }

        /**
         * Convierte el elemento <img> del logo institucional a Base64 PNG
         * para incrustarlo directamente a la izquierda del encabezado en ExcelJS.
         */
        function obtenerLogoBase64() {
            return new Promise((resolve) => {
                const imgElement = document.getElementById('logo-institucional-img');
                if (!imgElement || !imgElement.complete || imgElement.naturalWidth === 0) {
                    resolve(null);
                    return;
                }
                try {
                    const canvas = document.createElement('canvas');
                    canvas.width = imgElement.naturalWidth;
                    canvas.height = imgElement.naturalHeight;
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(imgElement, 0, 0);
                    resolve(canvas.toDataURL('image/png'));
                } catch (e) {
                    resolve(null);
                }
            });
        }

        // 1. Botón de Impresión Nativa / Guardar PDF (aislando #planilla-asistencia-card vía @media print)
        // Forzar tema claro ("light") temporalmente durante cualquier impresión (Botón o Ctrl + P)
        let temaHtmlPrevio = null;
        let temaBodyPrevio = null;

        window.addEventListener('beforeprint', function() {
            const htmlEl = document.documentElement;
            const bodyEl = document.body;

            temaHtmlPrevio = htmlEl.getAttribute('data-bs-theme');
            temaBodyPrevio = bodyEl.getAttribute('data-bs-theme');

            htmlEl.setAttribute('data-bs-theme', 'light');
            if (temaBodyPrevio) {
                bodyEl.setAttribute('data-bs-theme', 'light');
            }
        });

        window.addEventListener('afterprint', function() {
            const htmlEl = document.documentElement;
            const bodyEl = document.body;

            if (temaHtmlPrevio !== null) {
                htmlEl.setAttribute('data-bs-theme', temaHtmlPrevio);
            } else {
                htmlEl.removeAttribute('data-bs-theme');
            }

            if (temaBodyPrevio !== null) {
                bodyEl.setAttribute('data-bs-theme', temaBodyPrevio);
            }
        });

        const btnImprimir = document.getElementById('btn-imprimir');
        if (btnImprimir) {
            btnImprimir.addEventListener('click', function() {
                window.print();
            });
        }

        // 2. Botón de Exportación a Excel (.xlsx) con ExcelJS
        const btnExcel = document.getElementById('btn-exportar-excel');
        if (btnExcel) {
            btnExcel.addEventListener('click', async function() {
                if (!PLANILLA.columnas || PLANILLA.columnas.length === 0) return;

                const htmlOriginal = btnExcel.innerHTML;
                btnExcel.disabled = true;
                btnExcel.innerHTML =
                    '<i class="fa-solid fa-spinner fa-spin me-1"></i> Generando Excel...';

                try {
                    const workbook = new ExcelJS.Workbook();
                    workbook.creator = 'Plataforma Froebel';
                    workbook.created = new Date();

                    const nombreHoja = (`ASIST. ${PLANILLA.encabezado.periodo}`).substring(0, 31);
                    const sheet = workbook.addWorksheet(nombreHoja, {
                        pageSetup: {
                            paperSize: 9, // A4
                            orientation: 'landscape',
                            fitToPage: true,
                            fitToWidth: 1,
                            fitToHeight: 0
                        }
                    });

                    const totalClases = PLANILLA.columnas.length;
                    const colInicioClases = 3; // Columna C (1=A: N°, 2=B: Nombre Completo)
                    const colFinClases = colInicioClases + totalClases - 1;
                    const colInicioRecuento = colFinClases + 1;
                    const colFinTabla = colInicioRecuento + 5; // 6 columnas de recuento

                    // ================================================================
                    // A. ENCABEZADO INSTITUCIONAL CON LOGO A LA IZQUIERDA (Filas 1 a 4)
                    // ================================================================
                    const logoBase64 = await obtenerLogoBase64();
                    if (logoBase64) {
                        const logoId = workbook.addImage({
                            base64: logoBase64,
                            extension: 'png',
                        });
                        sheet.addImage(logoId, {
                            tl: {
                                col: 0.15,
                                row: 0.15
                            },
                            ext: {
                                width: 62,
                                height: 62
                            }
                        });
                    }

                    // Dejamos la Columna 1 para el logo y centramos/distribuimos los textos desde la Columna 2
                    sheet.mergeCells(1, 2, 1, colFinTabla);
                    const cellTitulo = sheet.getCell(1, 2);
                    cellTitulo.value = 'PLANILLA DE ASISTENCIA';
                    cellTitulo.font = {
                        name: 'Arial',
                        size: 12,
                        bold: true
                    };
                    cellTitulo.alignment = {
                        horizontal: 'center',
                        vertical: 'middle'
                    };

                    const colMitad = Math.max(4, Math.floor(colFinTabla / 2));

                    // Fila 2: Unidad Educativa | Gestión y Periodo
                    sheet.mergeCells(2, 2, 2, colMitad);
                    sheet.getCell(2, 2).value =
                        `UNIDAD EDUCATIVA: ${PLANILLA.encabezado.unidad_educativa}`;
                    sheet.getCell(2, 2).font = {
                        name: 'Arial',
                        size: 9,
                        bold: true
                    };

                    sheet.mergeCells(2, colMitad + 1, 2, colFinTabla);
                    sheet.getCell(2, colMitad + 1).value =
                        `GESTIÓN: ${PLANILLA.encabezado.gestion_periodo}`;
                    sheet.getCell(2, colMitad + 1).font = {
                        name: 'Arial',
                        size: 9,
                        bold: true
                    };

                    // Fila 3: Docente | Área
                    sheet.mergeCells(3, 2, 3, colMitad);
                    sheet.getCell(3, 2).value = `DOCENTE: ${PLANILLA.encabezado.docente}`;
                    sheet.getCell(3, 2).font = {
                        name: 'Arial',
                        size: 9,
                        bold: true
                    };

                    sheet.mergeCells(3, colMitad + 1, 3, colFinTabla);
                    sheet.getCell(3, colMitad + 1).value = `ÁREA: ${PLANILLA.encabezado.area}`;
                    sheet.getCell(3, colMitad + 1).font = {
                        name: 'Arial',
                        size: 9,
                        bold: true
                    };

                    // Fila 4: Campo | Curso (Asignatura)
                    sheet.mergeCells(4, 2, 4, colMitad);
                    sheet.getCell(4, 2).value = `CAMPO: ${PLANILLA.encabezado.campo}`;
                    sheet.getCell(4, 2).font = {
                        name: 'Arial',
                        size: 9,
                        bold: true
                    };

                    sheet.mergeCells(4, colMitad + 1, 4, colFinTabla);
                    sheet.getCell(4, colMitad + 1).value = `CURSO: ${PLANILLA.encabezado.curso}`;
                    sheet.getCell(4, colMitad + 1).font = {
                        name: 'Arial',
                        size: 9,
                        bold: true
                    };

                    // ================================================================
                    // B. ENCABEZADOS DE LA TABLA MATRICIAL (Filas 6, 7 y 8)
                    // ================================================================
                    const filaMes = 6;
                    const filaDia = 7;
                    const filaFecha = 8;

                    sheet.mergeCells(filaMes, 1, filaMes, 2);
                    sheet.getCell(filaMes, 1).value = 'MES';
                    sheet.getCell(filaMes, 1).alignment = {
                        horizontal: 'right',
                        vertical: 'middle'
                    };
                    sheet.getCell(filaMes, 1).font = {
                        name: 'Arial',
                        size: 8,
                        bold: true
                    };

                    sheet.mergeCells(filaDia, 1, filaDia, 2);
                    sheet.getCell(filaDia, 1).value = 'DÍA';
                    sheet.getCell(filaDia, 1).alignment = {
                        horizontal: 'right',
                        vertical: 'middle'
                    };
                    sheet.getCell(filaDia, 1).font = {
                        name: 'Arial',
                        size: 8,
                        bold: true
                    };

                    sheet.getCell(filaFecha, 1).value = 'N°';
                    sheet.getCell(filaFecha, 1).alignment = {
                        horizontal: 'center',
                        vertical: 'middle'
                    };
                    sheet.getCell(filaFecha, 1).font = {
                        name: 'Arial',
                        size: 8,
                        bold: true
                    };

                    sheet.getCell(filaFecha, 2).value = 'NOMBRE COMPLETO';
                    sheet.getCell(filaFecha, 2).alignment = {
                        horizontal: 'left',
                        vertical: 'middle'
                    };
                    sheet.getCell(filaFecha, 2).font = {
                        name: 'Arial',
                        size: 8,
                        bold: true
                    };

                    // 1. Nivel Meses
                    let cursorCol = colInicioClases;
                    PLANILLA.meses.forEach(grupo => {
                        const finGrupo = cursorCol + grupo.colspan - 1;
                        if (finGrupo > cursorCol) {
                            sheet.mergeCells(filaMes, cursorCol, filaMes, finGrupo);
                        }
                        const cellMes = sheet.getCell(filaMes, cursorCol);
                        cellMes.value = grupo.mes;
                        cellMes.font = {
                            name: 'Arial',
                            size: 8,
                            bold: true
                        };
                        cellMes.alignment = {
                            horizontal: 'center',
                            vertical: 'middle'
                        };
                        cellMes.fill = {
                            type: 'pattern',
                            pattern: 'solid',
                            fgColor: {
                                argb: 'FFEFEFEF'
                            }
                        };
                        cursorCol = finGrupo + 1;
                    });

                    // Encabezado RECUENTO combinado en las 6 columnas finales
                    sheet.mergeCells(filaMes, colInicioRecuento, filaMes, colFinTabla);
                    const cellRecuento = sheet.getCell(filaMes, colInicioRecuento);
                    cellRecuento.value = 'RECUENTO';
                    cellRecuento.font = {
                        name: 'Arial',
                        size: 8,
                        bold: true
                    };
                    cellRecuento.alignment = {
                        horizontal: 'center',
                        vertical: 'middle'
                    };
                    cellRecuento.fill = {
                        type: 'pattern',
                        pattern: 'solid',
                        fgColor: {
                            argb: 'FFEFEFEF'
                        }
                    };

                    // 2. Nivel Días Verticales (Fila 7) y Fechas Numéricas (Fila 8)
                    PLANILLA.columnas.forEach((col, idx) => {
                        const colActual = colInicioClases + idx;

                        const cDia = sheet.getCell(filaDia, colActual);
                        cDia.value = col.etiqueta_dia;
                        cDia.font = {
                            name: 'Arial',
                            size: 7.5,
                            bold: true
                        };
                        cDia.alignment = {
                            horizontal: 'center',
                            vertical: 'middle',
                            textRotation: 90
                        };

                        const cFecha = sheet.getCell(filaFecha, colActual);
                        cFecha.value = col.dia_numero;
                        cFecha.font = {
                            name: 'Arial',
                            size: 8,
                            bold: true
                        };
                        cFecha.alignment = {
                            horizontal: 'center',
                            vertical: 'middle'
                        };
                    });

                    // 3. Columnas de los 6 Recuentos
                    const recuentosConfig = [{
                            titulo: 'PRESENTES',
                            sigla: '.'
                        },
                        {
                            titulo: 'FALTAS',
                            sigla: 'F'
                        },
                        {
                            titulo: 'ATRASOS',
                            sigla: 'A'
                        },
                        {
                            titulo: 'LICENCIAS',
                            sigla: 'L'
                        },
                        {
                            titulo: 'PERMISOS',
                            sigla: 'P'
                        },
                        {
                            titulo: 'OTROS',
                            sigla: 'O'
                        }
                    ];

                    recuentosConfig.forEach((rec, idx) => {
                        const colActual = colInicioRecuento + idx;

                        const cTit = sheet.getCell(filaDia, colActual);
                        cTit.value = rec.titulo;
                        cTit.font = {
                            name: 'Arial',
                            size: 7.5,
                            bold: true
                        };
                        cTit.alignment = {
                            horizontal: 'center',
                            vertical: 'middle',
                            textRotation: 90
                        };

                        const cSigla = sheet.getCell(filaFecha, colActual);
                        cSigla.value = rec.sigla;
                        cSigla.font = {
                            name: 'Arial',
                            size: 8,
                            bold: true
                        };
                        cSigla.alignment = {
                            horizontal: 'center',
                            vertical: 'middle'
                        };
                    });

                    sheet.getRow(filaDia).height = 180;
                    sheet.getRow(filaFecha).height = 29;

                    // ================================================================
                    // C. FILAS DE ESTUDIANTES (Activos + Retirados en gris)
                    // ================================================================
                    let filaActual = filaFecha + 1;

                    PLANILLA.estudiantes.forEach(est => {
                        const row = sheet.getRow(filaActual);
                        row.height = 29;

                        row.getCell(1).value = est.numero;
                        row.getCell(1).alignment = {
                            horizontal: 'center',
                            vertical: 'middle'
                        };
                        row.getCell(1).font = {
                            name: 'Arial',
                            size: 8
                        };

                        row.getCell(2).value = est.en_lista_actual ?
                            est.apellidos_nombres :
                            `${est.apellidos_nombres} (RETIRADO)`;
                        row.getCell(2).alignment = {
                            horizontal: 'left',
                            vertical: 'middle'
                        };
                        row.getCell(2).font = {
                            name: 'Arial',
                            size: 8,
                            bold: !est.en_lista_actual
                        };

                        est.asistencias.forEach((celda, idx) => {
                            const colPos = colInicioClases + idx;
                            const cAsis = row.getCell(colPos);
                            cAsis.value = celda.simbolo;
                            cAsis.alignment = {
                                horizontal: 'center',
                                vertical: 'middle'
                            };
                            cAsis.font = {
                                name: 'Arial',
                                size: 8,
                                bold: celda.simbolo !== '.'
                            };
                        });

                        const valoresTotales = [
                            est.totales.PRESENTE,
                            est.totales.FALTA,
                            est.totales.ATRASO,
                            est.totales.LICENCIA,
                            est.totales.PERMISO,
                            est.totales.OTROS
                        ];

                        valoresTotales.forEach((val, idx) => {
                            const colPos = colInicioRecuento + idx;
                            const cTot = row.getCell(colPos);
                            cTot.value = val;
                            cTot.alignment = {
                                horizontal: 'center',
                                vertical: 'middle'
                            };
                            cTot.font = {
                                name: 'Arial',
                                size: 8,
                                bold: true
                            };
                        });

                        if (!est.en_lista_actual) {
                            for (let c = 1; c <= colFinTabla; c++) {
                                row.getCell(c).fill = {
                                    type: 'pattern',
                                    pattern: 'solid',
                                    fgColor: {
                                        argb: 'FFD9D9D9'
                                    }
                                };
                            }
                        }

                        filaActual++;
                    });

                    // ================================================================
                    // D. BORDES Y ANCHOS DE COLUMNA
                    // ================================================================
                    const bordeFino = {
                        top: {
                            style: 'thin',
                            color: {
                                argb: 'FF000000'
                            }
                        },
                        left: {
                            style: 'thin',
                            color: {
                                argb: 'FF000000'
                            }
                        },
                        bottom: {
                            style: 'thin',
                            color: {
                                argb: 'FF000000'
                            }
                        },
                        right: {
                            style: 'thin',
                            color: {
                                argb: 'FF000000'
                            }
                        }
                    };

                    for (let r = filaMes; r < filaActual; r++) {
                        const row = sheet.getRow(r);
                        for (let c = 1; c <= colFinTabla; c++) {
                            row.getCell(c).border = bordeFino;
                        }
                    }

                    sheet.getColumn(1).width =
                        9; // Espacio suficiente para N° y el logo en la esquina superior
                    sheet.getColumn(2).width = 36; // Nombre Completo
                    for (let c = colInicioClases; c <= colFinClases; c++) {
                        sheet.getColumn(c).width = 3.8;
                    }
                    for (let c = colInicioRecuento; c <= colFinTabla; c++) {
                        sheet.getColumn(c).width = 4.5;
                    }

                    const buffer = await workbook.xlsx.writeBuffer();
                    const blob = new Blob([buffer], {
                        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                    });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    const nombreLimpio = (
                            `Planilla_Asistencia_${PLANILLA.encabezado.curso}_${PLANILLA.encabezado.gestion_periodo}`
                        )
                        .replace(/[^a-zA-Z0-9_\-]/g, '_');
                    a.href = url;
                    a.download = `${nombreLimpio}.xlsx`;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    window.URL.revokeObjectURL(url);

                } catch (error) {
                    console.error('Error al generar Excel:', error);
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        icon: 'error',
                        title: 'Error al exportar',
                        text: 'No se pudo generar el archivo Excel. Inténtalo nuevamente.'
                    });
                } finally {
                    btnExcel.disabled = false;
                    btnExcel.innerHTML = htmlOriginal;
                }
            });
        }
    });
</script>
