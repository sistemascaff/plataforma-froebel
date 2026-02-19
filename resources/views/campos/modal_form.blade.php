<!-- Modal para crear y editar libros -->
<div class="modal fade" id="modal-formulario" tabindex="-1" aria-labelledby="modal-formulario-titulo" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal-formulario-titulo"><i class="fa-solid fa-duotone fa-plus"></i>
                    CREAR CAMPO</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-crear-o-editar">
                    <!-- input de id_campo en caso de editar -->
                    <input type="hidden" name="id_campo" value="0">

                    <div class="mb-3">
                        <label for="campo" class="form-label">Campo <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="campo" name="campo" min="3" max="45" required>
                    </div>

                    <div class="mb-3">
                        <label for="posicion_ordinal" class="form-label">Posición ordinal <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="posicion_ordinal" name="posicion_ordinal" step="1" min="1" max="5" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                class="fa-solid fa-duotone fa-close"></i>Cerrar</button>
                        <button type="button" id="btn-guardar" class="btn btn-primary"><i
                                class="fa-solid fa-duotone fa-save"></i>
                            Guardar</button>
                    </div>
            </div>
        </div>
    </div>
