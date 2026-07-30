@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-book-open-reader me-2"></i>{{ $head_title ?? 'Detalles de la Asignatura' }}
        </h1>
        <a class="btn btn-secondary shadow-sm" href="{{ route('asignaturas.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left me-1"></i>Volver
        </a>
    </div>

    @php
        $estado = match ($asignatura->estado) {
            0 => 'ARCHIVADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $badgeClass = match ($asignatura->estado) {
            0 => 'bg-secondary',
            1 => 'bg-success',
            default => 'bg-secondary',
        };
    @endphp

    <div class="card shadow-sm mb-4">
        <div class="card-header p-4 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold text-primary">{{ $asignatura->asignatura }}</h4>
            <span class="badge {{ $badgeClass }} px-3 py-2 fs-6 shadow-sm"><i class="fa-duotone fa-circle-check me-1"></i>
                {{ $estado }}</span>
        </div>
        <div class="card-body">
            <div class="row g-4 mt-1">
                <div class="col-md-6">
                    <h6 class="text-muted fw-bold mb-3 border-bottom pb-2">
                        <i class="fa-duotone fa-graduation-cap me-1"></i> Información Académica
                    </h6>
                    <div class="row mb-2">
                        <div class="col-sm-5 fw-bold text-muted">Materia:</div>
                        <div class="col-sm-7">{{ $asignatura->materia->abreviatura }} - {{ $asignatura->materia->materia }}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-5 fw-bold text-muted">Área:</div>
                        <div class="col-sm-7">{{ $asignatura->area->abreviatura }} - {{ $asignatura->area->area }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-5 fw-bold text-muted">Tipo Calificación:</div>
                        @php
                            $icono =
                                $asignatura->tipo_calificacion === 'cualitativa' ? 'fa-comments' : 'fa-chart-column';
                        @endphp
                        <div class="col-sm-7">
                            <span class="badge bg-info text-dark">
                                <i class="fa-solid fa-duotone {{ $icono }} me-1"></i>
                                {{ strtoupper($asignatura->tipo_calificacion) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-5 fw-bold text-muted">Tipo Bloque:</div>
                        <div class="col-sm-7">
                            <span class="badge {{ $asignatura->tipo_bloque === 'curso' ? 'bg-primary' : 'bg-danger' }}">
                                {{ strtoupper($asignatura->tipo_bloque) }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h6 class="text-muted fw-bold mb-3 border-bottom pb-2">
                        <i class="fa-duotone fa-sitemap me-1"></i> Ubicación y Estructura
                    </h6>
                    <div class="row mb-2">
                        <div class="col-sm-5 fw-bold text-muted">Aula:</div>
                        <div class="col-sm-7">{{ $asignatura->aula->aula }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-5 fw-bold text-muted">Curso:</div>
                        <div class="col-sm-7">{{ $asignatura->curso?->curso ?? 'No especificada' }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-5 fw-bold text-muted">Nivel:</div>
                        <div class="col-sm-7">{{ $asignatura->nivel->nivel }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-5 fw-bold text-muted">Coordinación:</div>
                        <div class="col-sm-7">{{ $asignatura->coordinacion?->coordinacion ?? 'No especificada' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header p-4 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold mb-0 text-info">
                <i class="fa-duotone fa-calendar-clock me-2"></i> Horarios de la Asignatura
            </h4>
            <button type="button" class="btn btn-primary" id="btn-guardar-horarios">
                <i class="fa-solid fa-duotone fa-floppy-disk me-1"></i>Guardar cambios
            </button>
        </div>
        <div class="card-body">
            <div class="accordion mb-4 shadow-sm" id="bootstrap-acordeon">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed rounded" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            <b class="text-secondary"><i class="fa-duotone fa-calendar-plus me-1"></i> ASIGNAR NUEVO HORARIO
                                DISPONIBLE</b>
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#bootstrap-acordeon">
                        <div class="accordion-body border border-top-0 rounded-bottom">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="dia_semana" class="form-label fw-bold">Día de la semana <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="dia_semana" name="dia_semana" required>
                                        <option value="" disabled selected>Selecciona un día...</option>
                                        <option value="1">LUNES</option>
                                        <option value="2">MARTES</option>
                                        <option value="3">MIÉRCOLES</option>
                                        <option value="4">JUEVES</option>
                                        <option value="5">VIERNES</option>
                                        <option value="6">SÁBADO</option>
                                    </select>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label for="horario" class="form-label fw-bold">Horario <span
                                            class="text-danger">*</span></label>
                                    <select class="form-select" id="horario" name="horario" required>
                                    </select>
                                    <div class="form-text text-muted small"><i class="fa-duotone fa-info-circle"></i> Se
                                        omiten los horarios de otros niveles, inactivos o de receso/recreo.</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-success" id="btn-agregar-horario">
                                    <i class="fa-solid fa-duotone fa-plus me-1"></i>Agregar a la lista
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 dataTable" id="horarios">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">#</th>
                            <th>Día</th>
                            <th>Denominación</th>
                            <th>Hora Inicio</th>
                            <th>Hora Fin</th>
                            <th>Gestión</th>
                            <th class="text-center" style="width: 10%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Renderizado dinámico desde JS --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header p-4 border-bottom">
            <h4 class="fw-bold mb-0 text-info">
                <i class="fa-duotone fa-users-class me-2"></i> Listas de la Asignatura
            </h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 dataTable" id="listas">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">#</th>
                            <th>Periodo</th>
                            <th>Gestión</th>
                            <th>Docente</th>
                            <th class="text-center" style="width: 10%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($asignatura->listas_asignaturas as $lista_asignatura)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ $lista_asignatura->periodo->periodo }}</td>
                                <td>{{ $lista_asignatura->periodo->gestion->anio }}</td>
                                <td class="fw-bold text-muted">
                                    {{ $lista_asignatura->docente?->persona->nombres_apellidos }}
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a class="btn btn-info btn-sm"
                                            href="{{ route('listas_asignaturas.detalles', $lista_asignatura->id_lista_asignatura) }}"
                                            data-bs-toggle="tooltip" title="Detalles de la lista">
                                            <i class="fa-duotone fa-solid fa-eye"></i>
                                        </a>
                                        <button type="button" class="btn btn-warning btn-sm btn-editar-docente"
                                            data-id-docente="{{ $lista_asignatura->id_docente }}"
                                            data-id-lista="{{ $lista_asignatura->id_lista_asignatura }}"
                                            data-bs-toggle="tooltip" title="Editar docente">
                                            <i class="fa-duotone fa-solid fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @include('asignaturas.details_docentes_modal_form')
@endsection

@section('scripts')
    @include('asignaturas.details_scripts')
@endsection
