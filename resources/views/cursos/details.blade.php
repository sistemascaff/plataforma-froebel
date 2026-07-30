@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-chalkboard-user"></i> {{ $head_title }}
        </h1>
        <a class="btn btn-secondary shadow-sm" href="{{ route('cursos.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-circle-info"></i> Detalles del Curso</h5>
            @php
                $badgeClass = match ($curso->estado) {
                    0 => 'bg-secondary',
                    1 => 'bg-success',
                    default => 'bg-secondary',
                };
                $estadoText = match ($curso->estado) {
                    0 => 'ARCHIVADO',
                    1 => 'ACTIVO',
                    default => 'DESCONOCIDO',
                };
            @endphp
            <span class="badge {{ $badgeClass }} fs-6 shadow-sm">{{ $estadoText }}</span>
        </div>
        <div class="card-body">
            <div class="row g-4">
                <div class="col-md-4">
                    <span class="text-muted d-block fw-bold mb-1">Curso</span>
                    <div class="fs-5">{{ $curso->curso }}</div>
                </div>
                <div class="col-md-4">
                    <span class="text-muted d-block fw-bold mb-1">Grado</span>
                    <div class="fs-5">{{ $curso->grado->grado }}</div>
                </div>
                <div class="col-md-4">
                    <span class="text-muted d-block fw-bold mb-1">Paralelo</span>
                    <div class="fs-5">{{ $curso->paralelo->paralelo }}</div>
                </div>
            </div>
        </div>
    </div>

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
                            <th>Apellidos y Nombres</th>
                            <th>C.I.</th>
                            <th>Correo</th>
                            <th>Fecha Nac.</th>
                            <th>Sexo</th>
                            <th>Tipo de Sangre</th>
                            <th>Alergias</th>
                            <th>Datos Médicos Importantes</th>
                            <th class="text-center" style="width: 10%;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($curso->estudiantes as $estudiante)
                            @if ($estudiante->estado == 1 && $estudiante->persona->estado == 1)
                                <tr>
                                    <td class="text-center">{{ $loop->index + 1 }}</td>
                                    <td class="text-center">
                                        <img class="rounded shadow-sm"
                                            src="{{ isset($estudiante->persona->usuario) && $estudiante->persona->usuario->url_foto_perfil ? URL::to('/') . '/' . $estudiante->persona->usuario->url_foto_perfil : URL::to('/') . '/public/img/user.png' }}"
                                            alt="Foto" style="width:35px; height:35px; object-fit:cover;">
                                    </td>
                                    <td class="align-middle fw-bold">
                                        {{ $estudiante->persona->apellidos_nombres }}
                                    </td>
                                    <td class="align-middle">{{ $estudiante->persona->documento_identificacion }}
                                        {{ $estudiante->persona->documento_expedido }}</td>
                                    <td class="align-middle">
                                        {{ isset($estudiante->persona->usuario) ? $estudiante->persona->usuario->correo : 'Sin correo' }}
                                    </td>
                                    <td class="align-middle">
                                        {{ \Carbon\Carbon::parse($estudiante->persona->fecha_nacimiento)->format('d/m/Y') }}
                                    </td>
                                    <td class="align-middle">{{ $estudiante->persona->sexo }}</td>
                                    <td class="align-middle">{{ $estudiante->salud_tipo_sangre }}</td>
                                    <td class="align-middle">
                                        {{ $estudiante->salud_alergias ? $estudiante->salud_alergias : 'Ninguna' }}</td>
                                    <td class="align-middle">
                                        {{ $estudiante->salud_datos ? $estudiante->salud_datos : 'Ninguna' }}</td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex gap-1 justify-content-center align-items-center">
                                            <a class="btn btn-outline-primary btn-sm"
                                                href="{{ route('estudiantes.detalles', $estudiante->id_estudiante) }}"
                                                target="_blank" rel="noopener noreferrer" data-bs-toggle="tooltip"
                                                title="Ver detalles del estudiante">
                                                <i class="fa-duotone fa-eye"></i>
                                            </a>

                                            <button type="button"
                                                class="btn btn-outline-secondary btn-sm btn-copiar-nombre"
                                                data-nombre="{{ $estudiante->persona->apellidos_nombres }}"
                                                data-bs-toggle="tooltip" title="Copiar nombre completo">
                                                <i class="fa-duotone fa-user-tag"></i>
                                            </button>

                                            @if (isset($estudiante->persona->usuario))
                                                <button type="button" class="btn btn-outline-info btn-sm btn-copiar-correo"
                                                    data-correo="{{ $estudiante->persona->usuario->correo }}"
                                                    data-bs-toggle="tooltip" title="Copiar correo electrónico">
                                                    <i class="fa-duotone fa-envelope"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('cursos.details_scripts')
@endsection
