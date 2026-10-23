<ul class="nav nav-tabs px-3 pt-2" id="gestionTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold py-3 px-3" id="periodos-tab" data-bs-toggle="tab"
            data-bs-target="#tab-periodos" type="button" role="tab" aria-controls="tab-periodos"
            aria-selected="true">
            <i class="fa-solid fa-duotone fa-calendar-lines me-2 text-info"></i>Periodos
            <span class="badge bg-secondary ms-2">{{ count($gestion->periodos) }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold py-3 px-3" id="dimensiones-tab" data-bs-toggle="tab"
            data-bs-target="#tab-dimensiones" type="button" role="tab" aria-controls="tab-dimensiones"
            aria-selected="false">
            <i class="fa-solid fa-duotone fa-layer-group me-2 text-primary"></i>Dimensiones
            <span class="badge bg-secondary ms-2">{{ count($gestion->dimensiones) }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold py-3 px-3" id="mallas-tab" data-bs-toggle="tab" data-bs-target="#tab-mallas"
            type="button" role="tab" aria-controls="tab-mallas" aria-selected="false">
            <i class="fa-solid fa-duotone fa-object-group me-2 text-warning"></i>Mallas Curriculares
            <span class="badge bg-secondary ms-2">{{ count($gestion->mallas_curriculares) }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold py-3 px-3" id="horarios-tab" data-bs-toggle="tab" data-bs-target="#tab-horarios"
            type="button" role="tab" aria-controls="tab-horarios" aria-selected="false">
            <i class="fa-solid fa-duotone fa-clock me-2 text-success"></i>Horarios de Asignaturas
            <span class="badge bg-secondary ms-2">{{ count($gestion->horarios_asignaturas) }}</span>
        </button>
    </li>
</ul>
