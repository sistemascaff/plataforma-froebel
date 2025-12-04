@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-book-open"></i> {{ $head_title }}</h1>

    <button type="button" class="btn btn-success mb-3 btn-crear" data-bs-toggle="modal" data-bs-target="#modal_form">
        <i class="fa-solid fa-duotone fa-plus"></i> Crear libro</button>

    <h2 class="text-info fw-bold">Lista de libros</h2>

    <h5>En esta sección se encuentran todos los libros registrados en la cooperativa del colegio.</h5>

    <h5>LEYENDA DE <b>ESTADO</b>:</h5>

    <p>
        <span class="badge bg-success">DISPONIBLE</span>: El libro está disponible para su préstamo.<br>
        <span class="badge bg-primary">EN USO</span>: El libro está actualmente prestado a una persona.<br>
        <span class="badge bg-secondary">ELIMINADO</span>: El libro ha sido dado de baja del inventario.<br>
    </p>

    <div class="card p-3 mb-3">
        <div class="row">
            <div class="col-12">
                <h4 class="text-dark-aquamarine fw-bold">ESTADÍSTICAS</h4>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card info-card shadow-sm border-info">
                    <div class="card-body d-flex align-items-center bg-info bg-opacity-10">
                        <div class="icon-box bg-info bg-opacity-10 me-3">
                            <i class="text-info fa-solid fa-duotone fa-book-open fa-xl"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Total</h6>
                            <h3 id="cantidad_libros_total" class="fw-bold">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card info-card shadow-sm border-success">
                    <div class="card-body d-flex align-items-center bg-success bg-opacity-10">
                        <div class="icon-box bg-success bg-opacity-10 me-3">
                            <i class="text-success fa-solid fa-duotone fa-book-open fa-xl"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Disponibles</h6>
                            <h3 id="cantidad_libros_disponibles" class="fw-bold">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card info-card shadow-sm border-primary">
                    <div class="card-body d-flex align-items-center bg-primary bg-opacity-10">
                        <div class="icon-box bg-primary bg-opacity-10 me-3">
                            <i class="text-primary fa-solid fa-duotone fa-book-open fa-xl"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Prestados</h6>
                            <h3 id="cantidad_libros_prestados" class="fw-bold">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card info-card shadow-sm border-secondary">
                    <div class="card-body d-flex align-items-center bg-secondary bg-opacity-10">
                        <div class="icon-box bg-secondary bg-opacity-10 me-3">
                            <i class="text-secondary fa-solid fa-duotone fa-book-open fa-xl"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Bajas</h6>
                            <h3 id="cantidad_libros_eliminados" class="fw-bold">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-3 mb-3">
        <p>Seleccione una opción para <i class="fa-solid fa-duotone fa-file-export"></i> exportar o <i
                class="fa-solid fa-duotone fa-filter"></i> filtrar la tabla:</p>
        <div id="datatable_export_buttons_container"></div>
    </div>

    <table class="table table-bordered table-striped" id="dataTable">
        <thead>
            <tr>
                <th>#</th>
                <th>Categoria</th>
                <th>Código</th>
                <th>Título</th>
                <th>Autor</th>
                <th>Editorial</th>
                <th>Año</th>
                <th>Descripción</th>
                <th>Costo</th>
                <th>Adquisición</th>
                <th>Presentación</th>
                <th>Observación</th>
                <th>F. Ingreso Cooperativa</th>
                <th>Prestado a</th>
                <th>Cant. Préstamos</th>
                <th>Estado</th>
                <th>F. Registro</th>
                <th>F. Actualización</th>
                <th>F. Eliminación</th>
                <th>Creado por</th>
                <th>Modificado por</th>
                <th>Eliminado por</th>
                <th>Ip</th>
                <th>Dispositivo</th>
                <th>Acciones</th>
            </tr>
        </thead>
    </table>

    <div class="mb-3"></div>

    @include('libros.modal_form')
@endsection

@section('scripts')
    @include('libros.index_scripts')
@endsection
