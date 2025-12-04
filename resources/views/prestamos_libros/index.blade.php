@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-books fa-rotate-270"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-success mb-3" href="{{ route('prestamos_libros.crear') }}">
        <i class="fa-solid fa-duotone fa-plus"></i> Crear préstamo de libros</a>
    <a class="btn btn-info mb-3" href="{{ route('prestamos_libros.reportes') }}">
        <i class="fa-solid fa-duotone fa-chart-column"></i> Reportes</a>

    <h2 class="text-info fw-bold">Lista de préstamos de libros</h2>

    <h5>En esta sección se encuentran todos los préstamos de libros realizados en la cooperativa del colegio.</h5>

    <div class="card p-3 mb-3">
        <div class="row">
            <div class="col-12">
                <h4 class="text-dark-aquamarine fw-bold">ESTADÍSTICAS</h4>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card info-card shadow-sm border-info">
                    <div class="card-body d-flex align-items-center bg-info bg-opacity-10">
                        <div class="icon-box bg-info bg-opacity-10 me-3">
                            <i class="text-info fa-solid fa-duotone fa-books fa-rotate-270 fa-xl"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Total</h6>
                            <h3 id="cantidad_prestamos_total" class="fw-bold">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card info-card shadow-sm border-success">
                    <div class="card-body d-flex align-items-center bg-success bg-opacity-10">
                        <div class="icon-box bg-success bg-opacity-10 me-3">
                            <i class="text-success fa-solid fa-duotone fa-books fa-rotate-270 fa-xl"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Completados</h6>
                            <h3 id="cantidad_prestamos_completados" class="fw-bold">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card info-card shadow-sm border-primary">
                    <div class="card-body d-flex align-items-center bg-primary bg-opacity-10">
                        <div class="icon-box bg-primary bg-opacity-10 me-3">
                            <i class="text-primary fa-solid fa-duotone fa-books fa-rotate-270 fa-xl"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Pendientes</h6>
                            <h3 id="cantidad_prestamos_pendientes" class="fw-bold">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card info-card shadow-sm border-danger">
                    <div class="card-body d-flex align-items-center bg-danger bg-opacity-10">
                        <div class="icon-box bg-danger bg-opacity-10 me-3">
                            <i class="text-danger fa-solid fa-duotone fa-books fa-rotate-270 fa-xl"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Anulados</h6>
                            <h3 id="cantidad_prestamos_anulados" class="fw-bold">0</h3>
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
                <th>Lector</th>
                <th>Perfil</th>
                <th>Curso</th>
                <th>Celular</th>
                <th>Libros</th>
                <th>F. Devolución objetivo</th>
                <th>Días de retraso</th>
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
@endsection

@section('scripts')
    @include('prestamos_libros.index_scripts')
@endsection
