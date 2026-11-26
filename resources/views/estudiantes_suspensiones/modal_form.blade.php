<div class="modal fade" id="modal-formulario" tabindex="-1" aria-labelledby="modal-formulario-titulo" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal-formulario-titulo">
                    <i class="fa-solid fa-duotone fa-plus"></i> REGISTRAR SUSPENSIÓN
                </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="form-crear-o-editar">
                    <input type="hidden" name="id_estudiante_suspension" id="id_estudiante_suspension" value="0">

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="id_estudiante" class="form-label">Estudiante <span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2" id="id_estudiante" name="id_estudiante" required>
                                <!-- Llenado desde JS -->
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fecha_inicio" class="form-label">Fecha y Hora Inicio <span
                                    class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="fecha_inicio" name="fecha_inicio"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_fin" class="form-label">Fecha y Hora Fin <span
                                    class="text-danger">*</span></label>
                            <input type="datetime-local" class="form-control" id="fecha_fin" name="fecha_fin" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="motivo" class="form-label">Motivo de la Suspensión <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control" id="motivo" name="motivo" rows="4"
                            placeholder="Describa detalladamente los hechos y el motivo de la suspensión..." required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="evidencia" class="form-label">Evidencia / Acta (URL de Drive) <span
                                class="text-muted">(Opcional)</span></label>
                        <input type="url" class="form-control" id="evidencia" name="evidencia" maxlength="150"
                            placeholder="https://drive.google.com/...">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-duotone fa-close me-1"></i>Cerrar
                </button>
                <button type="submit" id="btn-guardar" form="form-crear-o-editar" class="btn btn-primary">
                    <i class="fa-solid fa-duotone fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>
