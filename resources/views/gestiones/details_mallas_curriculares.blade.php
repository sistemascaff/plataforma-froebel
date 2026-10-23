<div class="tab-pane fade" id="tab-mallas" role="tabpanel" aria-labelledby="mallas-tab">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold text-warning text-dark">
            <i class="fa-solid fa-duotone fa-object-group me-2"></i>Mallas Curriculares
        </h5>
        <a class="btn btn-primary btn-sm shadow-sm" href="{{ route('mallas_curriculares.index') }}">
            <i class="fa-solid fa-duotone fa-objects-column me-1"></i> Gestionar Mallas
        </a>
    </div>
    <div class="alert border-info bg-info bg-opacity-10 text-info d-flex align-items-center mb-4 shadow-sm"
        role="alert">
        <i class="fa-solid fa-circle-info me-2 fs-5"></i>
        <div>
            Cuando existen más de un registro por grado, se promedian las dos o más materias involucradas
            entre sí.
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped dataTable w-100" id="tabla-mallas">
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th>Grado</th>
                    <th>P. ordinal de grado</th>
                    <th>Materia</th>
                    <th>Área</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($gestion->mallas_curriculares as $malla_curricular)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->index + 1 }}</td>
                        <td class="align-middle fw-bold">{{ $malla_curricular->grado->grado }}</td>
                        <td class="align-middle">{{ $malla_curricular->grado->posicion_ordinal }}</td>
                        <td class="align-middle">
                            <b>{{ $malla_curricular->materia->abreviatura }}</b> -
                            {{ $malla_curricular->materia->materia }}
                        </td>
                        <td class="align-middle">
                            <b>{{ $malla_curricular->area->abreviatura }}</b> -
                            {{ $malla_curricular->area->area }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
