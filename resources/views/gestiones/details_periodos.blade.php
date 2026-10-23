<div class="tab-pane fade show active" id="tab-periodos" role="tabpanel" aria-labelledby="periodos-tab">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold text-info">
            <i class="fa-solid fa-duotone fa-calendar-lines me-2"></i>Periodos Académicos
        </h5>
        <a class="btn btn-primary btn-sm shadow-sm" href="{{ route('periodos.index') }}">
            <i class="fa-solid fa-duotone fa-calendar-alt me-1"></i> Gestionar Periodos
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped dataTable w-100" id="tabla-periodos">
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th>Periodo</th>
                    <th>Posición ordinal</th>
                    <th class="text-center">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($gestion->periodos as $periodo)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->index + 1 }}</td>
                        <td class="align-middle fw-bold">{{ $periodo->periodo }}</td>
                        <td class="align-middle">{{ $periodo->posicion_ordinal }}</td>
                        <td class="text-center align-middle">
                            @php
                                $periodoEstado = match ($periodo->estado) {
                                    0 => 'ARCHIVADO',
                                    1 => 'ACTIVO',
                                    default => 'DESCONOCIDO',
                                };
                                $periodoBadgeClass = match ($periodo->estado) {
                                    0 => 'bg-secondary',
                                    1 => 'bg-success',
                                    default => 'bg-secondary',
                                };
                            @endphp
                            <span class="badge {{ $periodoBadgeClass }}">{{ $periodoEstado }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
