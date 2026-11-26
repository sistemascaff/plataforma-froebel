<div class="card p-3 mb-4 shadow-sm">
    <div class="row align-items-center mb-3">
        <div class="col-12">
            <h5 class="fw-bold m-0 text-uppercase tracking-wider small text-muted">
                <i class="fa-solid fa-chart-simple me-2 text-info"></i>Estadísticas de Incidencias
            </h5>
        </div>
    </div>

    <div class="row g-3">
        <!-- Total Incidencias -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="p-3 rounded bg-secondary bg-opacity-10 me-3">
                        <i class="fa-duotone fa-sigma fa-2xl"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block mb-1">Total Incidencias</span>
                        <h3 id="kpi-total" class="fw-bold m-0 text-secondary">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Atrasos -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="p-3 rounded bg-warning bg-opacity-10 text-warning me-3">
                        <i class="fa-duotone fa-hourglass-half fa-2xl"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block mb-1">Atrasos</span>
                        <h3 id="kpi-atrasos" class="fw-bold m-0 text-warning">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Faltas -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="p-3 rounded bg-danger bg-opacity-10 text-danger me-3">
                        <i class="fa-duotone fa-user-xmark fa-2xl"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block mb-1">Faltas</span>
                        <h3 id="kpi-faltas" class="fw-bold m-0 text-danger">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Licencias -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="p-3 rounded bg-info bg-opacity-10 text-info me-3">
                        <i class="fa-duotone fa-file-certificate fa-2xl"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block mb-1">Licencias</span>
                        <h3 id="kpi-licencias" class="fw-bold m-0 text-info">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Permisos -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="p-3 rounded bg-primary bg-opacity-10 text-primary me-3">
                        <i class="fa-duotone fa-file-signature fa-2xl"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block mb-1">Permisos</span>
                        <h3 id="kpi-permisos" class="fw-bold m-0 text-primary">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suspendidos -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="p-3 rounded bg-dark bg-opacity-10 text-danger border border-danger me-3">
                        <i class="fa-duotone fa-user-slash fa-2xl"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block mb-1">Suspendidos</span>
                        <h3 id="kpi-suspendidos" class="fw-bold m-0 text-danger">0</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Atrasos (Tiempos) -->
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="p-3 rounded bg-secondary bg-opacity-10 me-3">
                        <i class="fa-duotone fa-clock-rotate-left fa-2xl"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block mb-1">Atrasos (Mins)</span>
                        <h4 class="fw-bold m-0">
                            <span id="kpi-minutos-total" class="text-danger">0</span> <span
                                class="fs-6 text-muted fw-normal">min</span>
                        </h4>
                        <span class="fs-7 text-muted d-block">x̄: <span id="kpi-minutos-promedio">0</span> min</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
