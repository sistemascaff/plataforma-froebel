<script>
    $(document).ready(function() {
        const prestamoId = "{{ $prestamo_libro->id_prestamo_libro }}";

        $(document).on('click', '.btn-marcar', function() {
            const $btn = $(this);
            const idLibro = $btn.data('id');
            const accion = $btn.hasClass('btn-success') ? 'devolver' : 'pendiente';

            Swal.fire({
                theme: localStorage.getItem('theme') || 'light',
                title: "Confirmación",
                html: `¿Estás seguro de marcar este libro como <b class="text-primary">${accion}</b>?`,
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Si, confirmar",
                cancelButtonText: "No, cancelar"
            }).then((result) => {
                if (!result.isConfirmed) return;

                Swal.fire({
                    theme: localStorage.getItem('theme') || 'light',
                    title: 'Procesando...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                const url =
                    `{{ route('prestamos_libros.marcar', ['prestamo_libro' => $prestamo_libro->id_prestamo_libro, 'libro' => ':id_libro']) }}`
                    .replace(':id_libro', idLibro);

                $.ajax({
                    url: url,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.close();

                        if (response && response.success) {
                            const type = response.type || '';

                            if (type === 'devuelto') {
                                // Cambiar a estado "pendiente" visualmente
                                $btn.removeClass('btn-success').addClass(
                                    'btn-warning');
                                $btn.attr('title', 'Marcar como pendiente');
                                $btn.find('i').attr('class', 'fa fa-xmark');
                            } else if (type === 'revertido') {
                                // Cambiar a estado "devuelto" visualmente
                                $btn.removeClass('btn-warning').addClass(
                                    'btn-success');
                                $btn.attr('title', 'Marcar como devuelto');
                                $btn.find('i').attr('class', 'fa fa-check');
                            }

                            // Mensaje de éxito
                            Swal.fire({
                                theme: localStorage.getItem('theme') ||
                                    'light',
                                icon: 'success',
                                title: response.message ||
                                    'Operación realizada',
                                showConfirmButton: false,
                                timer: 1400
                            });
                        } else {
                            const msg = (response && response.message) ? response
                                .message : 'Ocurrió un error';
                            Swal.fire({
                                theme: localStorage.getItem('theme') ||
                                    'light',
                                icon: 'error',
                                title: 'Error',
                                html: msg
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.close();
                        let msg = 'Ocurrió un error en la petición.';
                        if (xhr && xhr.responseJSON && xhr.responseJSON.message)
                            msg = xhr.responseJSON.message;
                        Swal.fire({
                            theme: localStorage.getItem('theme') || 'light',
                            icon: 'error',
                            title: 'Error',
                            html: msg
                        });
                    }
                });
            });
        });
    });
</script>
