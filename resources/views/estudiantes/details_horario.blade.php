{{-- Tarjeta de Horario Semanal del Estudiante --}}
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <div class="row align-items-center g-2">
            {{-- Título --}}
            <div class="col-12 col-md-auto me-md-auto text-center text-md-start">
                <h5 class="fw-bold mb-0 text-primary">
                    <i class="fa-duotone fa-calendar-week me-1"></i>Horario Semanal
                </h5>
            </div>

            {{-- Badges de Estado --}}
            <div class="col-12 col-md-auto">
                <div class="d-flex flex-wrap justify-content-center justify-content-md-end gap-2">
                    <span class="badge bg-primary text-nowrap px-2 py-1 shadow-sm fs-7" id="periodo-actual-badge">
                        <i class="fa-solid fa-duotone fa-info-circle me-1"></i>Cargando periodo...
                    </span>
                    <span class="badge bg-info text-dark text-nowrap px-2 py-1 shadow-sm fs-7" id="fecha-actual-badge">
                        <i class="fa-duotone fa-calendar-day me-1"></i>Cargando fecha...
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        {{-- Pestañas (Nav Tabs) --}}
        <ul class="nav nav-tabs nav-tabs-custom" id="tabsHorarioEstudiante" role="tablist">
            <!-- Se renderiza por JS -->
        </ul>

        {{-- Contenido de las Pestañas --}}
        <div class="tab-content mt-4" id="contenidoHorarioEstudiante">
            <div class="text-center py-5" id="cargandoHorarioEstudiante">
                <i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i>
                <p class="mt-2 text-muted fw-bold">Cargando horario semanal...</p>
            </div>
        </div>
    </div>
</div>
