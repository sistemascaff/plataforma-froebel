{{-- Librería ExcelJS para construir el archivo .xlsx con estilos, logo, rotación a 90° y celdas combinadas --}}
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.4.0/dist/exceljs.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const PLANILLA = @json($planilla);

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
