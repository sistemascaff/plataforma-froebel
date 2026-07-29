<!-- Modal para crear y editar libros -->
<div class="modal fade" id="modal-formulario" tabindex="-1" aria-labelledby="modal-formulario-titulo" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal-formulario-titulo"><i class="fa-solid fa-duotone fa-plus"></i>
                    CREAR GESTIÓN</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-crear-o-editar">
                    <!-- input de id_gestion en caso de editar -->
                    <input type="hidden" name="id_gestion" value="0">

                    <div class="mb-3">
                        <label for="anio" class="form-label">Año <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="anio" name="anio" min="2000"
                            max="2050" step="1" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                        class="fa-solid fa-duotone fa-close me-1"></i>Cerrar</button>
                <button type="submit" id="btn-guardar" form="form-crear-o-editar" class="btn btn-primary">
                    <i class="fa-solid fa-duotone fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>
