<!-- Modal para cambiar contraseña -->
<div class="modal fade" id="modal-cambiar-contrasenha" tabindex="-1" aria-labelledby="modalCambiarContrasenhaLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-sm border-0">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title text-warning fw-bold" id="modalCambiarContrasenhaLabel">
                    <i class="fa-solid fa-duotone fa-key me-2"></i>Cambiar Contraseña
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-cambiar-contrasenha">
                <div class="modal-body">
                    <div class="alert alert-info shadow-sm mb-4 border-info border-start border-4 d-flex align-items-center"
                        role="alert">
                        <i class="fa-solid fa-duotone fa-circle-info fa-2x me-3"></i>
                        <div>
                            <p class="mb-0">Por seguridad, la nueva contraseña debe contener al menos <strong>8
                                    caracteres</strong>.</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="contrasenha_actual" class="form-label text-muted fw-bold">Contraseña actual</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text"><i class="fa-solid fa-duotone fa-lock"></i></span>
                            <input type="password" class="form-control" id="contrasenha_actual"
                                name="contrasenha_actual" required>
                            <button class="btn btn-outline-secondary btn-toggle-password" type="button" tabindex="-1"
                                title="Mostrar/Ocultar contraseña">
                                <i class="fa-solid fa-duotone fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nueva_contrasenha" class="form-label text-muted fw-bold">Nueva contraseña</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text"><i class="fa-solid fa-duotone fa-lock-keyhole"></i></span>
                            <input type="password" class="form-control" id="nueva_contrasenha" name="nueva_contrasenha"
                                minlength="8" required>
                            <button class="btn btn-outline-secondary btn-toggle-password" type="button" tabindex="-1"
                                title="Mostrar/Ocultar contraseña">
                                <i class="fa-solid fa-duotone fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="nueva_contrasenha_confirmation" class="form-label text-muted fw-bold">Confirmar
                            nueva contraseña</label>
                        <div class="input-group shadow-sm">
                            <span class="input-group-text"><i class="fa-solid fa-duotone fa-shield-check"></i></span>
                            <input type="password" class="form-control" id="nueva_contrasenha_confirmation"
                                name="nueva_contrasenha_confirmation" minlength="8" required>
                            <button class="btn btn-outline-secondary btn-toggle-password" type="button" tabindex="-1"
                                title="Mostrar/Ocultar contraseña">
                                <i class="fa-solid fa-duotone fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">
                        <i class="fa-solid fa-duotone fa-times me-1"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-warning shadow-sm text-dark fw-bold"
                        id="btn-guardar-contrasenha">
                        <i class="fa-solid fa-duotone fa-save me-1"></i>Actualizar Contraseña
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
