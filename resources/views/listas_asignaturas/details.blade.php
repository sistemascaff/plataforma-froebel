@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-book-open-reader me-2"></i> {{ $head_title ?? 'Lista de estudiantes' }}
        </h1>
        <a class="btn btn-secondary shadow-sm" href="{{ route('asignaturas.detalles', $lista_asignatura->id_asignatura) }}">
            <i class="fa-solid fa-duotone fa-arrow-left me-1"></i> Volver
        </a>
    </div>

    @if (!empty($cambios) && count($cambios) > 0)
        <div class="mb-3">
            <button type="button" class="btn btn-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCambios">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>Ver cambios recientes en la lista
            </button>
        </div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-header border-bottom-0 p-4">
            <h5 class="card-title mb-0"><i class="fa-solid fa-users me-2"></i>Estudiantes</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 dataTable" id="estudiantes">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 5%;">#</th>
                            <th>Estudiante</th>
                            <th>Correo</th>
                            <th>Curso</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lista_asignatura->estudiantes as $estudiante)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td>{{ trim($estudiante->persona->apellido_paterno . ' ' . $estudiante->persona->apellido_materno . ' ' . $estudiante->persona->nombres) }}
                                </td>
                                <td>{{ $estudiante->persona->usuario->correo }}</td>
                                <td>{{ $estudiante->curso->curso }}</td>
                                <td>
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
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-warning">
                        <h1 class="modal-title fs-5 text-dark" id="modalCambiosLabel">
                            <i class="fa-solid fa-rotate me-2"></i>Actualización Automática de Lista
                        </h1>
                    </div>
                    <div class="modal-body pb-1">
                        <p class="text-muted mb-3">El sistema ha sincronizado automáticamente esta lista de acuerdo a los
                            estudiantes vigentes del curso:</p>

                        <div class="bg-body-tertiary p-3 rounded mb-3">
                            @foreach ($cambios as $cambio)
                                {!! $cambio !!}
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa-solid fa-check me-2"></i>Entendido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endsection

@section('scripts')
    <script>
        // ─── DataTable ────────────────────────────────────────────────────────────
        $(".dataTable").DataTable({
            @include('components.datatables.datatables_global_properties')
            @include('components.datatables.datatables_language_property')
        });

        // ─── Disparar Modal de Cambios Automáticamente ────────────────────────────
        @if (!empty($cambios) && count($cambios) > 0)
            $(document).ready(function() {
                // Instanciar y mostrar el modal usando la API de Bootstrap 5
                const modalCambios = new bootstrap.Modal(document.getElementById('modalCambios'));
                modalCambios.show();
            });
        @endif
    </script>
@endsection
