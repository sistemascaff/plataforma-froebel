<div class="tab-pane fade" id="tab-asistencias" role="tabpanel" aria-labelledby="asistencias-tab">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-info">
            <i class="fa-duotone fa-calendar-check me-1"></i> Historial de Clases y Asistencias
        </h5>
        <a class="btn btn-success shadow-sm"
            href="{{ route('estudiantes_asistencias.crear', $lista_asignatura->id_lista_asignatura) }}">
            <i class="fa-solid fa-duotone fa-plus me-1"></i> Crear asistencia
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered table-striped dataTable w-100" id="asistencias">
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th style="width: 8%;">Fecha</th>
                    <th>Horario</th>
                    <th class="text-center">Registrados</th>
                    <th class="text-center">Presentes</th>
                    <th class="text-center">Atrasos</th>
                    <th class="text-center">Faltas</th>
                    <th class="text-center">Licencias</th>
                    <th class="text-center">Permisos</th>
                    <th class="text-center">Otros</th>
                    {{-- <th class="text-center">Estado</th> --}}
                    <th class="text-center" style="width: 10%;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lista_asignatura->estudiantes_asistencias as $estudiante_asistencia)
                    @php
                        $estudianteAsistenciaEstado = $estudiante_asistencia->estado;
                        $estudianteAsistenciaEstadoClass = match ($estudianteAsistenciaEstado) {
                            1 => 'bg-success',
                            0 => 'bg-danger',
                            default => 'bg-secondary',
                        };
                        $estudianteAsistenciaEstadoTexto = match ($estudianteAsistenciaEstado) {
                            1 => 'ACTIVO',
                            0 => 'INACTIVO',
                            default => 'DESCONOCIDO',
                        };
                        $esHorarioNoRegular = $estudiante_asistencia->es_horario_no_regular === 1;
                    @endphp
                    <tr
                        class="{{ $estudiante_asistencia->detalles_estudiantes_asistencias_count === $estudiante_asistencia->presentes_count ? 'table-success' : ($estudiante_asistencia->otros_count > 0 ? 'table-secondary' : '') }}">
                        <td class="text-center align-middle">{{ $loop->index + 1 }}</td>
                        <td class="align-middle">
                            {{ date('d/m/Y', strtotime($estudiante_asistencia->fecha)) }}</td>
                        <td class="align-middle {{ $esHorarioNoRegular ? ' table-info' : '' }}">
                            {{ "{$estudiante_asistencia->horario_asignatura->denominacion} ({$estudiante_asistencia->horario_asignatura->hora_inicio} - {$estudiante_asistencia->horario_asignatura->hora_fin})" }}
                            @if ($esHorarioNoRegular)
                                <br>
                                <span class="badge bg-info text-dark ms-1 shadow-sm">
                                    <i class="fa-solid fa-duotone fa-clock me-1"></i>No regular
                                </span>
                            @endif
                        </td>
                        <td class="text-center align-middle">
                            <span class="badge bg-primary">
                                {{ $estudiante_asistencia->detalles_estudiantes_asistencias_count }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <span
                                class="{{ $estudiante_asistencia->presentes_count > 0 ? 'badge bg-success' : 'text-muted' }}">
                                {{ $estudiante_asistencia->presentes_count }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <span
                                class="{{ $estudiante_asistencia->atrasos_count > 0 ? 'badge bg-warning text-dark' : 'text-muted' }}">
                                {{ $estudiante_asistencia->atrasos_count }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <span
                                class="{{ $estudiante_asistencia->faltas_count > 0 ? 'badge bg-danger' : 'text-muted' }}">
                                {{ $estudiante_asistencia->faltas_count }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <span
                                class="{{ $estudiante_asistencia->licencias_count > 0 ? 'badge bg-info' : 'text-muted' }}">
                                {{ $estudiante_asistencia->licencias_count }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <span
                                class="{{ $estudiante_asistencia->permisos_count > 0 ? 'badge bg-primary' : 'text-muted' }}">
                                {{ $estudiante_asistencia->permisos_count }}
                            </span>
                        </td>
                        <td class="text-center align-middle">
                            <span
                                class="{{ $estudiante_asistencia->otros_count > 0 ? 'badge bg-secondary' : 'text-muted' }}"
                                title="{{ $estudiante_asistencia->otros_count > 0 ? $estudiante_asistencia->justificacion_otros : '' }}">
                                {{ $estudiante_asistencia->otros_count }}
                            </span>
                        </td>
                        {{-- <td class="text-center align-middle">
                            <span class="badge {{ $estudianteAsistenciaEstadoClass }}">
                                {{ $estudianteAsistenciaEstadoTexto }}
                            </span>
                        </td> --}}
                        <td class="text-center align-middle">
                            <div class="btn-group" role="group">
                                <a class="btn btn-info btn-sm"
                                    href="{{ route('estudiantes_asistencias.detalles', $estudiante_asistencia->id_estudiante_asistencia) }}"
                                    target="_blank" rel="noopener noreferrer" data-bs-toggle="tooltip" title="Detalles">
                                    <i class="fa-duotone fa-solid fa-eye"></i>
                                </a>
                                <a class="btn btn-warning btn-sm"
                                    href="{{ route('estudiantes_asistencias.editar', $estudiante_asistencia->id_estudiante_asistencia) }}"
                                    target="_blank" rel="noopener noreferrer" data-bs-toggle="tooltip" title="Editar">
                                    <i class="fa-duotone fa-solid fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
