<ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active fw-bold text-info" data-bs-toggle="tab" data-bs-target="#tab-proporcion"
            type="button" role="tab">
            <i class="fa-solid fa-chart-pie"></i> General
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-info" data-bs-toggle="tab" data-bs-target="#tab-fechas" type="button"
            role="tab">
            <i class="fa-solid fa-chart-line"></i> Fechas
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-info" data-bs-toggle="tab" data-bs-target="#tab-horarios" type="button"
            role="tab">
            <i class="fa-solid fa-chart-column"></i> Horarios
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-info" data-bs-toggle="tab" data-bs-target="#tab-ranking" type="button"
            role="tab">
            <i class="fa-solid fa-user-graduate"></i> Estudiantes
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-info" data-bs-toggle="tab" data-bs-target="#tab-cursos" type="button"
            role="tab">
            <i class="fa-solid fa-chalkboard-user"></i> Cursos
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-info" data-bs-toggle="tab" data-bs-target="#tab-asignaturas" type="button"
            role="tab">
            <i class="fa-solid fa-books"></i> Asignaturas
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link fw-bold text-info" data-bs-toggle="tab" data-bs-target="#tab-docentes" type="button"
            role="tab">
            <i class="fa-solid fa-user-tie"></i> Docentes
        </button>
    </li>
</ul>

<div class="tab-content border border-top-0 p-4 mb-4 shadow-sm rounded-bottom bg-body" id="dashboardTabsContent">
    <div class="tab-pane fade show active" id="tab-proporcion" role="tabpanel">
        <div style="position: relative; height: 350px; width: 100%;">
            <canvas id="chart-proporcion"></canvas>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-fechas" role="tabpanel">
        <div style="position: relative; height: 350px; width: 100%;">
            <canvas id="chart-fechas"></canvas>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-horarios" role="tabpanel">
        <div style="position: relative; height: 350px; width: 100%;">
            <canvas id="chart-horarios"></canvas>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-ranking" role="tabpanel">
        <div style="position: relative; height: 350px; width: 100%;">
            <canvas id="chart-ranking"></canvas>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-cursos" role="tabpanel">
        <div style="position: relative; height: 350px; width: 100%;">
            <canvas id="chart-cursos"></canvas>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-asignaturas" role="tabpanel">
        <div style="position: relative; height: 350px; width: 100%;">
            <canvas id="chart-asignaturas"></canvas>
        </div>
    </div>
    <div class="tab-pane fade" id="tab-docentes" role="tabpanel">
        <div style="position: relative; height: 350px; width: 100%;">
            <canvas id="chart-docentes"></canvas>
        </div>
    </div>
</div>
