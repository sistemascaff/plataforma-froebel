@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-clipboard-list-check me-2"></i> {{ $head_title }}
        </h1>
        <a class="btn btn-secondary shadow-sm"
            href="{{ route('listas_asignaturas.detalles', $lista_asignatura->id_lista_asignatura) }}">
            <i class="fa-solid fa-duotone fa-arrow-left me-1"></i> Volver
        </a>
    </div>

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

    <form id="form-asistencia">
        <input type="hidden" name="id_lista_asignatura" value="{{ $lista_asignatura->id_lista_asignatura }}">
        
        <div class="card shadow-sm mb-4">
            <div class="card-header p-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-info">
                    <i class="fa-duotone fa-info-circle me-1"></i> Horario y fecha de la clase
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-calendar-days me-1"></i> Fecha de la clase:
                        </p>
                        <input type="date" class="form-control" name="fecha" id="fecha" min="{{ date('Y-02-01') }}" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-clock me-1"></i> Horario de la clase:</p>
                        <select class="form-select" name="id_horario" id="id_horario_asignatura" required>
                            @foreach ($horarios as $horario)
                                <option value="{{ $horario->id_horario_asignatura }}">
                                    {{ helper_dia_semana_a_nombre($horario->pivot->dia_semana) }} {{ $horario->denominacion }}
                                    ({{ date('H:i', strtotime($horario->hora_inicio)) }} -
                                    {{ date('H:i', strtotime($horario->hora_fin)) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        @if ($lista_asignatura->estudiantes->isEmpty())
            <div class="alert alert-warning shadow-sm" role="alert">
                <i class="fa-duotone fa-triangle-exclamation me-2"></i> Aún no hay estudiantes inscritos en esta asignatura y período.
            </div>
        @else
            <div class="card shadow-sm mb-4">
                <div class="card-header p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="card-title mb-0 fw-bold text-info">
                        <i class="fa-solid fa-users-class me-2"></i> Estudiantes
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped dataTable w-100" id="estudiantes">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%;">#</th>
                                    <th style="width: 5%;">Perfil</th>
                                    <th>Estudiante</th>
                                    <th class="text-center">Tipo</th>
                                    <th class="text-center" style="width: 15%;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lista_asignatura->estudiantes as $index => $estudiante)
                                    @php
                                        // Verificación segura utilizando isset para evitar errores
                                        $tieneLicencia = isset($estudiantes_licencias[$estudiante->id_estudiante]);
                                        $licencia = $tieneLicencia ? $estudiantes_licencias[$estudiante->id_estudiante]->first() : null;
                                    @endphp
                                    <tr>
                                        <td class="text-center align-middle">{{ $loop->index + 1 }}</td>
                                        <td class="text-center align-middle">
                                            <img class="rounded shadow-sm"
                                                src="{{ $estudiante->persona->usuario->url_foto_perfil ? URL::to('/') . '/' . $estudiante->persona->usuario->url_foto_perfil : URL::to('/') . '/public/img/user.png' }}"
                                                alt="Foto" style="width:35px; height:35px; object-fit:cover;">
                                        </td>
                                        <td class="align-middle fw-bold">
                                            {{ $estudiante->persona->apellidos_nombres }}
                                            <span class="badge bg-secondary ms-2 curso"
                                                title="{{ $estudiante->curso->curso }}">{{ helper_abreviar_curso($estudiante->curso->curso) }}</span>
                                            <br>
                                            @if ($tieneLicencia)
                                                <div class="mt-1">
                                                    <span class="badge bg-info text-dark mb-1">Licencia:
                                                        {{ ucwords(str_replace('_', ' ', $licencia->tipo)) }}
                                                        -
                                                        {{ $licencia->justificacion }}</span><br>
                                                    <span class="badge bg-primary">Desde
                                                        {{ date('d/m/Y H:i', strtotime($licencia->fecha_inicio)) }}</span>
                                                    <span class="badge bg-danger">Hasta
                                                        {{ date('d/m/Y H:i', strtotime($licencia->fecha_fin)) }}</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            <input type="hidden" name="estudiantes[{{ $index }}][id_estudiante]" value="{{ $estudiante->id_estudiante }}">
                                            
                                            @if ($tieneLicencia)
                                                <span class="badge bg-info tipo">Licencia</span>
                                                <input type="hidden" name="estudiantes[{{ $index }}][id_estudiante_licencia]" value="{{ $licencia->id_estudiante_licencia }}">
                                                <input type="hidden" name="estudiantes[{{ $index }}][tipo]" value="L">
                                            @else
                                                <span class="badge bg-success tipo" id="badge_tipo_{{ $index }}">Presente</span>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            @if ($tieneLicencia)
                                                <span class="text-muted">No se puede modificar</span>
                                            @else
                                                <div class="btn-group shadow-sm" role="group">
                                                    <input type="radio" class="btn-check radio-asistencia" name="estudiantes[{{ $index }}][tipo]" id="presente_{{ $index }}" value="P" required checked data-index="{{ $index }}">
                                                    <label class="btn btn-outline-success" for="presente_{{ $index }}" title="Marcar como presente"><i class="fa fa-solid fa-duotone fa-check-circle"></i></label>

                                                    <input type="radio" class="btn-check radio-asistencia" name="estudiantes[{{ $index }}][tipo]" id="atraso_{{ $index }}" value="A" required data-index="{{ $index }}">
                                                    <label class="btn btn-outline-warning" for="atraso_{{ $index }}" title="Marcar como atraso"><i class="fa fa-solid fa-duotone fa-hourglass-half"></i></label>

                                                    <input type="radio" class="btn-check radio-asistencia" name="estudiantes[{{ $index }}][tipo]" id="falta_{{ $index }}" value="F" required data-index="{{ $index }}">
                                                    <label class="btn btn-outline-danger" for="falta_{{ $index }}" title="Marcar como falta"><i class="fa fa-solid fa-duotone fa-times-circle"></i></label>
                                                </div>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary shadow-sm" id="btn-guardar">
                            <i class="fa-solid fa-duotone fa-floppy-disk me-1"></i>Guardar Asistencia
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </form> @endsection

@section('scripts')
    <style>
        .curso:hover {
            cursor: pointer;
            background-color: #0d6efd !important;
        }
    </style>
    @include('estudiantes_asistencias.create_scripts')
@endsection