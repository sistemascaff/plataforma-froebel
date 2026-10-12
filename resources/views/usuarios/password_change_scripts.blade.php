<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Lógica visual para mostrar/ocultar contraseñas ---
        document.querySelectorAll('.btn-toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.previousElementSibling;
                const icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('fa-eye', 'fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('fa-eye-slash', 'fa-eye');
                }
            });
        });

        // --- Lógica para cambiar contraseña ---
        const formContrasenha = document.getElementById('form-cambiar-contrasenha');

        if (formContrasenha) {
            formContrasenha.addEventListener('submit', function(e) {
                e.preventDefault(); // Evitamos la recarga tradicional de la página

                const btnSubmit = document.getElementById('btn-guardar-contrasenha');
                const originalText = btnSubmit.innerHTML;

                // Deshabilitar botón para evitar envíos múltiples
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Actualizando...';

                // Recolectar datos y agregar el método PATCH
                const formData = new FormData(formContrasenha);
                formData.append('_method', 'PATCH');

                fetch("{{ route('password.change') }}", {
                        method: 'POST', // Enviamos por POST para que Laravel lo interprete como PATCH
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
                                title: '¡Actualizada!',
                                text: res.body.message,
                                showConfirmButton: false,
                                timer: 2000
                            });

                            // Limpiar formulario y cerrar el modal nativamente con Bootstrap
                            formContrasenha.reset();
                            const modalInstance = bootstrap.Modal.getInstance(document
                                .getElementById('modal-cambiar-contrasenha'));
                            modalInstance.hide();

                        } else {
                            // Error 400 (Actual incorrecta) o 422 (No coinciden / < 8 caracteres)
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
                    })
                    .finally(() => {
                        // Restauramos el botón sin importar si hubo éxito o error
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = originalText;
                    });
            });
        }
    });
</script>
