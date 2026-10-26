<script>
    document.addEventListener('DOMContentLoaded', function() {
        const idNivelAsignatura = {{ $estudiante_asistencia->lista_asignatura->asignatura->id_nivel }};
        const idHorarioGuardado = {{ $estudiante_asistencia->id_horario_asignatura }};
        const selectHorario = document.getElementById('id_horario_asignatura');
        const switchHorarioNoRegular = document.getElementById('switch_horario_no_regular');
        const contenedorAvisoNoRegular = document.getElementById('contenedor_aviso_no_regular');

        // Respaldo de las opciones de horarios regulares renderizadas desde Blade
        const opcionesHorariosRegularesHTML = selectHorario ? selectHorario.innerHTML : '';
        let horariosNivelCache = null;

        // Función auxiliar para autoseleccionar horario regular según la fecha o el guardado originalmente
        function restaurarHorarioRegular() {
            if (!selectHorario) return;

            selectHorario.innerHTML = opcionesHorariosRegularesHTML;

            // Intentar mantener seleccionado el horario guardado si existe en los regulares y coincide con el día
            const inputFechaVal = document.getElementById('fecha')?.value;
            const diaObjetivo = inputFechaVal ? moment(inputFechaVal).isoWeekday() : null;

            const opcionGuardada = Array.from(selectHorario.options).find(
                op => parseInt(op.value) === idHorarioGuardado && (!diaObjetivo || parseInt(op.getAttribute(
                    'data-dia')) === diaObjetivo)
            );

            if (opcionGuardada) {
                selectHorario.value = opcionGuardada.value;
                return;
            }

            // Si no coincide, sugerir el primer horario regular del día de la fecha seleccionada
            if (diaObjetivo) {
                const opcionPorDia = Array.from(selectHorario.options).find(
                    op => parseInt(op.getAttribute('data-dia')) === diaObjetivo
                );
                if (opcionPorDia) {
                    selectHorario.value = opcionPorDia.value;
                }
            }
        }

        function renderizarOpcionesNoRegulares(horarios, idPreseleccionar = null) {
            selectHorario.innerHTML = '';

            if (horarios.length === 0) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.disabled = true;
                opt.selected = true;
                opt.textContent = 'No hay horarios activos para este nivel';
                selectHorario.appendChild(opt);
                return;
            }

            horarios.forEach(h => {
                const horaInicio = h.hora_inicio ? h.hora_inicio.substring(0, 5) : '';
                const horaFin = h.hora_fin ? h.hora_fin.substring(0, 5) : '';
                const opt = document.createElement('option');
                opt.value = h.id_horario_asignatura;
                opt.textContent = `${h.denominacion} (${horaInicio} - ${horaFin}) - [NO REGULAR]`;

                if (idPreseleccionar && parseInt(h.id_horario_asignatura) === parseInt(
                    idPreseleccionar)) {
                    opt.selected = true;
                }

                selectHorario.appendChild(opt);
            });
        }

        function cargarHorariosNoRegulares(idPreseleccionar = null) {
            if (!selectHorario) return;

            if (contenedorAvisoNoRegular) contenedorAvisoNoRegular.style.display = 'block';

            if (horariosNivelCache !== null) {
                renderizarOpcionesNoRegulares(horariosNivelCache, idPreseleccionar);
                return;
            }

            selectHorario.disabled = true;
            selectHorario.innerHTML = '<option value="">Cargando horarios del nivel...</option>';

            fetch("{{ route('horarios_asignaturas.listar') }}", {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(res => {
                    const lista = res.data || [];

                    // Filtrar horarios activos del mismo nivel y excluir recesos/recreos
                    horariosNivelCache = lista.filter(h => {
                        const desc = (h.denominacion || '').toLowerCase();
                        const esReceso = desc.includes('receso') || desc.includes('recreo');
                        return h.id_nivel == idNivelAsignatura && h.estado == 1 && !esReceso;
                    });

                    // Orden cronológico por hora_inicio
                    horariosNivelCache.sort((a, b) => a.hora_inicio.localeCompare(b.hora_inicio));

                    renderizarOpcionesNoRegulares(horariosNivelCache, idPreseleccionar);
                })
                .catch(err => {
                    console.error('Error al cargar horarios del nivel:', err);
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron cargar los horarios del nivel.'
                    });
                    if (switchHorarioNoRegular) switchHorarioNoRegular.checked = false;
                    if (contenedorAvisoNoRegular) contenedorAvisoNoRegular.style.display = 'none';
                    restaurarHorarioRegular();
                })
                .finally(() => {
                    selectHorario.disabled = false;
                });
        }

        // Si la asistencia ya estaba registrada como "Horario No Regular", cargamos los horarios del nivel al iniciar
        if (switchHorarioNoRegular && switchHorarioNoRegular.checked) {
            cargarHorariosNoRegulares(idHorarioGuardado);
        }

        // Evento al cambiar el switch de Horario No Regular
        if (switchHorarioNoRegular && selectHorario) {
            switchHorarioNoRegular.addEventListener('change', function() {
                if (this.checked) {
                    cargarHorariosNoRegulares(idHorarioGuardado);
                } else {
                    if (contenedorAvisoNoRegular) contenedorAvisoNoRegular.style.display = 'none';
                    restaurarHorarioRegular();
                }
            });
        }

        // Si el usuario cambia la fecha estando en modo regular, sugerir automáticamente el horario correspondiente a ese día
        const inputFechaElem = document.getElementById('fecha');
        if (inputFechaElem) {
            inputFechaElem.addEventListener('change', function() {
                if (!switchHorarioNoRegular || !switchHorarioNoRegular.checked) {
                    const diaObjetivo = moment(this.value).isoWeekday();
                    const opcionPorDia = Array.from(selectHorario.options).find(
                        op => parseInt(op.getAttribute('data-dia')) === diaObjetivo
                    );
                    if (opcionPorDia) {
                        selectHorario.value = opcionPorDia.value;
                    }
                }
            });
        }

        // Función auxiliar para actualizar el badge y el campo de atraso según el radio seleccionado
        function actualizarEstadoVisualFila(index, valor, enfocarInput = false) {
            const badge = document.getElementById('badge_tipo_' + index);
            const inputAtraso = document.getElementById('tiempo_atraso_' + index);

            if (!badge) return;

            if (valor === 'PRESENTE') {
                badge.className = 'badge bg-success tipo';
                badge.textContent = 'Presente';
                if (inputAtraso) {
                    inputAtraso.style.display = 'none';
                    inputAtraso.required = false;
                    inputAtraso.value = '';
                }
            } else if (valor === 'ATRASO') {
                badge.className = 'badge bg-warning text-dark tipo';
                badge.textContent = 'Atraso';
                if (inputAtraso) {
                    inputAtraso.style.display = 'block';
                    inputAtraso.required = true;
                    if (enfocarInput) inputAtraso.focus();
                }
            } else if (valor === 'FALTA') {
                badge.className = 'badge bg-danger tipo';
                badge.textContent = 'Falta';
                if (inputAtraso) {
                    inputAtraso.style.display = 'none';
                    inputAtraso.required = false;
                    inputAtraso.value = '';
                }
            } else if (valor === 'PERMISO') {
                badge.className = 'badge bg-primary tipo';
                badge.textContent = 'Permiso';
                if (inputAtraso) {
                    inputAtraso.style.display = 'none';
                    inputAtraso.required = false;
                    inputAtraso.value = '';
                }
            }
        }

        // --- 1. Lógica visual para los Badges de Estado Individuales y Tiempo de Atraso ---
        document.querySelectorAll('.radio-asistencia').forEach(radio => {
            radio.addEventListener('change', function() {
                const index = this.dataset.index;
                actualizarEstadoVisualFila(index, this.value, true);
            });
        });

        // --- 2. Lógica para el Interruptor Global "OTROS" ---
        const switchModoOtros = document.getElementById('switch_modo_otros');
        const contenedorJustificacion = document.getElementById('contenedor_justificacion_otros');
        const selectJustificacion = document.getElementById('justificacion_otros');

        if (switchModoOtros) {
            switchModoOtros.addEventListener('change', function() {
                const activarOtros = this.checked;

                // Mostrar/ocultar selector de justificación y hacerlo obligatorio si corresponde
                if (activarOtros) {
                    contenedorJustificacion.style.display = 'block';
                    selectJustificacion.required = true;
                } else {
                    contenedorJustificacion.style.display = 'none';
                    selectJustificacion.required = false;
                    selectJustificacion.value = '';
                }

                // Recorrer todos los inputs ocultos de modo OTROS (solo existen en filas SIN licencia)
                document.querySelectorAll('.input-hidden-otros').forEach(inputOtros => {
                    const index = inputOtros.id.replace('input_otros_', '');
                    const badge = document.getElementById('badge_tipo_' + index);
                    const contenedorAcciones = document.getElementById('acciones_fila_' +
                        index);
                    const etiquetaOtros = document.getElementById('etiqueta_otros_' + index);
                    const inputAtraso = document.getElementById('tiempo_atraso_' + index);
                    const radiosFila = document.querySelectorAll(
                        `.radio-asistencia[data-index="${index}"]`);

                    if (activarOtros) {
                        // Habilitar el input hidden con value="OTROS" y deshabilitar los radios individuales
                        inputOtros.disabled = false;
                        radiosFila.forEach(r => r.disabled = true);

                        // Ocultar y limpiar minutos de atraso si estuvieran abiertos
                        if (inputAtraso) {
                            inputAtraso.style.display = 'none';
                            inputAtraso.required = false;
                            inputAtraso.value = '';
                        }

                        // Actualizar el badge a OTROS y ocultar la botonera
                        if (badge) {
                            badge.className = 'badge bg-secondary tipo';
                            badge.textContent = 'Otros';
                        }
                        if (contenedorAcciones) contenedorAcciones.style.setProperty('display',
                            'none', 'important');
                        if (etiquetaOtros) etiquetaOtros.style.display = 'inline-block';
                    } else {
                        // Deshabilitar el input hidden de OTROS y rehabilitar los radios individuales
                        inputOtros.disabled = true;
                        radiosFila.forEach(r => r.disabled = false);

                        if (contenedorAcciones) contenedorAcciones.style.display = 'flex';
                        if (etiquetaOtros) etiquetaOtros.style.display = 'none';

                        // Restaurar el badge y estado según el radio que esté marcado (o marcar PRESENTE por defecto)
                        let radioSeleccionado = document.querySelector(
                            `.radio-asistencia[data-index="${index}"]:checked`);
                        if (!radioSeleccionado) {
                            radioSeleccionado = document.getElementById('presente_' + index);
                            if (radioSeleccionado) radioSeleccionado.checked = true;
                        }
                        if (radioSeleccionado) {
                            actualizarEstadoVisualFila(index, radioSeleccionado.value, false);
                        }
                    }
                });
            });
        }

        // --- 3. Validación en tiempo real para el Tiempo de Atraso ---
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            background: localStorage.getItem('theme') === 'dark' ? '#343a40' : '#fff',
            color: localStorage.getItem('theme') === 'dark' ? '#fff' : '#545454',
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        document.querySelectorAll('.input-tiempo-atraso').forEach(input => {
            input.addEventListener('change', function() {
                let valor = parseInt(this.value);

                if (this.value !== '' && (valor < 1 || valor > 60)) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Tiempo inválido',
                        text: 'El atraso debe estar entre 1 y 60 minutos.'
                    });

                    this.value = valor > 60 ? 60 : 1;
                }
            });
        });

        // --- 4. Lógica AJAX y SweetAlert2 para la Actualización del Formulario ---
        const formAsistencia = document.getElementById('form-asistencia');

        if (formAsistencia) {
            formAsistencia.addEventListener('submit', function(e) {
                e.preventDefault();

                const esNoRegular = switchHorarioNoRegular && switchHorarioNoRegular.checked;

                // ====================================================================
                // VALIDACIÓN 1: Cruce de Fecha vs Día del Horario (Solo si es horario regular)
                // ====================================================================
                if (!esNoRegular) {
                    const inputFecha = document.getElementById('fecha').value;
                    const opcionSeleccionada = selectHorario.options[selectHorario.selectedIndex];

                    if (!opcionSeleccionada) {
                        Swal.fire({
                            theme: localStorage.getItem('theme') || 'dark',
                            icon: 'warning',
                            title: 'Horario requerido',
                            text: 'Seleccione un horario válido para continuar.'
                        });
                        return;
                    }

                    const diaHorario = parseInt(opcionSeleccionada.getAttribute('data-dia'));
                    const diaFecha = moment(inputFecha).isoWeekday();

                    if (diaFecha !== diaHorario) {
                        Swal.fire({
                            theme: localStorage.getItem('theme') || 'dark',
                            icon: 'error',
                            title: 'Discrepancia de Horario',
                            text: 'El día de la fecha seleccionada no coincide con el día de la semana del horario elegido. Si es una clase extraordinaria, active la opción "Horario no regular".',
                            confirmButtonColor: '#d33'
                        });
                        return;
                    }
                }

                // ====================================================================
                // VALIDACIÓN 2: Justificación obligatoria cuando Modo "OTROS" está activo
                // ====================================================================
                if (switchModoOtros && switchModoOtros.checked && !selectJustificacion.value) {
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        icon: 'warning',
                        title: 'Justificación Requerida',
                        text: 'Debe seleccionar el motivo (ACTIVIDAD CURRICULAR o ACTIVIDAD EXTRACURRICULAR) para actualizar la asistencia como OTROS.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                // ====================================================================

                const btnSubmit = document.getElementById('btn-guardar');
                const originalText = btnSubmit.innerHTML;
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Actualizando...';

                const formData = new FormData(formAsistencia);
                formData.append('_method', 'PUT');

                const updateUrl =
                    "{{ route('estudiantes_asistencias.update', $estudiante_asistencia->id_estudiante_asistencia) }}";

                fetch(updateUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json().then(data => ({
                        status: response.status,
                        body: data
                    })))
                    .then(res => {
                        if (res.status === 200 && res.body.success) {
                            Swal.fire({
                                theme: localStorage.getItem('theme') || 'dark',
                                icon: 'success',
                                title: '¡Asistencia Actualizada!',
                                text: res.body.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                const idAsistencia = res.body.data.id_estudiante_asistencia;
                                window.location.href =
                                    `{{ route('estudiantes_asistencias.detalles', ['estudiante_asistencia' => ':id']) }}`
                                    .replace(':id', idAsistencia);
                            });
                        } else {
                            let errorMessage = res.body.message ||
                                'Verifica los datos e intenta nuevamente.';

                            if (res.status === 422 && res.body.errors) {
                                const errores = Object.values(res.body.errors).flat().join('<br>');
                                errorMessage += '<br><br><strong>Detalles:</strong><br>' + errores;
                            }

                            Swal.fire({
                                theme: localStorage.getItem('theme') || 'dark',
                                icon: 'warning',
                                title: 'No se pudo actualizar',
                                html: errorMessage,
                                confirmButtonColor: '#3085d6'
                            });

                            btnSubmit.disabled = false;
                            btnSubmit.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Error Crítico:', error);
                        Swal.fire({
                            theme: localStorage.getItem('theme') || 'dark',
                            icon: 'error',
                            title: 'Error de Servidor',
                            text: 'Ocurrió un problema de red o interno en el servidor.',
                            confirmButtonColor: '#d33'
                        });

                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = originalText;
                    });
            });
        }
    });
</script>
