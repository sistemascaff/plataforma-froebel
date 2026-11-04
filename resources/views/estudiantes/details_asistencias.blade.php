<div class="tab-pane fade" id="asistencias-pane" role="tabpanel" aria-labelledby="asistencias-tab">
    <div class="table-responsive pt-2">
        <table class="table table-bordered table-striped dataTable w-100" id="tabla-asistencias">
            <thead>
                <tr>
                    <th class="text-center" scope="col" style="width: 5%;">#</th>
                    <th scope="col" style="width: 15%;">Fecha</th>
                    <th scope="col">Asignatura</th>
                    <th scope="col">Horario</th>
                    <th class="text-center" scope="col" style="width: 20%;">Estado Registrado</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($estudiante->estudiantes_asistencias as $asistencia)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->index + 1 }}</td>
                        <td class="align-middle fw-bold">
                            {{ date('d/m/Y', strtotime($asistencia->fecha)) }}
                        </td>
                        <td class="align-middle">
                            {{ $asistencia->lista_asignatura->asignatura->asignatura }}
                        </td>
                        <td class="align-middle">
                            <span class="text-muted">
                                @if ($asistencia->horario_asignatura)
                                    {{ $asistencia->horario_asignatura->denominacion }}
                                    ({{ date('H:i', strtotime($asistencia->horario_asignatura->hora_inicio)) }})
                                @else
                                    N/A
                                @endif
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            @php
                                $tipo = $asistencia->pivot->tipo;

                                $badgeClass = match ($tipo) {
                                    'PRESENTE' => 'bg-success',
                                    'ATRASO' => 'bg-warning text-dark',
                                    'FALTA' => 'bg-danger',
                                    'LICENCIA' => 'bg-info text-dark',
                                    'PERMISO' => 'bg-primary',
                                    'OTROS' => 'bg-secondary',
                                    default => 'bg-secondary',
                                };

                                $textoTipo = match ($tipo) {
                                    'ATRASO' => $asistencia->pivot->tiempo_atraso
                                        ? 'ATRASO (' . $asistencia->pivot->tiempo_atraso . ' min)'
                                        : 'ATRASO',
                                    default => $tipo,
                                };

                                $icon = match ($tipo) {
                                    'PRESENTE' => '<i class="fa-solid fa-duotone fa-check-circle me-1"></i>',
                                    'ATRASO' => '<i class="fa-solid fa-duotone fa-hourglass-half me-1"></i>',
                                    'FALTA' => '<i class="fa-solid fa-duotone fa-times-circle me-1"></i>',
                                    'LICENCIA' => '<i class="fa-solid fa-duotone fa-file-medical me-1"></i>',
                                    'PERMISO' => '<i class="fa-solid fa-duotone fa-file-signature me-1"></i>',
                                    'OTROS' => '<i class="fa-solid fa-duotone fa-calendar-star me-1"></i>',
                                    default => '<i class="fa-solid fa-duotone fa-question-circle me-1"></i>',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">
                                {!! $icon !!}{{ $textoTipo }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
