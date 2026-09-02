@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-clipboard-user me-2"></i> {{ $head_title }}
        </h1>

        <div class="d-flex gap-2">
            <a class="btn btn-warning shadow-sm"
                href="{{ route('estudiantes_asistencias.editar', $estudiante_asistencia->id_estudiante_asistencia) }}">
                <i class="fa-solid fa-duotone fa-pen-to-square me-1"></i> Editar Asistencia
            </a>

            <a class="btn btn-secondary shadow-sm"
                href="{{ route('listas_asignaturas.detalles', $estudiante_asistencia->id_lista_asignatura) }}">
                <i class="fa-solid fa-duotone fa-arrow-left me-1"></i> Volver a la Lista
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header p-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-primary">
                <i class="fa-duotone fa-circle-info me-1"></i> Información Académica de la Clase
            </h5>
            <span class="badge bg-primary px-3 py-2 fs-6 shadow-sm">
                <i class="fa-duotone fa-calendar-day me-1"></i>
                {{ helper_dia_semana_a_nombre(date('N', strtotime($estudiante_asistencia->fecha))) }}
                {{ date('d/m/Y', strtotime($estudiante_asistencia->fecha)) }}
            </span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-book me-1"></i> Asignatura:</p>
                    <p class="mb-0 fs-6 fw-bold text-info">
                        {{ $estudiante_asistencia->lista_asignatura->asignatura->asignatura }}
                    </p>
                </div>
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-clock me-1"></i> Horario:</p>
                    <p class="mb-0 fs-6">
                        @if ($estudiante_asistencia->horario_asignatura)
                            {{ $estudiante_asistencia->horario_asignatura->denominacion }}
                            ({{ date('H:i', strtotime($estudiante_asistencia->horario_asignatura->hora_inicio)) }} -
                            {{ date('H:i', strtotime($estudiante_asistencia->horario_asignatura->hora_fin)) }})
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-3">
                    <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-calendar me-1"></i> Gestión y Periodo:</p>
                    <p class="mb-0 fs-6">
                        {{ $estudiante_asistencia->lista_asignatura->periodo->gestion->anio }} -
                        {{ $estudiante_asistencia->lista_asignatura->periodo->periodo }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    @php
        $totalEstudiantes = $estudiante_asistencia->detalles_estudiantes_asistencias->count();
        $presentes = $estudiante_asistencia->detalles_estudiantes_asistencias->where('tipo', 'P')->count();
        $atrasos = $estudiante_asistencia->detalles_estudiantes_asistencias->where('tipo', 'A')->count();
        $faltas = $estudiante_asistencia->detalles_estudiantes_asistencias->where('tipo', 'F')->count();
        $licencias = $estudiante_asistencia->detalles_estudiantes_asistencias->where('tipo', 'L')->count();
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-6 col-md">
            <div class="card shadow-sm border-start border-4 border-secondary h-100">
                <div class="card-body p-3 text-center">
                    <p class="text-muted fw-bold mb-1 fs-7"><i class="fa-duotone fa-users me-1"></i> Registrados</p>
                    <h3 class="fw-bold mb-0 text-secondary">{{ $totalEstudiantes }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card shadow-sm border-start border-4 border-success h-100">
                <div class="card-body p-3 text-center">
                    <p class="text-muted fw-bold mb-1 fs-7"><i class="fa-duotone fa-user-check me-1"></i> Presentes</p>
                    <h3 class="fw-bold mb-0 text-success">{{ $presentes }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card shadow-sm border-start border-4 border-warning h-100">
                <div class="card-body p-3 text-center">
                    <p class="text-muted fw-bold mb-1 fs-7"><i class="fa-duotone fa-hourglass-half me-1"></i> Atrasos</p>
                    <h3 class="fw-bold mb-0 text-warning">{{ $atrasos }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card shadow-sm border-start border-4 border-danger h-100">
                <div class="card-body p-3 text-center">
                    <p class="text-muted fw-bold mb-1 fs-7"><i class="fa-duotone fa-user-xmark me-1"></i> Faltas</p>
                    <h3 class="fw-bold mb-0 text-danger">{{ $faltas }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-md">
            <div class="card shadow-sm border-start border-4 border-info h-100">
                <div class="card-body p-3 text-center">
                    <p class="text-muted fw-bold mb-1 fs-7"><i class="fa-duotone fa-file-certificate me-1"></i> Licencias
                    </p>
                    <h3 class="fw-bold mb-0 text-info">{{ $licencias }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header p-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-info">
                <i class="fa-solid fa-users-class me-2"></i> Detalle de Asistencia por Estudiante
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped dataTable w-100"
                    id="tabla-detalles-asistencia">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">#</th>
                            <th class="text-center" style="width: 5%;">Perfil</th>
                            <th>Estudiante</th>
                            <th class="text-center">Curso</th>
                            <th class="text-center" style="width: 18%;">Estado Registrado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($estudiante_asistencia->detalles_estudiantes_asistencias as $detalle)
                            @php
                                $tipo = $detalle->tipo;

                                $badgeClass = match ($tipo) {
                                    'P' => 'bg-success',
                                    'A' => 'bg-warning text-dark',
                                    'F' => 'bg-danger',
                                    'L' => 'bg-info text-dark',
                                    default => 'bg-secondary',
                                };

                                $tipoTexto = match ($tipo) {
                                    'P' => 'Presente',
                                    'A' => 'Atraso',
                                    'F' => 'Falta',
                                    'L' => 'Licencia Justificada',
                                    default => 'Desconocido',
                                };

                                $tipoIcono = match ($tipo) {
                                    'P' => 'fa-check-circle',
                                    'A' => 'fa-hourglass-half',
                                    'F' => 'fa-times-circle',
                                    'L' => 'fa-file-certificate',
                                    default => 'fa-question-circle',
                                };
                            @endphp
                            <tr>
                                <td class="text-center align-middle">{{ $loop->index + 1 }}</td>
                                <td class="text-center align-middle">
                                    <img class="rounded shadow-sm zoomable-image"
                                        src="{{ $detalle->estudiante->persona->usuario->url_foto_perfil ? URL::to('/') . '/' . $detalle->estudiante->persona->usuario->url_foto_perfil : URL::to('/') . '/public/img/user.png' }}"
                                        alt="Foto" style="width:35px; height:35px; object-fit:cover;">
                                </td>
                                <td class="align-middle fw-bold">
                                    {{ $detalle->estudiante->persona->apellidos_nombres }}
                                    @if ($detalle->estudiante_licencia)
                                        <div class="mt-1">
                                            <span class="badge bg-info text-dark mb-1">Licencia:
                                                {{ ucwords(str_replace('_', ' ', $detalle->estudiante_licencia->tipo)) }}
                                                -
                                                {{ $detalle->estudiante_licencia->justificacion }}</span><br>
                                            <span class="badge bg-primary">Desde
                                                {{ date('d/m/Y H:i', strtotime($detalle->estudiante_licencia->fecha_inicio)) }}</span>
                                            <span class="badge bg-danger">Hasta
                                                {{ date('d/m/Y H:i', strtotime($detalle->estudiante_licencia->fecha_fin)) }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge bg-secondary curso"
                                        title="{{ $detalle->estudiante->curso->curso }}">
                                        {{ helper_abreviar_curso($detalle->estudiante->curso->curso) }}
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    <span class="badge {{ $badgeClass }} px-3 py-2 fs-6 shadow-sm">
                                        <i class="fa-solid fa-duotone {{ $tipoIcono }} me-1"></i> {{ $tipoTexto }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header p-3">
            <h5 class="fw-bold mb-0 text-secondary">
                <i class="fa-duotone fa-shield-check me-1"></i> Datos de Trazabilidad y Auditoría
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3 fs-7">
                <div class="col-md-2">
                    <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-user-pen me-1"></i> Registrado Por:</p>
                    <p class="mb-0">
                        {{ $estudiante_asistencia->creado->correo }}
                    </p>
                </div>
                <div class="col-md-2">
                    <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-calendar-clock me-1"></i> Fecha y Hora
                        Registro:</p>
                    <p class="mb-0">
                        {{ date('d/m/Y H:i:s', strtotime($estudiante_asistencia->fecha_registro)) }}
                    </p>
                </div>
                <div class="col-md-2">
                    <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-user-pen me-1"></i> Modificado Por:</p>
                    <p class="mb-0">
                        {{ $estudiante_asistencia->modificado?->correo != null ? $estudiante_asistencia->modificado->correo : 'Sin modificar' }}
                    </p>
                </div>
                <div class="col-md-2">
                    <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-calendar-clock me-1"></i> Fecha y Hora
                        Modificación:</p>
                    <p class="mb-0">
                        {{ $estudiante_asistencia->modificado?->correo != null ? date('d/m/Y H:i:s', strtotime($estudiante_asistencia->fecha_actualizacion)) : 'Sin modificar' }}
                    </p>
                </div>
                <div class="col-md-2">
                    <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-network-wired me-1"></i> Dirección IP:</p>
                    <p class="mb-0"><code>{{ $estudiante_asistencia->ip ?? 'No registrada' }}</code></p>
                </div>
                <div class="col-md-2">
                    <p class="mb-1 text-muted fw-bold"><i class="fa-duotone fa-laptop-mobile me-1"></i> Dispositivo:</p>
                    <p class="mb-0 text-truncate" title="{{ $estudiante_asistencia->dispositivo }}">
                        {{ helper_recortar_texto($estudiante_asistencia->dispositivo ?? 'No registrado', 35) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    @include('components.app.img_modal')
@endsection

@section('scripts')
    <style>
        .curso:hover {
            cursor: pointer;
            background-color: #0d6efd !important;
        }

        .fs-7 {
            font-size: 0.875rem;
        }
    </style>
    <script>
        $(document).ready(function() {
            if ($.fn.DataTable.isDataTable('#tabla-detalles-asistencia')) {
                $('#tabla-detalles-asistencia').DataTable().destroy();
            }
            $('#tabla-detalles-asistencia').DataTable({
                @include('components.datatables.datatables_global_properties')
                @include('components.datatables.datatables_language_property')
            });
        });
    </script>
@endsection
