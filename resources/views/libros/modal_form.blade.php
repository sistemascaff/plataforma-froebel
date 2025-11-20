<!-- Modal para crear y editar libros -->
<div class="modal fade" id="modal_form" tabindex="-1" aria-labelledby="modal_form_title" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal_form_title"><i class="fa-solid fa-duotone fa-plus"></i>
                    CREAR CLIENTE</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formCreateOrEdit">
                    <!-- input de id_libro en caso de editar -->
                    <input type="hidden" name="id_libro" value="0">

                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="titulo" name="titulo" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="codigo" name="codigo" step="1" required>
                    </div>

                    <div class="mb-3">
                        <label for="autor" class="form-label">Autor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="autor" name="autor" required>
                    </div>

                    <div class="mb-3">
                        <label for="categoria" class="form-label">Categoria <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoria" name="categoria" required>
                    </div>

                    <div class="mb-3">
                        <label for="editorial" class="form-label">Editorial <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editorial" name="editorial" required>
                    </div>

                    <div class="mb-3">
                        <label for="presentacion" class="form-label">Presentacion <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="presentacion" name="presentacion" required>
                    </div>

                    <div class="mb-3">
                        <label for="anio" class="form-label">Año <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="anio" name="anio" min="1901"
                            max="{{ date('Y') }}" step="1" value="{{ date('Y') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="costo" class="form-label">Costo <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="costo" name="costo" required>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                    </div>                    

                    <div class="mb-3">
                        <label for="adquisicion" class="form-label">Adquisición <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="adquisicion" name="adquisicion" required>
                            <option value="1">COMPRA</option>
                            <option value="2">DONACIÓN</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="fecha_ingreso_cooperativa" class="form-label">Fecha de ingreso cooperativa <span
                                class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="fecha_ingreso_cooperativa"
                            name="fecha_ingreso_cooperativa"
                            value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="observacion" class="form-label">Observación</label>
                        <textarea class="form-control" id="observacion" name="observacion" rows="3"></textarea>
                    </div>
                    
                </form>
            </div>
            <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i
                                class="fa-solid fa-duotone fa-close"></i>Cerrar</button>
                        <button type="button" id="btnSave" class="btn btn-primary"><i
                                class="fa-solid fa-duotone fa-save"></i>
                            Guardar</button>
                    </div>
            </div>
        </div>
    </div>
