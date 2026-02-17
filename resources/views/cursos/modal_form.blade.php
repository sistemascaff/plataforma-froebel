<!-- Modal para crear y editar libros -->
<div class="modal fade" id="modal-formulario" tabindex="-1" aria-labelledby="modal-formulario-titulo" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal-formulario-titulo"><i class="fa-solid fa-duotone fa-plus"></i>
                    CREAR CURSO</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-crear-o-editar">
                    <!-- input de id_curso en caso de editar -->
                    <input type="hidden" name="id_curso" value="0">

                    <div class="mb-3">
                        <label for="curso" class="form-label">Curso <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="curso" name="curso" min="3" required>
                    </div>

                    <div class="mb-3">
                        <label for="grado" class="form-label">Nivel <span class="text-danger">*</span></label>
                        <select class="form-select" id="grado" name="id_grado" required>
                            <option value="" disabled selected>Seleccione un grado</option>
                            @foreach ($grados as $grado)
                                <option value="{{ $grado->id_grado }}">{{ $grado->grado }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="paralelo" class="form-label">Paralelo <span class="text-danger">*</span></label>
                        <select class="form-select" id="paralelo" name="id_paralelo" required>
                            <option value="" disabled selected>Seleccione un paralelo</option>
                            @foreach ($paralelos as $paralelo)
                                <option value="{{ $paralelo->id_paralelo }}">{{ $paralelo->paralelo }}</option>
                            @endforeach
                        </select>
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
