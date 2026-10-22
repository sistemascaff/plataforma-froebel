<ul class="nav nav-tabs px-3 pt-2" id="listaAsignaturaTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold py-3 px-4" id="estudiantes-tab" data-bs-toggle="tab"
            data-bs-target="#tab-estudiantes" type="button" role="tab" aria-controls="tab-estudiantes"
            aria-selected="true">
            <i class="fa-solid fa-duotone fa-users-class me-2 text-info"></i>Lista de Estudiantes
            <span class="badge bg-info text-dark ms-2" id="badge-total-estudiantes">
                {{ count($lista_asignatura->estudiantes) }}
            </span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold py-3 px-4" id="asistencias-tab" data-bs-toggle="tab"
            data-bs-target="#tab-asistencias" type="button" role="tab" aria-controls="tab-asistencias"
            aria-selected="false">
            <i class="fa-solid fa-duotone fa-clipboard-list-check me-2 text-success"></i>Asistencias Registradas
            <span class="badge bg-secondary ms-2">
                {{ count($lista_asignatura->estudiantes_asistencias) }}
            </span>
        </button>
    </li>
</ul>
