<!-- Modal para editar docentes de las listas de asignaturas -->
<div class="modal fade" id="modal-formulario" tabindex="-1" aria-labelledby="modal-formulario-titulo" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal-formulario-titulo"><i class="fa-solid fa-duotone fa-plus"></i>
                    EDITAR DOCENTE</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-editar-docente">
                    <div class="mb-3">
                        <label for="docente" class="form-label">Docente <span class="text-danger">*</span></label>
                        <select class="form-select" id="docente" name="docente" required>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                        class="fa-solid fa-duotone fa-close me-1"></i>Cerrar</button>
                <button type="submit" id="btn-guardar-docente" form="form-editar-docente" class="btn btn-primary">
                    Guardar
                </button>
            </div>
        </div>
    </div>
</div>
