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
                        <input type="text" class="form-control" id="titulo" name="titulo" list="libro_titulos" required>
                    </div>

                    <datalist id="libro_titulos">
                        @foreach ($libro_titulos as $titulo)
                            <option value="{{ $titulo }}"></option>
                        @endforeach
                    </datalist>

                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="codigo" name="codigo" step="1" required>
                    </div>

                    <div class="mb-3">
                        <label for="autor" class="form-label">Autor <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="autor" name="autor" list="libro_autores" required>
                    </div>

                    <datalist id="libro_autores">
                        @foreach ($libro_autores as $autor)
                            <option value="{{ $autor }}"></option>
                        @endforeach
                    </datalist>

                    <div class="mb-3">
                        <label for="categoria" class="form-label">Categoria <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoria" name="categoria" list="libro_categorias" required>
                    </div>

                    <datalist id="libro_categorias">
                        @foreach ($libro_categorias as $categoria)
                            <option value="{{ $categoria }}"></option>
                        @endforeach
                    </datalist>

                    <div class="mb-3">
                        <label for="editorial" class="form-label">Editorial <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editorial" name="editorial" list="libro_editoriales" required>
                    </div>

                    <datalist id="libro_editoriales">
                        @foreach ($libro_editoriales as $editorial)
                            <option value="{{ $editorial }}"></option>
                        @endforeach
                    </datalist>

                    <div class="mb-3">
                        <label for="presentacion" class="form-label">Presentacion <span
                                class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="presentacion" name="presentacion" list="libro_presentaciones" required>
                    </div>

                    <datalist id="libro_presentaciones">
                        @foreach ($libro_presentaciones as $presentacion)
                            <option value="{{ $presentacion }}"></option>
                        @endforeach
                    </datalist>

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
