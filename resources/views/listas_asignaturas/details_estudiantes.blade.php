<div class="tab-pane fade show active" id="tab-estudiantes" role="tabpanel" aria-labelledby="estudiantes-tab">

    @if ($lista_asignatura->asignatura->tipo_bloque === 'mixto')
        {{-- Barra de Herramientas Rápida para Lista Mixta --}}
        <div class="card border-info bg-body-tertiary shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-7">
                        <label for="estudiante" class="form-label fw-bold text-info mb-1">
                            <i class="fa-duotone fa-user-plus me-1"></i> Agregar Estudiantes (Búsqueda
                            Múltiple)
                        </label>
                        <select class="form-select" id="estudiante" multiple="multiple" style="width: 100%;"></select>
                    </div>
                    <div class="col-lg-5 d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-success shadow-sm flex-grow-1"
                            id="btn-agregar-estudiante">
                            <i class="fa-solid fa-duotone fa-plus me-1"></i> Añadir Seleccionados
                        </button>
                        <button type="button" class="btn btn-outline-primary shadow-sm" data-bs-toggle="modal"
                            data-bs-target="#modalImportarCursos">
                            <i class="fa-solid fa-duotone fa-file-import me-1"></i> Importar Cursos
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Barra de Estado de Cambios Pendientes (Oculta por defecto) --}}
        <div id="barra-cambios-pendientes"
            class="alert alert-warning shadow-sm d-none justify-content-between align-items-center flex-wrap gap-2 mb-3 py-2 px-3">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-duotone fa-circle-exclamation fs-5 me-2"></i>
                <span>
                    Tienes <b>cambios sin guardar</b> en la lista.
                    <span id="resumen-cambios" class="ms-1 small"></span>
                </span>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-dark" id="btn-deshacer-cambios">
                    <i class="fa-solid fa-rotate-left me-1"></i> Deshacer
                </button>
                <button type="button" class="btn btn-sm btn-primary fw-bold shadow-sm" id="btn-guardar-estudiantes">
                    <i class="fa-solid fa-duotone fa-floppy-disk me-1"></i> Guardar Cambios
                </button>
            </div>
        </div>

        {{-- Acciones de Selección Masiva en Tabla --}}
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-danger btn-sm shadow-sm d-none"
                    id="btn-remover-seleccionados">
                    <i class="fa-duotone fa-trash-can me-1"></i> Quitar seleccionados (<span
                        id="count-seleccionados">0</span>)
                </button>
            </div>
        </div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover table-bordered table-striped dataTable w-100" id="estudiantes">
            <thead>
                <tr>
                    @if ($lista_asignatura->asignatura->tipo_bloque === 'mixto')
                        <th class="text-center" style="width: 4%;">
                            <input class="form-check-input" type="checkbox" id="check-todos-estudiantes"
                                title="Seleccionar todos">
                        </th>
                    @endif
                    <th class="text-center" style="width: 5%;">#</th>
                    <th class="text-center" style="width: 5%;">Perfil</th>
                    <th>Estudiante</th>
                    <th>Correo</th>
                    <th>Curso</th>
                    <th class="text-center" style="width: 12%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lista_asignatura->estudiantes as $estudiante)
                    <tr id="fila-{{ $estudiante->id_estudiante }}" data-id="{{ $estudiante->id_estudiante }}">
                        @if ($lista_asignatura->asignatura->tipo_bloque === 'mixto')
                            <td class="text-center align-middle">
                                <input class="form-check-input check-estudiante" type="checkbox"
                                    value="{{ $estudiante->id_estudiante }}">
                            </td>
                        @endif
                        <td class="text-center align-middle"></td>
                        <td class="text-center align-middle">
                            <img class="rounded shadow-sm zoomable-image"
                                src="{{ $estudiante->persona->usuario->url_foto_perfil ? URL::to('/') . '/' . $estudiante->persona->usuario->url_foto_perfil : URL::to('/') . '/public/img/user.png' }}"
                                alt="Foto" style="width:35px; height:35px; object-fit:cover;">
                        </td>
                        <td class="align-middle fw-bold">
                            {{ $estudiante->persona->apellidos_nombres }}
                        </td>
                        <td class="align-middle">{{ $estudiante->persona->usuario->correo }}</td>
                        <td class="align-middle">{{ $estudiante->curso->curso ?? 'Sin Curso' }}</td>
                        <td class="text-center align-middle">
                            <div class="d-flex gap-1 justify-content-center align-items-center">
                                <button type="button" class="btn btn-outline-secondary btn-sm btn-copiar-nombre"
                                    data-nombre="{{ $estudiante->persona->apellidos_nombres }}" data-bs-toggle="tooltip"
                                    title="Copiar nombre completo">
                                    <i class="fa-duotone fa-user-tag"></i>
                                </button>

                                <button type="button" class="btn btn-outline-info btn-sm btn-copiar-correo"
                                    data-correo="{{ $estudiante->persona->usuario->correo }}" data-bs-toggle="tooltip"
                                    title="Copiar correo electrónico">
                                    <i class="fa-duotone fa-envelope"></i>
                                </button>

                                @if ($lista_asignatura->asignatura->tipo_bloque === 'mixto')
                                    <button type="button" class="btn btn-outline-danger btn-sm btn-remover"
                                        data-id="{{ $estudiante->id_estudiante }}" data-bs-toggle="tooltip"
                                        title="Quitar de la lista">
                                        <i class="fa-duotone fa-trash"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
