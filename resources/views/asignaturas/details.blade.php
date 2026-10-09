@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-book-open-reader me-2"></i>{{ $head_title ?? 'Detalles de la Asignatura' }}
        </h1>
        <a class="btn btn-secondary shadow-sm" href="{{ route('asignaturas.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left me-1"></i>Volver a <b>Asignaturas</b>
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

    <!-- Listas de la Asignatura -->
    <div class="card shadow-sm mb-4">
        <div class="card-header p-4 border-bottom">
            <h4 class="fw-bold mb-0 text-info">
                <i class="fa-duotone fa-users-class me-2"></i>Listas de la Asignatura
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
                            <th>Cant. Estudiantes</th>
                            <th>Cant. Asistencias</th>
                            <th class="text-center" style="width: 10%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($asignatura->listas_asignaturas as $lista_asignatura)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td><span class="badge bg-light text-dark">{{ $lista_asignatura->periodo->periodo }}</span>
                                </td>
                                <td>{{ $lista_asignatura->periodo->gestion->anio }}</td>
                                <td class="fw-bold text-muted">
                                    {!! $lista_asignatura->docente?->persona->nombres_apellidos == null
                                        ? '<i class="fa-duotone fa-solid fa-exclamation-triangle"></i> '
                                        : '' !!}
                                    {{ $lista_asignatura->docente?->persona->nombres_apellidos ?? 'No asignado' }}
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $lista_asignatura->estudiantes_count > 0 ? 'bg-primary' : 'bg-warning text-dark' }}">
                                        {!! $lista_asignatura->estudiantes_count === 0
                                            ? '<i class="fa-duotone fa-solid fa-exclamation-triangle"></i> '
                                            : '' !!}
                                        {{ $lista_asignatura->estudiantes_count > 0 ? $lista_asignatura->estudiantes_count : ($asignatura->tipo_bloque === 'curso' ? 'Bloque curso: Ingresa para generar estudiantes' : 'Bloque mixto: Ingresa y asigna a los estudiantes') }}
                                    </span>
                                </td>
                                <td>{{ $lista_asignatura->estudiantes_asistencias_count ?? 0 }}</td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a class="btn btn-info btn-sm"
                                            href="{{ route('listas_asignaturas.detalles', $lista_asignatura->id_lista_asignatura) }}"
                                            data-bs-toggle="tooltip" title="Detalles de la lista">
                                            <i class="fa-duotone fa-solid fa-eye"></i>
                                        </a>
                                        {{-- Si el usuario es un docente no puede editar el docente --}}
                                        @if (Auth::user()->persona->tipo_perfil != 'DOCENTE')
                                            <button type="button" class="btn btn-warning btn-sm btn-editar-docente"
                                                data-id-docente="{{ $lista_asignatura->id_docente }}"
                                                data-id-lista="{{ $lista_asignatura->id_lista_asignatura }}"
                                                data-bs-toggle="tooltip" title="Editar docente">
                                                <i class="fa-duotone fa-solid fa-edit"></i>
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

    <!-- Horarios de la Asignatura -->
    <div class="card shadow-sm mb-4">
        <div class="card-header p-4 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold mb-0 text-info">
                <i class="fa-duotone fa-calendar-clock me-2"></i>Horarios de la Asignatura
            </h4>
            <button type="button" class="btn btn-primary shadow-sm" id="btn-guardar-horarios">
                <i class="fa-solid fa-duotone fa-floppy-disk me-1"></i>Guardar cambios
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 text-center align-middle" id="horarios-grid">
                    <thead>
                        <tr>
                            <th style="width: 16%;" class="text-start ps-3"><i class="fa-duotone fa-clock me-1"></i> PERIODO
                            </th>
                            <th style="width: 14%;">LUNES</th>
                            <th style="width: 14%;">MARTES</th>
                            <th style="width: 14%;">MIÉRCOLES</th>
                            <th style="width: 14%;">JUEVES</th>
                            <th style="width: 14%;">VIERNES</th>
                            <th style="width: 14%;">SÁBADO</th>
                        </tr>
                    </thead>
                    <tbody id="horarios-grid-body">
                        {{-- Renderizado dinámico de la cuadrícula desde JS --}}
                        <tr>
                            <td colspan="7" class="py-4 text-muted">
                                <i class="fa-solid fa-spinner fa-spin me-2"></i> Cargando cuadrícula de horarios...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Detalles de la Asignatura -->
    <div class="card shadow-sm mb-4">
        <div class="card-header p-4 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold text-primary">{{ $asignatura->asignatura }}</h4>
            <span class="badge {{ $badgeClass }} px-3 py-2 fs-6 shadow-sm"><i
                    class="fa-duotone fa-circle-check me-1"></i>
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
                        <div class="col-sm-7"><b>{{ $asignatura->materia->abreviatura }}</b> -
                            {{ $asignatura->materia->materia }}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-5 fw-bold text-muted">Área:</div>
                        <div class="col-sm-7"><b>{{ $asignatura->area->abreviatura }}</b> - {{ $asignatura->area->area }}
                        </div>
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
                        <div class="col-sm-7">
                            <span
                                class="badge {{ $asignatura->tipo_bloque === 'curso' ? 'bg-info text-dark' : 'bg-secondary' }}">
                                {{ $asignatura->tipo_bloque === 'curso' ? $asignatura->curso?->curso : 'N/A' }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-5 fw-bold text-muted">Nivel:</div>
                        <div class="col-sm-7">{{ $asignatura->nivel->nivel }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-sm-5 fw-bold text-muted">Coordinación:</div>
                        <div class="col-sm-7">{{ $asignatura->coordinacion?->coordinacion ?? '' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Si el usuario es un docente no renderizar el modal para editar docentes --}}
    @if (Auth::user()->persona->tipo_perfil != 'DOCENTE')
        @include('asignaturas.details_docentes_modal_form')
    @endif
@endsection

@section('scripts')
    @include('asignaturas.details_scripts')
@endsection
