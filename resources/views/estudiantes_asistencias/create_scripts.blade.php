<script>
    document.addEventListener('DOMContentLoaded', function() {

        // Función auxiliar para actualizar el badge y el campo de atraso según el radio seleccionado
        function actualizarEstadoVisualFila(index, valor) {
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
                    inputAtraso.focus();
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
                actualizarEstadoVisualFila(index, this.value);
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

                        // Restaurar el badge y estado según el radio que había quedado marcado
                        const radioSeleccionado = document.querySelector(
                            `.radio-asistencia[data-index="${index}"]:checked`);
                        if (radioSeleccionado) {
                            actualizarEstadoVisualFila(index, radioSeleccionado.value);
                        }
                    }
                });
            });
        }

        // --- 3. Lógica AJAX y SweetAlert2 para el Envío del Formulario ---
        const formAsistencia = document.getElementById('form-asistencia');

        if (formAsistencia) {
            formAsistencia.addEventListener('submit', function(e) {
                e.preventDefault(); // Evitamos la recarga tradicional

                // ====================================================================
                // VALIDACIÓN 1: Cruce de Fecha vs Día del Horario
                // ====================================================================
                const inputFecha = document.getElementById('fecha').value;
                const selectHorario = document.getElementById('id_horario_asignatura');
                const opcionSeleccionada = selectHorario.options[selectHorario.selectedIndex];

                // Obtenemos el día esperado de la base de datos
                const diaHorario = parseInt(opcionSeleccionada.getAttribute('data-dia'));

                // Obtenemos el día real de la fecha seleccionada usando Moment.js (1=Lunes, 7=Domingo)
                const diaFecha = moment(inputFecha).isoWeekday();

                if (diaFecha !== diaHorario) {
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        icon: 'error',
                        title: 'Discrepancia de Horario',
                        text: 'El día de la fecha seleccionada no coincide con el día de la semana del horario elegido.',
                        confirmButtonColor: '#d33'
                    });
                    return; // Detenemos la ejecución de todo el script aquí mismo
                }

                // ====================================================================
                // VALIDACIÓN 2: Justificación obligatoria cuando Modo "OTROS" está activo
                // ====================================================================
                if (switchModoOtros && switchModoOtros.checked && !selectJustificacion.value) {
                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'dark',
                        icon: 'warning',
                        title: 'Justificación Requerida',
                        text: 'Debe seleccionar el motivo (ACTIVIDAD CURRICULAR o ACTIVIDAD EXTRACURRICULAR) para registrar la asistencia como OTROS.',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }
                // ====================================================================

                // Deshabilitar botón para evitar envíos múltiples
                const btnSubmit = document.getElementById('btn-guardar');
                const originalText = btnSubmit.innerHTML;
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = 'Guardando... <i class="fas fa-spinner fa-spin"></i>';

                // Recolectar automáticamente todos los datos del formulario 
                const formData = new FormData(formAsistencia);

                // Solicitud AJAX asíncrona
                fetch("{{ route('estudiantes_asistencias.create') }}", {
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
                            // Éxito
                            Swal.fire({
                                theme: localStorage.getItem('theme') || 'dark',
                                icon: 'success',
                                title: '¡Asistencia Registrada!',
                                text: res.body.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                const idAsistencia = res.body.data.id_estudiante_asistencia;
                                // Redirección utilizando helper URL de Laravel
                                window.location.href =
                                    `{{ route('estudiantes_asistencias.detalles', ['estudiante_asistencia' => ':id']) }}`
                                    .replace(':id', idAsistencia);
                            });
                        } else {
                            // Errores de Validación o Duplicidad (Ej: ya se tomó lista ese día)
                            let errorMessage = res.body.message ||
                                'Verifica los datos e intenta nuevamente.';

                            if (res.status === 422 && res.body.errors) {
                                const errores = Object.values(res.body.errors).flat().join('<br>');
                                errorMessage += '<br><br><strong>Detalles:</strong><br>' + errores;
                            }

                            Swal.fire({
                                theme: localStorage.getItem('theme') || 'dark',
                                icon: 'warning',
                                title: 'No se pudo guardar',
                                html: errorMessage,
                                confirmButtonColor: '#3085d6'
                            });

                            // Restaurar el botón para que puedan intentar de nuevo
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

        // --- 4. Validación en tiempo real para el Tiempo de Atraso ---
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

                // Evaluar si el campo no está vacío y está fuera del rango 1-60
                if (this.value !== '' && (valor < 1 || valor > 60)) {
                    Toast.fire({
                        icon: 'warning',
                        title: 'Tiempo inválido',
                        text: 'El atraso debe estar entre 1 y 60 minutos.'
                    });

                    // Ajustar automáticamente al límite más cercano para ayudar al usuario
                    this.value = valor > 60 ? 60 : 1;
                }
            });
        });
    });
</script>
