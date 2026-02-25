@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-layer-group"></i> {{ $head_title }}</h1>

    <button type="button" class="btn btn-success mb-3 btn-crear" data-bs-toggle="modal" data-bs-target="#modal-formulario">
        <i class="fa-solid fa-duotone fa-plus"></i> Crear dimensión</button>

    <h2 class="text-info fw-bold">Lista de dimensiones</h2>

    <p class="text-justify">
        Las dimensiones son las capas de evaluación que conforman la estructura del registro pedagógico. Cada dimensión tiene un <span class="badge bg-secondary">nombre</span>, una <span class="badge bg-secondary">posición ordinal</span> que determina su orden de aparición, un <span class="badge bg-secondary">puntaje máximo</span> que indica la puntuación máxima asignada a esa dimensión, un <span class="badge bg-secondary">tipo de cálculo</span> que especifica cómo se calculará el puntaje (ya sea sumatoria o promedio) y una gestión a la que pertenece.
    </p>

    <div class="card p-3 mb-3">
        <p>Seleccione una opción para <i class="fa-solid fa-duotone fa-file-export"></i> exportar o <i
                class="fa-solid fa-duotone fa-filter"></i> filtrar la tabla:</p>
        <div id="dataTable-export-buttons-container"></div>
    </div>

    <table class="table table-bordered table-striped" id="dataTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Dimensión</th>
                <th>P. Ordinal</th>
                <th>Puntaje Máximo</th>
                <th>Tipo de Cálculo</th>
                <th>Gestión</th>
                <th>Estado</th>
                <th>F. Registro</th>
                <th>F. Actualización</th>
                <th>F. Archivado</th>
                <th>Creado por</th>
                <th>Modificado por</th>
                <th>Archivado por</th>
                <th>Ip</th>
                <th>Dispositivo</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>

    <div class="mb-3"></div>

    @include('dimensiones.modal_form')
@endsection

@section('scripts')
    @include('dimensiones.index_scripts')
@endsection
