<div class="modal fade" id="modalCambios" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="modalCambiosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow">
            <div class="modal-header bg-warning">
                <h5 class="modal-title fw-bold text-dark" id="modalCambiosLabel">
                    <i class="fa-solid fa-rotate me-2"></i>Actualización Automática de Lista
                </h5>
            </div>
            <div class="modal-body pb-2">
                <p class="text-muted mb-3">
                    El sistema ha sincronizado automáticamente esta lista de acuerdo a los estudiantes vigentes:
                </p>
                <div class="bg-body-tertiary p-3 rounded border mb-3" style="max-height: 400px; overflow-y: auto;">
                    @foreach ($cambios as $cambio)
                        {!! $cambio !!}
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">
                    <i class="fa-solid fa-check me-2"></i>Entendido
                </button>
            </div>
        </div>
    </div>
</div>
