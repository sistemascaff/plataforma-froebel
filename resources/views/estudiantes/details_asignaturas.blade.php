<div class="tab-pane fade show active" id="asignaturas-pane" role="tabpanel" aria-labelledby="asignaturas-tab"
    tabindex="0">
    <div class="table-responsive pt-2">
        <table class="table table-bordered table-striped dataTable w-100" id="asignaturas">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Asignatura</th>
                    <th scope="col">Tipo de Calificación</th>
                    <th scope="col">Tipo de Bloque</th>
                    <th scope="col">Docente</th>
                    <th scope="col">Gestión</th>
                    <th scope="col">Periodo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($estudiante->listas_asignaturas as $lista_asignatura)
                    <tr>
                        <td>{{ $loop->index + 1 }}</td>
                        <td>{{ $lista_asignatura->asignatura->asignatura }}</td>
                        <td>
                            <span class="badge bg-info text-dark">
                                @php
                                    $icono =
                                        $lista_asignatura->asignatura->tipo_calificacion === 'cualitativa'
                                            ? 'fa-comments'
                                            : 'fa-chart-column';
                                @endphp
                                <i class="fa-solid fa-duotone {{ $icono }} me-1"></i>
                                {{ strtoupper($lista_asignatura->asignatura->tipo_calificacion) }}
                            </span>
                        </td>
                        <td>
                            <span
                                class="badge {{ $lista_asignatura->asignatura->tipo_bloque === 'curso' ? 'bg-primary' : 'bg-danger' }}">
                                {{ strtoupper($lista_asignatura->asignatura->tipo_bloque) }}
                            </span>
                        </td>
                        <td>{{ $lista_asignatura->docente?->persona->nombres_apellidos }}</td>
                        <td>{{ $lista_asignatura->periodo->gestion->anio }}</td>
                        <td>{{ $lista_asignatura->periodo->periodo }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
