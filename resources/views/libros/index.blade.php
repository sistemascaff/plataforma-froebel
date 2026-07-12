@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h1 class="text-info fw-bold mb-0 me-auto">
            <i class="fa-solid fa-duotone fa-book-open me-2"></i>{{ $head_title }}
        </h1>
        <button type="button" class="btn btn-success btn-crear" data-bs-toggle="modal" data-bs-target="#modal-formulario">
            <i class="fa-solid fa-duotone fa-plus me-1"></i> Crear libro
        </button>
    </div>

    <div class="alert alert-info border-info border-start border-4 shadow-sm mb-4">
        <div class="d-flex align-items-start">
            <i class="fa-solid fa-duotone fa-circle-info fa-xl me-3 mt-1 text-info align-self-center"></i>
            <div>
                <h5 class="alert-heading fw-bold mb-2">Lista de Libros Registrados</h5>
                <p class="mb-2">En esta sección se encuentran todos los libros registrados en la cooperativa del colegio.
                    Puedes verificar su disponibilidad actual mediante los siguientes estados:</p>
                <div class="d-flex flex-wrap gap-3 small fw-bold">
                    <div><span class="badge bg-success px-2 py-1 me-1">DISPONIBLE</span> El libro está libre para su
                        préstamo.</div>
                    <div><span class="badge bg-primary px-2 py-1 me-1">EN USO</span> El libro está actualmente prestado a
                        una persona.</div>
                    <div><span class="badge bg-secondary px-2 py-1 me-1">ELIMINADO</span> El libro ha sido dado de baja del
                        inventario.</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info"><i class="fa-solid fa-duotone fa-chart-simple me-2"></i> Estadísticas de
                Libros</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6 col-lg-3 mb-3">
                    <div class="card info-card shadow-sm border-info">
                        <div class="card-body d-flex align-items-center bg-info bg-opacity-10">
                            <div class="icon-box bg-info bg-opacity-10 me-3">
                                <i class="text-info fa-solid fa-duotone fa-book-open fa-xl"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1 small">Total</h6>
                                <h3 id="cantidad-libros-total" class="fw-bold">0</h3>
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
                                <h3 id="cantidad-libros-disponibles" class="fw-bold">0</h3>
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
                                <h3 id="cantidad-libros-prestados" class="fw-bold">0</h3>
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
                                <h3 id="cantidad-libros-eliminados" class="fw-bold">0</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex flex-wrap align-items-center gap-2 py-3">
            <p class="mb-0 text-muted fw-bold me-2">
                <i class="fa-solid fa-duotone fa-file-export me-1"></i> Exportar o <i
                    class="fa-solid fa-duotone fa-filter me-1"></i> Filtrar la tabla:
            </p>
            <div id="dataTable-export-buttons-container" class="flex-grow-1"></div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0 fw-bold text-info"><i class="fa-solid fa-duotone fa-table-list me-2"></i> Inventario de la
                Biblioteca</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table class="table table-bordered table-striped w-100 mb-0" id="dataTable">
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
            </div>
        </div>
    </div>

    @include('libros.modal_form')
@endsection

@section('scripts')
    @include('libros.index_scripts')
@endsection
