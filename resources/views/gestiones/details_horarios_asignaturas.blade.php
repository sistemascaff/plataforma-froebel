<div class="tab-pane fade" id="tab-horarios" role="tabpanel" aria-labelledby="horarios-tab">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold text-success">
            <i class="fa-solid fa-duotone fa-clock me-2"></i>Horarios de Asignaturas
        </h5>
        <a class="btn btn-primary btn-sm shadow-sm" href="{{ route('horarios_asignaturas.index') }}">
            <i class="fa-solid fa-duotone fa-clock-rotate-left me-1"></i> Gestionar Horarios
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped dataTable w-100" id="tabla-horarios">
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th>Nivel</th>
                    <th>P. ordinal de nivel</th>
                    <th>Denominación</th>
                    <th>Hora de inicio</th>
                    <th>Hora de fin</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($gestion->horarios_asignaturas as $horario_asignatura)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->index + 1 }}</td>
                        <td class="align-middle fw-bold">{{ $horario_asignatura->nivel->nivel }}</td>
                        <td class="align-middle">{{ $horario_asignatura->nivel->posicion_ordinal }}</td>
                        <td class="align-middle">{{ $horario_asignatura->denominacion }}</td>
                        <td class="align-middle">{{ $horario_asignatura->hora_inicio }}</td>
                        <td class="align-middle">{{ $horario_asignatura->hora_fin }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
