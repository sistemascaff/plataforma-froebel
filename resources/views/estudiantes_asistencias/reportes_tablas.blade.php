<div class="card shadow-sm mb-4">
    <div class="card-header pb-0 border-bottom-0">
        <ul class="nav nav-tabs card-header-tabs" id="tablasTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-bold text-info" id="btn-tab-incidencias" data-bs-toggle="tab"
                    data-bs-target="#tab-tabla-incidencias" type="button" role="tab">
                    <i class="fa-solid fa-duotone fa-calendar-lines me-1"></i>
                    Detalle de incidencias
                    <span class="badge bg-secondary ms-1" id="badge-count-incidencias">0</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-bold text-info" id="btn-tab-ranking" data-bs-toggle="tab"
                    data-bs-target="#tab-tabla-ranking" type="button" role="tab">
                    <i class="fa-solid fa-duotone fa-ranking-star me-1"></i>
                    Ranking por estudiante
                    <span class="badge bg-info text-dark ms-1" id="badge-count-ranking">0</span>
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="tablasTabsContent">
            {{-- TAB 1: REPORTE DETALLADO DE INCIDENCIAS --}}
            <div class="tab-pane fade show active" id="tab-tabla-incidencias" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped w-100" id="dataTable">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 5%;">#</th>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Curso</th>
                                <th>Estudiante</th>
                                <th>Asignatura</th>
                                <th>Docente</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Atraso</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            {{-- TAB 2: RANKING POR ESTUDIANTE --}}
            <div class="tab-pane fade" id="tab-tabla-ranking" role="tabpanel">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped w-100" id="table-ranking-estudiantes">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 4%;">#</th>
                                <th>Curso</th>
                                <th>Estudiante</th>
                                <th class="text-center">Atrasos</th>
                                <th class="text-center">Faltas</th>
                                <th class="text-center">Suspendidos</th>
                                <th class="text-center table-danger text-danger"
                                    title="Subtotal: Atrasos + Faltas + Suspendidos">
                                    Disciplinarias
                                </th>
                                <th class="text-center">Licencias</th>
                                <th class="text-center">Permisos</th>
                                <th class="text-center table-info text-info" title="Subtotal: Licencias + Permisos">
                                    Justificadas
                                </th>
                                <th class="text-center">Atrasos (Σ)</th>
                                <th class="text-center">Atrasos (x̄)</th>
                                <th class="text-center table-secondary">Total</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
