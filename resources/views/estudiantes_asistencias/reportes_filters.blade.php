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
    </div>
</div>
