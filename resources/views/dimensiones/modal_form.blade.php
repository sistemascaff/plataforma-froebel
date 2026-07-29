<!-- Modal para crear y editar libros -->
<div class="modal fade" id="modal-formulario" tabindex="-1" aria-labelledby="modal-formulario-titulo" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal-formulario-titulo"><i class="fa-solid fa-duotone fa-plus"></i>
                    CREAR DIMENSIÓN</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-crear-o-editar">
                    <!-- input de id_dimension en caso de editar -->
                    <input type="hidden" name="id_dimension" value="0">

                    <div class="mb-3">
                        <label for="dimension" class="form-label">Dimensión <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="dimension" name="dimension" min="3"
                            max="45" required>
                    </div>

                    <div class="mb-3">
                        <label for="posicion_ordinal" class="form-label">Posición ordinal <span
                                class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="posicion_ordinal" name="posicion_ordinal"
                            step="1" min="1" max="4" required>
                    </div>

                    <div class="mb-3">
                        <label for="puntaje_maximo" class="form-label">Puntaje máximo <span
                                class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="puntaje_maximo" name="puntaje_maximo"
                            step="1" min="0" max="100" required>
                    </div>

                    <div class="mb-3">
                        <label for="tipo_calculo" class="form-label">Tipo de cálculo <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="tipo_calculo" name="tipo_calculo" required>
                            <option value="" disabled selected>Seleccione un tipo de cálculo</option>
                            <option value="sumatoria">SUMATORIA</option>
                            <option value="promedio">PROMEDIO</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="gestion" class="form-label">Gestión <span class="text-danger">*</span></label>
                        <select class="form-select" id="gestion" name="id_gestion" required>
                            <option value="" disabled selected>Seleccione una gestión</option>
                            @foreach ($gestiones as $gestion)
                                <option value="{{ $gestion->id_gestion }}">{{ $gestion->anio }}</option>
                            @endforeach
                        </select>
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
