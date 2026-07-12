@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 border-bottom pb-3">
        <div>
            <h1 class="h2 fw-bold mb-1 text-info">
                <i class="fa-solid fa-duotone fa-books fa-rotate-270 me-2"></i>{{ $head_title }}
            </h1>
            <p class="text-muted mb-0">En esta sección se encuentran todos los préstamos de libros realizados en la
                cooperativa del colegio.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-success" href="{{ route('prestamos_libros.crear') }}">
                <i class="fa-solid fa-duotone fa-plus me-1"></i> Crear préstamo
            </a>
            <a class="btn btn-info text-white" href="{{ route('prestamos_libros.reportes') }}">
                <i class="fa-solid fa-duotone fa-chart-column me-1"></i> Reportes
            </a>
        </div>
    </div>

    <div class="card p-3 mb-4 shadow-sm">
        <div class="row align-items-center mb-3">
            <div class="col-12">
                <h5 class="fw-bold m-0 text-uppercase tracking-wider small text-muted">
                    <i class="fa-solid fa-chart-simple me-2 text-info"></i>Estadísticas de Préstamos
                </h5>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6 col-lg-3 mb-3 mb-lg-0">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="p-3 rounded bg-info bg-opacity-10 text-info me-3">
                            <i class="fa-solid fa-duotone fa-book-bookmark fa-2xl"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-1">Total Registrados</span>
                            <h3 id="cantidad-prestamos-total" class="fw-bold m-0">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mb-3 mb-lg-0">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="p-3 rounded bg-success bg-opacity-10 text-success me-3">
                            <i class="fa-solid fa-duotone fa-circle-check fa-2xl"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-1">Completados</span>
                            <h3 id="cantidad-prestamos-completados" class="fw-bold m-0">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3 mb-3 mb-sm-0">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="p-3 rounded bg-warning bg-opacity-10 text-warning me-3">
                            <i class="fa-solid fa-duotone fa-hourglass-half fa-2xl"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-1">Pendientes / Retraso</span>
                            <h3 id="cantidad-prestamos-pendientes" class="fw-bold m-0">0</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-lg-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body d-flex align-items-center">
                        <div class="p-3 rounded bg-danger bg-opacity-10 text-danger me-3">
                            <i class="fa-solid fa-duotone fa-ban fa-2xl"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block mb-1">Anulados</span>
                            <h3 id="cantidad-prestamos-anulados" class="fw-bold m-0">0</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card p-4 shadow-sm mb-4">
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3 pb-2 border-bottom">
            <div class="text-muted small">
                <i class="fa-solid fa-duotone fa-filter-list me-1 text-info"></i> Herramientas de filtrado y exportación de
                datos
            </div>
            <div id="dataTable-export-buttons-container" class="d-flex flex-wrap gap-1"></div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle w-100" id="dataTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Lector</th>
                        <th>Perfil</th>
                        <th>Curso</th>
                        <th>Celular</th>
                        <th>Libros Prestados</th>
                        <th>F. Devolución Obj.</th>
                        <th>Estado de Entrega</th>
                        <th>Estado Reg.</th>
                        <th>F. Registro</th>
                        <th>F. Actualización</th>
                        <th>F. Eliminación</th>
                        <th>Creado por</th>
                        <th>Modificado por</th>
                        <th>Eliminado por</th>
                        <th>IP</th>
                        <th>Dispositivo</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    @include('prestamos_libros.index_scripts')
@endsection
