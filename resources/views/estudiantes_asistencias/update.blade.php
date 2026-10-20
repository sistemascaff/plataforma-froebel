@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-pen-to-square me-2"></i>{{ $head_title }}
        </h1>
        <a class="btn btn-secondary shadow-sm"
            href="{{ route('listas_asignaturas.detalles', $estudiante_asistencia->id_lista_asignatura) }}">
            <i class="fa-solid fa-duotone fa-arrow-left me-1"></i> Volver a la Lista
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header p-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-primary">
                <i class="fa-duotone fa-info-circle me-1"></i> Información Académica de la Asignatura
            </h5>
            <span
                class="badge {{ $estudiante_asistencia->lista_asignatura->asignatura->tipo_bloque === 'mixto' ? 'bg-warning text-dark' : 'bg-info' }} px-3 py-2 fs-6 shadow-sm">
                <i class="fa-duotone fa-cubes me-1"></i> Bloque
                {{ ucfirst($estudiante_asistencia->lista_asignatura->asignatura->tipo_bloque) }}
            </span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-book me-1"></i> Asignatura:</p>
                    <p class="mb-0 fs-6 fw-bold text-info">
                        {{ $estudiante_asistencia->lista_asignatura->asignatura->asignatura }}
                    </p>
                </div>
                <div class="col-md-4">
                    <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-calendar me-1"></i> Gestión y Periodo:</p>
                    <p class="mb-0 fs-6">
                        {{ $estudiante_asistencia->lista_asignatura->periodo->gestion->anio }} -
                        {{ $estudiante_asistencia->lista_asignatura->periodo->periodo }}
                        @php
                            $periodoEstado = $estudiante_asistencia->lista_asignatura->periodo->estado;

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
                        @if ($estudiante_asistencia->lista_asignatura->docente)
                            {{ $estudiante_asistencia->lista_asignatura->docente->persona->apellidos_nombres }}
                        @else
                            <span class="text-danger"><i class="fa-duotone fa-triangle-exclamation"></i> Sin asignar</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    @php
        // Determinamos si la asistencia actual está en modo "OTROS"
        $esModoOtrosInicial =
            !empty($estudiante_asistencia->justificacion_otros) ||
            $estudiante_asistencia->detalles_estudiantes_asistencias->contains('tipo', 'OTROS');
    @endphp

    <form id="form-asistencia">
        <input type="hidden" name="id_lista_asignatura" value="{{ $estudiante_asistencia->id_lista_asignatura }}">
        <input type="hidden" name="id_estudiante_asistencia"
            value="{{ $estudiante_asistencia->id_estudiante_asistencia }}">

        <div class="card shadow-sm mb-4">
            <div class="card-header p-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-info">
                    <i class="fa-duotone fa-clock-desk me-1"></i> Modificar Horario, Fecha y Modalidad de Clase
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-calendar-days me-1"></i> Fecha de la
                            clase:</p>
                        <input type="date" class="form-control" name="fecha" id="fecha"
                            min="{{ date('Y-02-01') }}" max="{{ date('Y-m-d') }}"
                            value="{{ $estudiante_asistencia->fecha }}" required>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-clock me-1"></i> Horario de la clase:</p>
                        <select class="form-select" name="id_horario" id="id_horario_asignatura" required>
                            @foreach ($horarios as $horario)
                                <option value="{{ $horario->id_horario_asignatura }}"
                                    data-dia="{{ $horario->pivot->dia_semana }}"
                                    {{ $estudiante_asistencia->id_horario_asignatura == $horario->id_horario_asignatura ? 'selected' : '' }}>
                                    @if (isset($horario->pivot->dia_semana))
                                        {{ helper_dia_semana_a_nombre($horario->pivot->dia_semana) }}
                                    @endif
                                    {{ $horario->denominacion }}
                                    ({{ date('H:i', strtotime($horario->hora_inicio)) }} -
                                    {{ date('H:i', strtotime($horario->hora_fin)) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Interruptor para Modo Global "OTROS" -->
                    <div class="col-md-4">
                        <div
                            class="form-check form-switch p-2 border rounded bg-body-tertiary d-flex align-items-center justify-content-between">
                            <label class="form-check-label fw-bold text-secondary mb-0 me-2" for="switch_modo_otros"
                                style="cursor: pointer;">
                                <i class="fa-duotone fa-calendar-star me-1"></i> Marcar clase como "OTROS"
                            </label>
                            <input class="form-check-input ms-0 fs-5" type="checkbox" role="switch" id="switch_modo_otros"
                                style="cursor: pointer;" {{ $esModoOtrosInicial ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- Contenedor de Justificación para "OTROS" -->
                    <div class="col-md-12" id="contenedor_justificacion_otros"
                        style="display: {{ $esModoOtrosInicial ? 'block' : 'none' }};">
                        <div class="alert alert-secondary mb-0 border-secondary shadow-sm">
                            <div class="row g-2 align-items-center">
                                <div class="col-md-6">
                                    <label for="justificacion_otros" class="form-label fw-bold mb-1">
                                        <i class="fa-duotone fa-file-certificate me-1"></i> Justificación para asistencia
                                        tipo "OTROS" (Obligatorio):
                                    </label>
                                    <select class="form-select" name="justificacion_otros" id="justificacion_otros"
                                        {{ $esModoOtrosInicial ? 'required' : '' }}>
                                        <option value="" disabled
                                            {{ empty($estudiante_asistencia->justificacion_otros) ? 'selected' : '' }}>
                                            Seleccione el motivo de la actividad...
                                        </option>
                                        <option value="ACTIVIDAD CURRICULAR"
                                            {{ $estudiante_asistencia->justificacion_otros === 'ACTIVIDAD CURRICULAR' ? 'selected' : '' }}>
                                            ACTIVIDAD CURRICULAR
                                        </option>
                                        <option value="ACTIVIDAD EXTRACURRICULAR"
                                            {{ $estudiante_asistencia->justificacion_otros === 'ACTIVIDAD EXTRACURRICULAR' ? 'selected' : '' }}>
                                            ACTIVIDAD EXTRACURRICULAR
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6 text-muted small mt-3 mt-md-4">
                                    <i class="fa-duotone fa-circle-info me-1"></i>
                                    Al mantener activa esta opción, todos los estudiantes (excepto aquellos con
                                    <strong>Licencia</strong> vigente) serán registrados automáticamente con el estado
                                    <strong>OTROS</strong>.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($estudiante_asistencia->detalles_estudiantes_asistencias->isEmpty())
            <div class="alert alert-warning shadow-sm" role="alert">
                <i class="fa-duotone fa-triangle-exclamation me-2"></i>No existen registros de estudiantes en esta
                asistencia.
            </div>
        @else
            <div class="card shadow-sm mb-4">
                <div class="card-header p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <h5 class="card-title mb-0 fw-bold text-info">
                        <i class="fa-solid fa-users-class me-2"></i>Modificar Registro de Estudiantes
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
                                    <th class="text-center">Estado Actual</th>
                                    <th class="text-center" style="width: 18%;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($estudiante_asistencia->detalles_estudiantes_asistencias as $index => $detalle)
                                    @php
                                        $estudiante = $detalle->estudiante;
                                        // Prioridad: Licencia ya asignada en el detalle, o licencia activa hallada en la BD para esa fecha
                                        $licencia =
                                            $detalle->estudiante_licencia ??
                                            (isset($estudiantes_licencias[$estudiante->id_estudiante])
                                                ? $estudiantes_licencias[$estudiante->id_estudiante]->first()
                                                : null);
                                        $tieneLicencia = !is_null($licencia);
                                        $tipoActual = $detalle->tipo;
                                        $tiempoAtrasoActual = $detalle->tiempo_atraso ?? '';

                                        // Si estaba en modo OTROS y el docente desmarca el switch en el frontend, por defecto el radio "PRESENTE" quedará preseleccionado
                                        $tipoRadioSeleccionado = in_array($tipoActual, [
                                            'PRESENTE',
                                            'ATRASO',
                                            'FALTA',
                                            'PERMISO',
                                        ])
                                            ? $tipoActual
                                            : 'PRESENTE';
                                    @endphp
                                    <tr>
                                        <td class="text-center align-middle">{{ $loop->index + 1 }}</td>
                                        <td class="text-center align-middle">
                                            <img class="rounded shadow-sm zoomable-image"
                                                src="{{ $estudiante->persona->usuario->url_foto_perfil ? URL::to('/') . '/' . $estudiante->persona->usuario->url_foto_perfil : URL::to('/') . '/public/img/user.png' }}"
                                                alt="Foto" style="width:35px; height:35px; object-fit:cover;">
                                        </td>
                                        <td class="align-middle fw-bold">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    {{ $estudiante->persona->apellidos_nombres }}
                                                    <span class="badge bg-secondary ms-2 curso"
                                                        title="{{ $estudiante->curso->curso }}">
                                                        {{ helper_abreviar_curso($estudiante->curso->curso) }}
                                                    </span>
                                                </div>
                                                <!-- Botón para abrir el historial -->
                                                <button type="button"
                                                    class="btn btn-primary btn-sm shadow-sm btn-historial ms-2"
                                                    data-id="{{ $estudiante->id_estudiante }}"
                                                    title="Ver últimas 30 asistencias">
                                                    <i class="fa-solid fa-duotone fa-clock-rotate-left"></i>
                                                </button>
                                            </div>
                                            @if ($tieneLicencia)
                                                <div class="mt-2">
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
                                            <input type="hidden" name="estudiantes[{{ $index }}][id_estudiante]"
                                                value="{{ $estudiante->id_estudiante }}">

                                            @if ($tieneLicencia)
                                                <span class="badge bg-info text-dark tipo">Licencia</span>
                                                <input type="hidden"
                                                    name="estudiantes[{{ $index }}][id_estudiante_licencia]"
                                                    value="{{ $licencia->id_estudiante_licencia }}">
                                                <input type="hidden" name="estudiantes[{{ $index }}][tipo]"
                                                    value="LICENCIA">
                                            @else
                                                @php
                                                    $badgeClass = match ($tipoActual) {
                                                        'PRESENTE' => 'bg-success',
                                                        'ATRASO' => 'bg-warning text-dark',
                                                        'FALTA' => 'bg-danger',
                                                        'PERMISO' => 'bg-primary',
                                                        'OTROS' => 'bg-secondary',
                                                        default => 'bg-success',
                                                    };

                                                    $badgeText = match ($tipoActual) {
                                                        'PRESENTE' => 'Presente',
                                                        'ATRASO' => 'Atraso',
                                                        'FALTA' => 'Falta',
                                                        'PERMISO' => 'Permiso',
                                                        'OTROS' => 'Otros',
                                                        default => 'Presente',
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeClass }} tipo"
                                                    id="badge_tipo_{{ $index }}">{{ $badgeText }}</span>

                                                <!-- Input oculto exclusivo para cuando el modo global OTROS está activo -->
                                                <input type="hidden" class="input-hidden-otros"
                                                    name="estudiantes[{{ $index }}][tipo]"
                                                    id="input_otros_{{ $index }}" value="OTROS"
                                                    {{ $esModoOtrosInicial ? '' : 'disabled' }}>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            @if ($tieneLicencia)
                                                <span class="text-muted">No se puede modificar</span>
                                            @else
                                                <div class="d-flex justify-content-center align-items-center gap-2 contenedor-acciones-fila"
                                                    id="acciones_fila_{{ $index }}"
                                                    style="{{ $esModoOtrosInicial ? 'display: none !important;' : '' }}">
                                                    <div class="btn-group shadow-sm" role="group">
                                                        <!-- PRESENTE -->
                                                        <input type="radio" class="btn-check radio-asistencia"
                                                            name="estudiantes[{{ $index }}][tipo]"
                                                            id="presente_{{ $index }}" value="PRESENTE" required
                                                            {{ $tipoRadioSeleccionado === 'PRESENTE' ? 'checked' : '' }}
                                                            {{ $esModoOtrosInicial ? 'disabled' : '' }}
                                                            data-index="{{ $index }}">
                                                        <label class="btn btn-outline-success"
                                                            for="presente_{{ $index }}"
                                                            title="Marcar como presente"><i
                                                                class="fa fa-solid fa-duotone fa-check-circle"></i></label>

                                                        <!-- ATRASO -->
                                                        <input type="radio" class="btn-check radio-asistencia"
                                                            name="estudiantes[{{ $index }}][tipo]"
                                                            id="atraso_{{ $index }}" value="ATRASO" required
                                                            {{ $tipoRadioSeleccionado === 'ATRASO' ? 'checked' : '' }}
                                                            {{ $esModoOtrosInicial ? 'disabled' : '' }}
                                                            data-index="{{ $index }}">
                                                        <label class="btn btn-outline-warning"
                                                            for="atraso_{{ $index }}"
                                                            title="Marcar como atraso"><i
                                                                class="fa fa-solid fa-duotone fa-hourglass-half"></i></label>

                                                        <!-- FALTA -->
                                                        <input type="radio" class="btn-check radio-asistencia"
                                                            name="estudiantes[{{ $index }}][tipo]"
                                                            id="falta_{{ $index }}" value="FALTA" required
                                                            {{ $tipoRadioSeleccionado === 'FALTA' ? 'checked' : '' }}
                                                            {{ $esModoOtrosInicial ? 'disabled' : '' }}
                                                            data-index="{{ $index }}">
                                                        <label class="btn btn-outline-danger"
                                                            for="falta_{{ $index }}" title="Marcar como falta"><i
                                                                class="fa fa-solid fa-duotone fa-times-circle"></i></label>

                                                        <!-- PERMISO -->
                                                        <input type="radio" class="btn-check radio-asistencia"
                                                            name="estudiantes[{{ $index }}][tipo]"
                                                            id="permiso_{{ $index }}" value="PERMISO" required
                                                            {{ $tipoRadioSeleccionado === 'PERMISO' ? 'checked' : '' }}
                                                            {{ $esModoOtrosInicial ? 'disabled' : '' }}
                                                            data-index="{{ $index }}">
                                                        <label class="btn btn-outline-primary"
                                                            for="permiso_{{ $index }}"
                                                            title="Marcar como permiso"><i
                                                                class="fa fa-solid fa-duotone fa-file-signature"></i></label>
                                                    </div>

                                                    <input type="number"
                                                        class="form-control form-control-sm shadow-sm input-tiempo-atraso"
                                                        name="estudiantes[{{ $index }}][tiempo_atraso]"
                                                        id="tiempo_atraso_{{ $index }}" min="1"
                                                        max="60"
                                                        value="{{ !$esModoOtrosInicial && $tipoActual === 'ATRASO' ? $tiempoAtrasoActual : '' }}"
                                                        placeholder="Min." title="Minutos de atraso"
                                                        style="display: {{ !$esModoOtrosInicial && $tipoActual === 'ATRASO' ? 'block' : 'none' }}; width: 70px;"
                                                        {{ !$esModoOtrosInicial && $tipoActual === 'ATRASO' ? 'required' : '' }}>
                                                </div>

                                                <!-- Etiqueta informativa cuando el modo OTROS está activo -->
                                                <span class="text-muted small etiqueta-modo-otros"
                                                    id="etiqueta_otros_{{ $index }}"
                                                    style="display: {{ $esModoOtrosInicial ? 'inline-block' : 'none' }};">
                                                    <i class="fa-duotone fa-lock me-1"></i> Bloqueado por modo OTROS
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-warning shadow-sm text-dark" id="btn-guardar">
                            <i class="fa-solid fa-duotone fa-floppy-disk me-1"></i>Actualizar Asistencia
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </form>

    @include('components.app.img_modal')

    @include('estudiantes_asistencias.modal_asistencias_historial')
@endsection

@section('scripts')
    <style>
        .curso:hover {
            cursor: pointer;
            background-color: #0d6efd !important;
        }
    </style>

    @include('estudiantes_asistencias.update_scripts')

    @include('estudiantes_asistencias.modal_asistencias_historial_scripts')
@endsection
