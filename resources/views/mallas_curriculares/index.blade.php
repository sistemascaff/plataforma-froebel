@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-object-group"></i> {{ $head_title }}</h1>

    <button type="button" class="btn btn-success mb-3 btn-crear" data-bs-toggle="modal" data-bs-target="#modal-formulario">
        <i class="fa-solid fa-duotone fa-plus"></i> Crear nueva malla curricular</button>

    <h2 class="text-info fw-bold">Lista de mallas curriculares</h2>

    <p class="text-justify">
        Las mallas curriculares son <b>estructuras académicas internas</b> que organizan las materias y áreas de estudio para cada grado y gestión. Estas mallas son fundamentales para la planificación educativa, ya que definen qué materias se imparten en cada grado y cómo se agrupan en áreas temáticas. Es importante destacar que las mallas curriculares no deben confundirse con los planes de estudio oficiales, que son documentos externos aprobados por las autoridades educativas y que pueden contener información adicional como objetivos de aprendizaje, competencias, metodologías de enseñanza, entre otros aspectos. Las mallas curriculares se centran principalmente en la organización interna de las materias y áreas dentro de la institución educativa.
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
                <th>Grado</th>
                <th>Materia interna</th>
                <th>M. Abreviatura</th>
                <th>Área SIE</th>
                <th>A. Abreviatura</th>
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

    @include('mallas_curriculares.modal_form')
@endsection

@section('scripts')
    @include('mallas_curriculares.index_scripts')
@endsection
