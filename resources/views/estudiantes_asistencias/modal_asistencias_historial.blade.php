<div class="modal fade" id="modal-historial-asistencias" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title text-info fw-bold">
                    <i class="fa-solid fa-duotone fa-clock-rotate-left me-2"></i>Historial de Asistencias
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="card shadow-sm mb-4 border-start border-4 border-primary">
                    <div class="card-body py-2 px-3">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-0 text-muted fs-7 fw-bold">Estudiante:</p>
                                <p class="mb-0 fw-bold" id="historial-nombre-estudiante">Cargando...</p>
                            </div>
                            <div class="col-md-6 border-start">
                                <p class="mb-0 text-muted fs-7 fw-bold">Curso:</p>
                                <p class="mb-0" id="historial-curso-estudiante">Cargando...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-bordered table-striped w-100" id="tabla-historial">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 5%;">#</th>
                                <th style="width: 15%;">Fecha</th>
                                <th>Asignatura</th>
                                <th class="text-center">Horario</th>
                                <th class="text-center" style="width: 20%;">Estado Registrado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Las filas se inyectarán mediante AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">
                    <i class="fa-solid fa-duotone fa-times me-1"></i> Cerrar
                </button>
            </div>
        </div>
    </div>
</div>
