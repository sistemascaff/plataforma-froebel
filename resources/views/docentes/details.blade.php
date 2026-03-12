@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-chalkboard-user"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-secondary mb-3" href="{{ route('docentes.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver</a>

    <div class="col-12 col-md-12 col-lg-12 mb-3 d-flex justify-content-center">
        <img class="rounded" style="max-width: 200px;" alt="Foto de perfil"
            src="{{ URL::to('/') }}/{{ $docente->persona->usuario->url_foto_perfil }}">
    </div>

    <div class="row">
        <div class="col-12 col-md-6 col-lg-6">
            <h2 class="text-info fw-bold">Datos personales</h2>

            <label for="apellido_paterno">Apellido paterno:</label>
            <p class="form-control mb-3" id="apellido_paterno">
                {{ $docente->persona->apellido_paterno }}
            </p>

            <label for="apellido_materno">Apellido materno:</label>
            <p class="form-control mb-3" id="apellido_materno">
                {{ $docente->persona->apellido_materno }}
            </p>

            <label for="nombres">Nombres:</label>
            <p class="form-control mb-3" id="nombres">
                {{ $docente->persona->nombres }}
            </p>

            <label for="documento_identificacion">C.I.:</label>
            <p class="form-control mb-3" id="documento_identificacion">
                {{ $docente->persona->documento_identificacion }}
            </p>

            <label for="documento_complemento">C.I. Complemento:</label>
            <p class="form-control mb-3" id="documento_complemento">
                {{ $docente->persona->documento_complemento ?: '-' }}
            </p>

            <label for="documento_expedido">C.I. Expedido:</label>
            <p class="form-control mb-3" id="documento_expedido">
                {{ $docente->persona->documento_expedido }}
            </p>

            <label for="fecha_nacimiento">Fecha de nacimiento:</label>
            <p class="form-control mb-3" id="fecha_nacimiento">
                {{ date('d/m/Y', strtotime($docente->persona->fecha_nacimiento)) }}
            </p>

            <label for="sexo">Sexo:</label>
            <p class="form-control mb-3" id="sexo">
                {{ $docente->persona->sexo == 'M' ? 'MASCULINO' : 'FEMENINO' }}
            </p>

            <label for="idioma">Idioma:</label>
            <p class="form-control mb-3" id="idioma">
                {{ $docente->persona->idioma }}
            </p>

            <label for="celular">Celular:</label>
            <p class="form-control mb-3" id="celular">
                {{ $docente->persona->celular }}
            </p>

            <label for="telefono">Teléfono:</label>
            <p class="form-control mb-3" id="telefono">
                {{ $docente->persona->telefono }}
            </p>

            <label for="tipo_perfil">Tipo de perfil:</label>
            <p class="form-control mb-3" id="tipo_perfil">
                {{ $docente->persona->tipo_perfil }}
            </p>
        </div>

        <div class="col-12 col-md-6 col-lg-6">
            <h2 class="text-info fw-bold">Datos de acceso</h2>

            <label for="correo">Correo:</label>
            <p class="form-control mb-3 fw-bold bg-info text-dark" id="correo">
                {{ $docente->persona->usuario->correo }}
            </p>

            @if (session('tipo_perfil') === 'ADMIN')
                <label for="contrasenha">Contraseña:</label>
                <b><i class="fa-solid fa-duotone fa-circle-info"></i> Este dato solo es visible para el tipo de perfil <span class="text-primary">ADMIN</span></b>
                <p class="form-control mb-3 fw-bold bg-warning text-dark" id="contrasenha">
                    {{ helper_decrypt($docente->persona->usuario->contrasenha) }}
                </p>
            @endif

            <label for="tiene_acceso">Tiene acceso:</label>
            <p class="form-control mb-3 fw-bold {{ $docente->persona->usuario->tiene_acceso ? 'bg-success' : 'bg-danger' }}" id="tiene_acceso">
                {{ $docente->persona->usuario->tiene_acceso ? 'SI' : 'NO' }}
            </p>

            <h2 class="text-info fw-bold">Datos del docente</h2>

            <label for="nivel">Responsable de nivel:</label>
            <p class="form-control mb-3" id="nivel">
                {{ $docente->nivel?->nivel ?: '-' }}
            </p>

            <label for="coordinacion">Responsable de coordinacion:</label>
            <p class="form-control mb-3" id="coordinacion">
                {{ $docente->coordinacion?->coordinacion ?: '-' }}
            </p>

            <label for="especialidad">Especialidad:</label>
            <p class="form-control mb-3" id="especialidad">
                {{ $docente->especialidad ?: '-' }}
            </p>

            <label for="grado_estudios">Grado de estudios:</label>
            <p class="form-control mb-3" id="grado_estudios">
                {{ $docente->grado_estudios }}
            </p>

            <label for="domicilio">domicilio:</label>
            <p class="form-control mb-3" id="domicilio">
                {{ $docente->domicilio }}
            </p>
        </div>
    </div>

    @php
        $estado = match ($docente->estado) {
            0 => 'ARCHIVADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $class = match ($docente->estado) {
            0 => 'alert alert-secondary',
            1 => 'alert alert-success',
            default => 'alert alert-secondary',
        };
    @endphp

    <div class="{{ $class }} fw-bold mb-3">
        Estado: {{ $estado }}
    </div>

    
    <div class="mb-3"></div>
    
    <h2 class="text-info fw-bold">Listas de asignaturas</h2>
    
    <table class="table table-bordered table-striped mb-3 dataTable" id="materias">
        <thead>
            <tr>
                <th>#</th>
                <th>Asignatura</th>
                <th>Periodo</th>
                <th>Gestión</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($docente->listas_asignaturas as $lista_asignatura)
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $lista_asignatura->asignatura->asignatura }}</td>
                <td>{{ $lista_asignatura->periodo->periodo }}</td>
                <td>{{ $lista_asignatura->periodo->gestion->anio }}</td>
                <td>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mb-3"></div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $(".dataTable").DataTable({
                @include('components.datatables.datatables_global_properties')
                @include('components.datatables.datatables_language_property')
            });
        });
    </script>
@endsection
