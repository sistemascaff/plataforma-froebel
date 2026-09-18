@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-book-reader me-2"></i> {{ $head_title }}
        </h1>
    </div>

    <p class="text-justify mb-4">
        En esta sección se encuentran todas las listas de asignaturas registradas en el sistema.
    </p>

    <div class="card shadow-sm mb-4">
        <div class="card-header p-3 d-flex justify-content-between align-items-center">
            <p class="mb-0">Seleccione una opción para <i class="fa-solid fa-duotone fa-file-export mx-1"></i> exportar o
                <i class="fa-solid fa-duotone fa-filter mx-1"></i> filtrar la tabla:
            </p>
            <div id="dataTable-export-buttons-container"></div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped w-100" id="dataTable">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>Asignatura</th>
                            <th>Tipo de calificación</th>
                            <th>Tipo de bloque</th>
                            <th>Periodo</th>
                            <th>Docente</th>
                            <th>Gestión</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('listas_asignaturas.index_scripts')
@endsection
