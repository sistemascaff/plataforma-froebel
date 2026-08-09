<script>
    document.addEventListener('DOMContentLoaded', function() {

        // --- 1. Lógica visual para los Badges de Estado en tiempo real ---
        document.querySelectorAll('.radio-asistencia').forEach(radio => {
            radio.addEventListener('change', function() {
                const index = this.dataset.index;
                const badge = document.getElementById('badge_tipo_' + index);

                if (this.value === 'P') {
                    badge.className = 'badge bg-success tipo';
                    badge.textContent = 'Presente';
                } else if (this.value === 'A') {
                    badge.className = 'badge bg-warning tipo';
                    badge.textContent = 'Atraso';
                } else if (this.value === 'F') {
                    badge.className = 'badge bg-danger tipo';
                    badge.textContent = 'Falta';
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
                                icon: 'success',
                                title: '¡Asistencia Registrada!',
                                text: res.body.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                const idAsistencia = res.body.data.id_estudiante_asistencia;
                                // Redirección utilizando helper URL de Laravel
                                window.location.href =
                                    `{{ route('estudiantes_asistencias.detalles', ['estudiante_asistencia' => ':id']) }}`.replace(':id', idAsistencia);
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
