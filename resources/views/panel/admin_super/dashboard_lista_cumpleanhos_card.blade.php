<div class="card shadow-sm mb-4">
    <div class="card-header p-3">
        <div class="row align-items-center g-2">
            {{-- Título --}}
            <div class="col-12 col-md-auto me-md-auto text-center text-md-start">
                <h5 class="fw-bold mb-0 text-primary">
                    <i class="fa-duotone fa-cake-candles me-1"></i> Lista de Cumpleaños
                </h5>
            </div>

            {{-- Badges de Estado --}}
            <div class="col-12 col-md-auto">
                <div class="d-flex flex-wrap justify-content-center justify-content-md-end gap-2">
                    <span class="badge bg-info text-dark text-nowrap px-2 py-1 shadow-sm fs-7" id="fecha-actual-badge">
                        <i class="fa-duotone fa-calendar-day me-1"></i>{{ date('d/m/Y') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">

        @include('panel.admin_super.dashboard_lista_cumpleanhos_filters')

        <div class="table-responsive">
            <table class="table table-bordered table-striped dataTable w-100" id="table-cumpleanhos">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Persona</th>
                        <th>Perfil</th>
                        <th>Curso</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
