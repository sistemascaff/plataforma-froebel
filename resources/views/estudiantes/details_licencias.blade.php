<div class="tab-pane fade" id="licencias-pane" role="tabpanel" aria-labelledby="licencias-tab" tabindex="0">
    <div class="table-responsive pt-2">
        <table class="table table-bordered table-striped dataTable w-100" id="tabla-licencias">
            <thead>
                <tr>
                    <th class="text-center" scope="col" style="width: 5%;">#</th>
                    <th scope="col" style="width: 15%;">Tipo de Licencia</th>
                    <th scope="col">Justificación</th>
                    <th scope="col" style="width: 15%;">Fecha de Inicio</th>
                    <th scope="col" style="width: 15%;">Fecha de Fin</th>
                    <th class="text-center" scope="col" style="width: 10%;">Evidencia</th>
                    <th scope="col" style="width: 15%;">F. Registro</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($estudiante->estudiantes_licencias))
                    @foreach ($estudiante->estudiantes_licencias as $licencia)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->index + 1 }}</td>
                            <td class="align-middle fw-bold text-primary">
                                {{ ucwords(str_replace('_', ' ', $licencia->tipo)) }}
                            </td>
                            <td class="align-middle">
                                {{ $licencia->justificacion }}
                            </td>
                            <td class="align-middle">
                                <span class="badge bg-primary text-white shadow-sm w-100 py-2">
                                    {{ date('d/m/Y H:i', strtotime($licencia->fecha_inicio)) }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <span class="badge bg-danger text-white shadow-sm w-100 py-2">
                                    {{ date('d/m/Y H:i', strtotime($licencia->fecha_fin)) }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                @if ($licencia->evidencia)
                                    <a href="{{ $licencia->evidencia }}" target="_blank"
                                        class="btn btn-sm btn-outline-info shadow-sm" title="Ver Evidencia">
                                        <i class="fa-solid fa-duotone fa-up-right-from-square"></i>
                                        Abrir
                                    </a>
                                @else
                                    <span class="text-muted fst-italic small">N/A</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                {{ date('d/m/Y H:i', strtotime($licencia->fecha_registro)) }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
