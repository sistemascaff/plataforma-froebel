@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-clipboard-list-check me-2"></i> {{ $head_title }}
        </h1>
        <a class="btn btn-secondary shadow-sm" href="{{ route('asignaturas.detalles', $lista_asignatura->id_asignatura) }}">
            <i class="fa-solid fa-duotone fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    @if (!empty($cambios) && count($cambios) > 0)
        <div class="mb-3">
            <button type="button" class="btn btn-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCambios">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> Ver cambios recientes en la lista
            </button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header p-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-primary">
                <i class="fa-duotone fa-info-circle me-1"></i> Información Académica
            </h5>
            <span
                class="badge {{ $lista_asignatura->asignatura->tipo_bloque === 'mixto' ? 'bg-warning text-dark' : 'bg-info' }} px-3 py-2 fs-6 shadow-sm">
                <i class="fa-duotone fa-cubes me-1"></i> Bloque {{ ucfirst($lista_asignatura->asignatura->tipo_bloque) }}
            </span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-book me-1"></i> Asignatura:</p>
                    <p class="mb-0 fs-6">{{ $lista_asignatura->asignatura->asignatura }}</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-calendar me-1"></i> Gestión y Periodo:</p>
                    <p class="mb-0 fs-6">
                        {{ $lista_asignatura->periodo->gestion->anio }} - {{ $lista_asignatura->periodo->periodo }}
                        @php
                            $periodoEstado = $lista_asignatura->periodo->estado;

                            $periodoEstadoClass = match ($periodoEstado) {
                                1 => 'bg-success',
                                0 => 'bg-danger',
                                default => 'bg-secondary',
                            };

                            $periodoEstadoTexto = match ($periodoEstado) {
                                1 => 'ACTIVO',
                                0 => 'INACTIVO',
                                default => 'DESCONOCIDO',
                            };
                        @endphp
                        <span class="badge {{ $periodoEstadoClass }}">{{ $periodoEstadoTexto }}</span>
                    </p>
                </div>
                <div class="col-md-4">
                    <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-chalkboard-user me-1"></i> Docente Asignado:
                    </p>
                    <p class="mb-0 fs-6">
                        @if ($lista_asignatura->docente)
                            {{ $lista_asignatura->docente->persona->nombres_apellidos }}
                        @else
                            <span class="text-danger"><i class="fa-duotone fa-triangle-exclamation"></i> Sin asignar</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if ($lista_asignatura->asignatura->tipo_bloque === 'mixto')
        <div class="card shadow-sm mb-4 border-success" id="panel-edicion" style="display: none;">
            <div class="card-header bg-success bg-opacity-10 p-3">
                <h5 class="fw-bold mb-0 text-success">
                    <i class="fa-duotone fa-user-plus me-1"></i> Añadir Estudiante a la Lista Mixta
                </h5>
            </div>
            <div class="card-body">
                <div class="row align-items-end g-3">
                    <div class="col-md-9">
                        <label for="estudiante" class="form-label fw-bold text-muted">Seleccionar Estudiante <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="estudiante" style="width: 100%;">
                            <option value="">-- Seleccione un estudiante --</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-success w-100 shadow-sm" id="btn-agregar-estudiante">
                            <i class="fa-solid fa-duotone fa-plus me-1"></i> Agregar a tabla
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h5 class="card-title mb-0 fw-bold text-info">
                <i class="fa-solid fa-users-class me-2"></i> Estudiantes Inscritos
            </h5>
            <div class="d-flex gap-2">
                <a class="btn btn-success shadow-sm" href="{{ route('estudiantes_asistencias.crear', $lista_asignatura->id_lista_asignatura) }}">
                    <i class="fa-solid fa-duotone fa-plus me-1"></i>Crear asistencia
                </a>
                @if ($lista_asignatura->asignatura->tipo_bloque === 'mixto')
                    <button type="button" class="btn btn-warning shadow-sm" id="btn-toggle-edicion">
                        <i class="fa-solid fa-duotone fa-edit me-1"></i>Editar Lista
                    </button>
                    <button type="button" class="btn btn-primary shadow-sm" id="btn-guardar-estudiantes" disabled>
                        <i class="fa-solid fa-duotone fa-floppy-disk me-1"></i>Guardar Cambios
                    </button>
                @endif
            </div>

        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped dataTable w-100" id="estudiantes">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">#</th>
                            <th style="width: 5%;">Perfil</th>
                            <th>Estudiante</th>
                            <th>Correo</th>
                            <th>Curso</th>
                            <th class="text-center" style="width: 10%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lista_asignatura->estudiantes as $estudiante)
                            <tr id="fila-{{ $estudiante->id_estudiante }}">
                                <td class="text-center"></td>
                                <td class="text-center">
                                    <img class="rounded shadow-sm"
                                        src="{{ $estudiante->persona->usuario->url_foto_perfil ? URL::to('/') . '/' . $estudiante->persona->usuario->url_foto_perfil : URL::to('/') . '/public/img/user.png' }}"
                                        alt="Foto" style="width:35px; height:35px; object-fit:cover;">
                                </td>
                                <td class="align-middle fw-bold">
                                    {{ $estudiante->persona->apellidos_nombres }}
                                </td>
                                <td class="align-middle">{{ $estudiante->persona->usuario->correo }}</td>
                                <td class="align-middle">{{ $estudiante->curso->curso }}</td>
                                <td class="text-center align-middle">
                                    <div class="d-flex gap-1 justify-content-center align-items-center">
                                        <button type="button" class="btn btn-outline-secondary btn-sm btn-copiar-nombre"
                                            data-nombre="{{ $estudiante->persona->apellidos_nombres }}"
                                            data-bs-toggle="tooltip" title="Copiar nombre completo">
                                            <i class="fa-duotone fa-user-tag"></i>
                                        </button>

                                        <button type="button" class="btn btn-outline-info btn-sm btn-copiar-correo"
                                            data-correo="{{ $estudiante->persona->usuario->correo }}"
                                            data-bs-toggle="tooltip" title="Copiar correo electrónico">
                                            <i class="fa-duotone fa-envelope"></i>
                                        </button>

                                        @if ($lista_asignatura->asignatura->tipo_bloque === 'mixto')
                                            <button type="button" class="btn btn-danger btn-sm btn-remover"
                                                data-id="{{ $estudiante->id_estudiante }}" data-bs-toggle="tooltip"
                                                title="Remover de la lista" disabled>
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
    </div>

    @if (!empty($cambios) && count($cambios) > 0)
        <div class="modal fade" id="modalCambios" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="modalCambiosLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content shadow">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title fw-bold text-dark" id="modalCambiosLabel">
                            <i class="fa-solid fa-rotate me-2"></i> Actualización Automática de Lista
                        </h5>
                    </div>
                    <div class="modal-body pb-2">
                        <p class="text-muted mb-3">
                            El sistema ha sincronizado automáticamente esta lista de acuerdo a los estudiantes vigentes:
                        </p>
                        <div class="bg-body-tertiary p-3 rounded border mb-3"
                            style="max-height: 400px; overflow-y: auto;">
                            @foreach ($cambios as $cambio)
                                {!! $cambio !!}
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary shadow-sm" data-bs-dismiss="modal">
                            <i class="fa-solid fa-check me-2"></i> Entendido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    @include('listas_asignaturas.details_scripts')
@endsection
