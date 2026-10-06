<div class="card shadow-sm mb-4">
    <div class="card-header p-3">
        <h5 class="fw-bold mb-0 text-primary">
            <i class="fa-duotone fa-filter me-1"></i> Filtros de Búsqueda
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label for="id_nivel" class="form-label text-muted fw-bold">Nivel:</label>
                <select class="form-select" id="filter_id_nivel">
                    <option value="">Todos</option>
                    @foreach ($niveles as $nivel)
                        <option value="{{ $nivel->id_nivel }}">{{ $nivel->nivel }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="id_coordinacion" class="form-label text-muted fw-bold">Coordinación:</label>
                <select class="form-select" id="filter_id_coordinacion">
                    <option value="">Todos</option>
                    @foreach ($coordinaciones as $coordinacion)
                        <option value="{{ $coordinacion->id_coordinacion }}">
                            {{ $coordinacion->coordinacion }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="id_curso" class="form-label text-muted fw-bold">Curso:</label>
                <select class="form-select" id="filter_id_curso">
                    <option value="">Todos</option>
                    @foreach ($cursos as $curso)
                        <option value="{{ $curso->id_curso }}">{{ $curso->curso }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="id_curso" class="form-label text-muted fw-bold">Tipo de calificación:</label>
                <select class="form-select" id="filter_tipo_calificacion">
                    <option value="">Todos</option>
                    <option value="cualitativa">CUALITATIVA</option>
                    <option value="cuantitativa">CUANTITATIVA</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="id_curso" class="form-label text-muted fw-bold">Tipo de bloque:</label>
                <select class="form-select" id="filter_tipo_bloque">
                    <option value="">Todos</option>
                    <option value="curso">CURSO</option>
                    <option value="mixto">MIXTO</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="id_curso" class="form-label text-muted fw-bold">Docente:</label>
                <select class="form-select" id="filter_id_docente">
                    <option value="">Todos</option>
                    @foreach ($docentes as $docente)
                        <option value="{{ $docente->id_docente }}">{{ $docente->persona->nombres_apellidos }}</option>
                    @endforeach
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
