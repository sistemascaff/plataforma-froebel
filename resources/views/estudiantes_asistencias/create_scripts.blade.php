<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- 1. Lógica visual para los Badges de Estado y Tiempo de Atraso ---
        document.querySelectorAll('.radio-asistencia').forEach(radio => {
            radio.addEventListener('change', function() {
                const index = this.dataset.index;
                const badge = document.getElementById('badge_tipo_' + index);
                const inputAtraso = document.getElementById('tiempo_atraso_' + index);

                if (this.value === 'P') {
                    badge.className = 'badge bg-success tipo';
                    badge.textContent = 'Presente';
                    if (inputAtraso) {
                        inputAtraso.style.display = 'none';
                        inputAtraso.required = false;
                        inputAtraso.value = ''; // Limpiamos el valor por seguridad
                    }
                } else if (this.value === 'A') {
                    badge.className = 'badge bg-warning tipo';
                    badge.textContent = 'Atraso';
                    if (inputAtraso) {
                        inputAtraso.style.display = 'block';
                        inputAtraso.required = true; // Se vuelve obligatorio
                        inputAtraso.focus(); // Llevamos el cursor al input
                    }
                } else if (this.value === 'F') {
                    badge.className = 'badge bg-danger tipo';
                    badge.textContent = 'Falta';
                    if (inputAtraso) {
                        inputAtraso.style.display = 'none';
                        inputAtraso.required = false;
                        inputAtraso.value = '';
                    }
                }
            });
        });

        // --- 2. Lógica AJAX y SweetAlert2 para el Envío del Formulario ---
        const formAsistencia = document.getElementById('form-asistencia');

        if (formAsistencia) {
            formAsistencia.addEventListener('submit', function(e) {
                e.preventDefault(); // Evitamos la recarga tradicional

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

        // --- 3. Validación en tiempo real para el Tiempo de Atraso ---
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            // Soporte para modo oscuro/claro basado en tu localStorage
            background: localStorage.getItem('theme') === 'dark' ? '#343a40' : '#fff',
            color: localStorage.getItem('theme') === 'dark' ? '#fff' : '#545454',
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        document.querySelectorAll('input[type="number"]').forEach(input => {
            input.addEventListener('change', function() {
                let valor = parseInt(this.value);

                // Evaluar si el campo no está vacío y está fuera del rango 1-60
                if (this.value !== '' && (valor < 1 || valor > 60)) {
                    // Mostrar el Toast de error
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
