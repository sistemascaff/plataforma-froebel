<script>
    $(document).ready(function() {
        let dtEstudiantes = $("#estudiantes").DataTable({
            @include('components.datatables.datatables_global_properties')
            @include('components.datatables.datatables_language_property'),
            columnDefs: [{
                    orderable: false,
                    targets: [1, 9]
                }, // Deshabilita orden en Foto y Acciones
                {
                    searchable: false,
                    targets: [0, 1, 9]
                } // Deshabilita búsqueda en #, Foto y Acciones
            ]
        });

        // Reinicializar tooltips (Bootstrap 5) al redibujar la tabla
        dtEstudiantes.on('draw', function() {
            $('[data-bs-toggle="tooltip"]').tooltip();
        });

        // ─── Utilidad: Toast para notificaciones rápidas ────────────────────────────
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true,
            theme: localStorage.getItem('theme') || 'dark'
        });

        // ─── Funciones para copiar al portapapeles ──────────────────────────────────
        $('#estudiantes tbody').on('click', '.btn-copiar-nombre', function() {
            let texto = $(this).data('nombre');
            navigator.clipboard.writeText(texto).then(() => {
                Toast.fire({
                    icon: 'success',
                    title: 'Nombre copiado'
                });
            });
        });

        $('#estudiantes tbody').on('click', '.btn-copiar-correo', function() {
            let texto = $(this).data('correo');
            navigator.clipboard.writeText(texto).then(() => {
                Toast.fire({
                    icon: 'success',
                    title: 'Correo copiado'
                });
            });
        });
    });
</script>
