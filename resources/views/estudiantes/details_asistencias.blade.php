<div class="tab-pane fade" id="asistencias-pane" role="tabpanel" aria-labelledby="asistencias-tab" tabindex="0">
    <div class="table-responsive pt-2">
        <table class="table table-bordered table-striped dataTable w-100" id="tabla-asistencias">
            <thead>
                <tr>
                    <th class="text-center" scope="col" style="width: 5%;">#</th>
                    <th scope="col" style="width: 15%;">Fecha</th>
                    <th scope="col">Asignatura</th>
                    <th scope="col">Horario</th>
                    <th class="text-center" scope="col" style="width: 15%;">Estado Registrado</th>
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
                            <span class="text-muted">{{ $asistencia->horario_asignatura->hora_inicio }}</span>
                        </td>
                        <td class="text-center align-middle">
                            @php
                                $tipo = $asistencia->pivot->tipo;
                                $tiempoAtraso = $asistencia->pivot->tiempo_atraso;

                                $badgeClass = match ($tipo) {
                                    'P' => 'bg-success',
                                    'A' => 'bg-warning text-dark',
                                    'F' => 'bg-danger',
                                    'L' => 'bg-info text-dark',
                                    default => 'bg-secondary',
                                };

                                // Verificamos si es Atraso y tiene minutos registrados en el pivote
                                $badgeText = match ($tipo) {
                                    'P' => 'Presente',
                                    'A' => $tiempoAtraso ? 'Atraso (' . $tiempoAtraso . ' min)' : 'Atraso',
                                    'F' => 'Falta',
                                    'L' => 'Licencia',
                                    default => 'Desconocido',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} px-3 py-2 shadow-sm">
                                {{ $badgeText }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
