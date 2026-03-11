@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-star"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-secondary mb-3" href="{{ route('asignaturas.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver</a>

    <div></div>

    <label for="asignatura">Asignatura:</label>
    <p class="form-control mb-3" id="asignatura">
        {{ $asignatura->asignatura }}
    </p>

    <label for="tipo_calificacion">Tipo de calificación:</label>
    <p class="form-control mb-3" id="tipo_calificacion">
        {{ strtoupper($asignatura->tipo_calificacion) }}
    </p>

    <label for="tipo_bloque">Tipo de bloque:</label>
    <p class="form-control mb-3" id="tipo_bloque">
        {{ strtoupper($asignatura->tipo_bloque) }}
    </p>

    <label for="materia">Materia:</label>
    <p class="form-control mb-3" id="materia">
        {{ $asignatura->materia->abreviatura }} - {{ $asignatura->materia->materia }}
    </p>

    <label for="area">Área:</label>
    <p class="form-control mb-3" id="area">
        {{ $asignatura->area->abreviatura }} - {{ $asignatura->area->area }}
    </p>

    <label for="aula">Aula:</label>
    <p class="form-control mb-3" id="aula">
        {{ $asignatura->aula->aula }}
    </p>

    <label for="nivel">Nivel:</label>
    <p class="form-control mb-3" id="nivel">
        {{ $asignatura->nivel->nivel }}
    </p>

    <label for="coordinacion">Coordinación:</label>
    <p class="form-control mb-3" id="coordinacion">
        {{ $asignatura->coordinacion?->coordinacion ?? 'N/A' }}
    </p>

    @php
        $estado = match ($asignatura->estado) {
            0 => 'ARCHIVADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $class = match ($asignatura->estado) {
            0 => 'alert alert-secondary',
            1 => 'alert alert-success',
            default => 'alert alert-secondary',
        };
    @endphp

    <div class="{{ $class }} fw-bold mb-3">
        Estado: {{ $estado }}
    </div>

    <div class="accordion" id="bootstrap-acordeon">
        <div class="accordion-item">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <b>ASIGNAR HORARIOS DISPONIBLES</b>
                </button>
            </h2>
            <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#bootstrap-acordeon">
                <div class="accordion-body">

                    <div class="mb-3">
                        <label for="dia_semana" class="form-label">Día de la semana <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="dia_semana" name="dia_semana" required>
                            <option value="" disabled selected>Selecciona un día de la semana</option>
                            <option value="1">LUNES</option>
                            <option value="2">MARTES</option>
                            <option value="3">MIÉRCOLES</option>
                            <option value="4">JUEVES</option>
                            <option value="5">VIERNES</option>
                            <option value="6">SÁBADO</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="horario" class="form-label">Horario <span class="text-danger">*</span></label>
                        <p class="text-info">Se omite los horarios que no sean del nivel de la asignatura, que estén
                            inactivos o que la denominación del horario contenga "RECESO" o "RECREO"</p>
                        <select class="form-select" id="horario" name="horario" required>
                        </select>
                    </div>

                    <button type="button" class="btn btn-success" id="btn-agregar-horario">
                        <i class="fa-solid fa-duotone fa-plus"></i> Agregar horario
                    </button>

                </div>
            </div>
        </div>
    </div>

    <h2 class="text-info fw-bold mt-3">Horarios de la asignatura</h2>

    <table class="table table-bordered table-striped mb-3 dataTable" id="horarios">
        <thead>
            <tr>
                <th>#</th>
                <th>Día</th>
                <th>Denominación</th>
                <th>Hora de inicio</th>
                <th>Hora de fin</th>
                <th>Gestión</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            {{-- Las filas existentes se renderizan desde JS para que el estado (pendiente borrar) sea consistente --}}
        </tbody>
    </table>

    <div class="mb-3"></div>

    <button type="button" class="btn btn-primary" id="btn-guardar-horarios">
        <i class="fa-solid fa-duotone fa-floppy-disk"></i> Guardar
    </button>

    <div class="mb-3"></div>

    <h2 class="text-info fw-bold mt-3">Listas de la asignatura</h2>

    <table class="table table-bordered table-striped mb-3 dataTable" id="listas">
        <thead>
            <tr>
                <th>#</th>
                <th>Periodo</th>
                <th>Gestión</th>
                <th>Docente</th>
                <th>Acciones</th>
            </tr>
        </thead>
        @foreach ($asignatura->listas_asignaturas as $lista_asignatura)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $lista_asignatura->periodo->periodo }}</td>
                <td>{{ $lista_asignatura->periodo->gestion->anio }}</td>
                <td>{{ trim($lista_asignatura->docente?->persona->apellido_paterno . ' ' . $lista_asignatura->docente?->persona->apellido_materno . ' ' . $lista_asignatura->docente?->persona->nombres) }}
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <a class="btn btn-info btn-sm" href="#" target="_blank" rel="noopener noreferrer"
                            data-toggle="tooltip" title="Detalles de la lista de asignatura">
                            <i class="fa-duotone fa-solid fa-eye"></i>
                        </a>
                        <button type="button" class="btn btn-warning btn-sm btn-editar-docente"
                            data-id-docente="{{ $lista_asignatura->id_docente }}"
                            data-id-lista="{{ $lista_asignatura->id_lista_asignatura }}"
                            data-toggle="tooltip" title="Editar docente">
                            <i class="fa-duotone fa-solid fa-edit"></i>
                        </button>
                    </div>
                    {{ $lista_asignatura->id_lista_asignatura }}
                </td>
                </td>
            </tr>
        @endforeach
    </table>

    <div class="mb-3"></div>

    @include('asignaturas.details_docentes_modal_form')
@endsection

@section('scripts')
    @include('asignaturas.details_scripts')
@endsection
