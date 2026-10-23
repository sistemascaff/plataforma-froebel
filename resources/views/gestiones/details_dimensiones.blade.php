<div class="tab-pane fade" id="tab-dimensiones" role="tabpanel" aria-labelledby="dimensiones-tab">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-bold text-primary">
            <i class="fa-solid fa-duotone fa-layer-group me-2"></i>Dimensiones de Evaluación
        </h5>
        <a class="btn btn-primary btn-sm shadow-sm" href="{{ route('dimensiones.index') }}">
            <i class="fa-solid fa-duotone fa-layer-plus me-1"></i> Gestionar Dimensiones
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped dataTable w-100" id="tabla-dimensiones">
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%;">#</th>
                    <th>Dimensión</th>
                    <th>Posición ordinal</th>
                    <th>Puntaje máximo</th>
                    <th>Tipo de cálculo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($gestion->dimensiones as $dimension)
                    <tr>
                        <td class="text-center align-middle">{{ $loop->index + 1 }}</td>
                        <td class="align-middle fw-bold">{{ $dimension->dimension }}</td>
                        <td class="align-middle">{{ $dimension->posicion_ordinal }}</td>
                        <td class="align-middle">{{ $dimension->puntaje_maximo }}</td>
                        <td class="align-middle">{{ strtoupper($dimension->tipo_calculo) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
