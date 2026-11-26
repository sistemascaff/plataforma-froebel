<div class="card shadow-sm mb-4">
    <div class="card-header p-3">
        <h5 class="fw-bold mb-0 text-primary">
            <i class="fa-duotone fa-filter me-1"></i>Filtros de Búsqueda
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="filter_rango_fechas" class="form-label text-muted fw-bold">Rango de fechas:</label>
                <select class="form-select" id="filter_rango_fechas">
                    <option value="">Todo</option>
                    <option value="hoy" selected>Hoy</option>
                    <option value="esta_semana">Esta Semana</option>
                    <option value="este_mes">Este Mes</option>
                    <option value="personalizado">Personalizado</option>
                </select>
            </div>
            <div class="col-md-3 d-none" id="filter_fecha_inicio_container">
                <label for="fecha_inicio" class="form-label text-muted fw-bold">Desde Fecha:</label>
                <input type="date" class="form-control" id="fecha_inicio" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3 d-none" id="filter_fecha_fin_container">
                <label for="fecha_fin" class="form-label text-muted fw-bold">Hasta Fecha:</label>
                <input type="date" class="form-control" id="fecha_fin" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-3">
                <label for="id_nivel" class="form-label text-muted fw-bold">Nivel:</label>
                <select class="form-select" id="id_nivel">
                    <option value="">Todos</option>
                    @if (isset($niveles))
                        @foreach ($niveles as $nivel)
                            <option value="{{ $nivel->id_nivel }}">{{ $nivel->nivel }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3">
                <label for="id_coordinacion" class="form-label text-muted fw-bold">Coordinación:</label>
                <select class="form-select" id="id_coordinacion">
                    <option value="">Todos</option>
                    @if (isset($coordinaciones))
                        @foreach ($coordinaciones as $coordinacion)
                            <option value="{{ $coordinacion->id_coordinacion }}">{{ $coordinacion->coordinacion }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3">
                <label for="id_curso" class="form-label text-muted fw-bold">Curso:</label>
                <select class="form-select" id="id_curso">
                    <option value="">Todos</option>
                    @if (isset($cursos))
                        @foreach ($cursos as $curso)
                            <option value="{{ $curso->id_curso }}">{{ $curso->curso }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3">
                <label for="id_docente" class="form-label text-muted fw-bold">Docente:</label>
                <select class="form-select" id="id_docente">
                    <option value="">Todos</option>
                    @if (isset($docentes))
                        @foreach ($docentes as $docente)
                            <option value="{{ $docente->id_docente }}">{{ $docente->persona?->nombres_apellidos }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-primary w-100 shadow-sm" id="btn-filtrar">
                    <i class="fa-duotone fa-search me-1"></i>Filtrar
                </button>
            </div>
        </div>

        {{-- FILTRO REACTIVO POR ESTADOS --}}
        <div class="border-top pt-3 mt-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                <span class="text-muted fw-bold small text-uppercase">
                    <i class="fa-duotone fa-sliders me-1"></i>Estados visibles:
                </span>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary" id="btn-estados-todos">
                        <i class="fa-solid fa-check-double me-1"></i>Todos
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="btn-estados-ninguno">
                        <i class="fa-solid fa-xmark me-1"></i>Ninguno
                    </button>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-3 align-items-center">
                <div class="form-check form-switch m-0">
                    <input class="form-check-input filter-estado" type="checkbox" role="switch" id="chk-estado-atraso"
                        value="ATRASO" checked>
                    <label class="form-check-label" for="chk-estado-atraso">
                        <span class="badge bg-warning text-dark shadow-sm">
                            <i class="fa-solid fa-duotone fa-hourglass-half me-1"></i>Atrasos
                        </span>
                    </label>
                </div>

                <div class="form-check form-switch m-0">
                    <input class="form-check-input filter-estado" type="checkbox" role="switch" id="chk-estado-falta"
                        value="FALTA" checked>
                    <label class="form-check-label" for="chk-estado-falta">
                        <span class="badge bg-danger shadow-sm">
                            <i class="fa-solid fa-duotone fa-times-circle me-1"></i>Faltas
                        </span>
                    </label>
                </div>

                <div class="form-check form-switch m-0">
                    <input class="form-check-input filter-estado" type="checkbox" role="switch"
                        id="chk-estado-licencia" value="LICENCIA" checked>
                    <label class="form-check-label" for="chk-estado-licencia">
                        <span class="badge bg-info text-dark shadow-sm">
                            <i class="fa-solid fa-duotone fa-file-certificate me-1"></i>Licencias
                        </span>
                    </label>
                </div>

                <div class="form-check form-switch m-0">
                    <input class="form-check-input filter-estado" type="checkbox" role="switch"
                        id="chk-estado-permiso" value="PERMISO" checked>
                    <label class="form-check-label" for="chk-estado-permiso">
                        <span class="badge bg-primary shadow-sm">
                            <i class="fa-solid fa-duotone fa-file-signature me-1"></i>Permisos
                        </span>
                    </label>
                </div>

                <div class="form-check form-switch m-0">
                    <input class="form-check-input filter-estado" type="checkbox" role="switch"
                        id="chk-estado-suspendido" value="SUSPENDIDO" checked>
                    <label class="form-check-label" for="chk-estado-suspendido">
                        <span class="badge bg-dark text-light border border-danger shadow-sm">
                            <i class="fa-solid fa-duotone fa-user-slash me-1"></i>Suspendidos
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
