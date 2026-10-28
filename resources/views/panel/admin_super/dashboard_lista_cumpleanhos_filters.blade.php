<div class="card shadow-sm mb-4">
    <div class="card-header p-3">
        <h5 class="fw-bold mb-0 text-primary">
            <i class="fa-duotone fa-filter me-1"></i> Filtros de Búsqueda
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <!-- Nuevo Filtro Periodo -->
            <div class="col-md-3">
                <label for="filter_periodo" class="form-label text-muted fw-bold">Periodo:</label>
                <select class="form-select" id="filter_periodo">
                    <option value="hoy">Hoy</option>
                    <option value="esta_semana">Esta Semana</option>
                    <option value="este_mes">Este Mes</option>
                </select>
            </div>

            <!-- Filtro Perfil Existente -->
            <div class="col-md-3">
                <label for="filter_tipo_perfil" class="form-label text-muted fw-bold">Tipo de perfil:</label>
                <select class="form-select" id="filter_tipo_perfil">
                    <option value="">Todos</option>
                    <option value="DOCENTE">DOCENTE</option>
                    <option value="ESTUDIANTE">ESTUDIANTE</option>
                </select>
            </div>

            <!-- Nuevo Switch Personal -->
            <div class="col-md-4">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="filter_solo_personal" role="switch" checked>
                    <label class="form-check-label fw-bold text-muted" for="filter_solo_personal">
                        Mostrar solo personal de trabajo
                    </label>
                </div>
            </div>

            <div class="col-md-2">
                <button type="button" class="btn btn-primary w-100 shadow-sm" id="btn-filtrar">
                    <i class="fa-duotone fa-search me-1"></i> Filtrar
                </button>
            </div>
        </div>
    </div>
</div>
