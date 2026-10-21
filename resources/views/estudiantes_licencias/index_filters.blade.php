<div class="card shadow-sm mb-4">
    <div class="card-header p-3">
        <h5 class="fw-bold mb-0 text-primary">
            <i class="fa-duotone fa-filter me-1"></i> Filtros de Búsqueda
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="filter_id_nivel" class="form-label text-muted fw-bold">Nivel:</label>
                <select class="form-select" id="filter_id_nivel">
                    <option value="">Todos</option>
                    @foreach ($niveles as $nivel)
                        <option value="{{ $nivel->id_nivel }}">{{ $nivel->nivel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_id_grado" class="form-label text-muted fw-bold">Grado:</label>
                <select class="form-select" id="filter_id_grado">
                    <option value="">Todos</option>
                    @foreach ($grados as $grado)
                        <option value="{{ $grado->id_grado }}">{{ $grado->grado }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_id_curso" class="form-label text-muted fw-bold">Curso:</label>
                <select class="form-select" id="filter_id_curso">
                    <option value="">Todos</option>
                    @foreach ($cursos as $curso)
                        <option value="{{ $curso->id_curso }}">{{ $curso->curso }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_id_paralelo" class="form-label text-muted fw-bold">Paralelo:</label>
                <select class="form-select" id="filter_id_paralelo">
                    <option value="">Todos</option>
                    <option value="1">A (ROT)</option>
                    <option value="2">B (WEISS)</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filter_estado" class="form-label text-muted fw-bold">Estado:</label>
                <select class="form-select" id="filter_estado">
                    <option value="">Todos</option>
                    <option value="1" selected>Activo</option>
                    <option value="0">Archivado</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="button" class="btn btn-primary w-100 shadow-sm" id="btn-filtrar">
                    <i class="fa-duotone fa-search me-1"></i> Filtrar
                </button>
            </div>
        </div>
    </div>
</div>
