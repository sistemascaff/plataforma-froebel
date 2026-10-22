<div class="modal fade" id="modalImportarCursos" tabindex="-1" aria-labelledby="modalImportarCursosLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="modalImportarCursosLabel">
                    <i class="fa-solid fa-duotone fa-users-medical me-2"></i>Importar Estudiantes por Cursos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form id="form-importar-cursos">
                <div class="modal-body pb-2">
                    <p class="text-muted small mb-3">
                        Seleccione los cursos cuyos estudiantes desea incorporar en masa a esta lista mixta. Los
                        estudiantes que ya pertenezcan a la lista serán ignorados automáticamente.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped w-100" id="tabla-cursos-importar">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">
                                        <input class="form-check-input" type="checkbox" id="check-todos-cursos">
                                    </th>
                                    <th>Curso</th>
                                    <th class="text-center">Nivel / Paralelo</th>
                                    <th class="text-center" style="width: 20%;">Total Estudiantes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cursos as $c)
                                    <tr>
                                        <td class="text-center align-middle">
                                            <input class="form-check-input check-curso" type="checkbox" name="cursos[]"
                                                value="{{ $c->id_curso }}" id="curso-{{ $c->id_curso }}">
                                        </td>
                                        <td class="align-middle fw-bold">
                                            <label for="curso-{{ $c->id_curso }}"
                                                class="mb-0 cursor-pointer">{{ $c->curso }}</label>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span
                                                class="badge bg-secondary">{{ $c->grado->nivel->nivel ?? 'N/A' }}</span>
                                            <span class="badge bg-info">{{ $c->paralelo->paralelo ?? 'N/A' }}</span>
                                        </td>
                                        <td class="text-center align-middle">
                                            <span class="badge bg-primary fs-6">{{ $c->estudiantes_count ?? 0 }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">
                        <i class="fa-solid fa-times me-1"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-success shadow-sm" id="btn-submit-importar-cursos">
                        <i class="fa-solid fa-duotone fa-check me-1"></i> Incorporar a la Lista
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
