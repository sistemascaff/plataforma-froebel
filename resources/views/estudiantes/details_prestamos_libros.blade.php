<div class="tab-pane fade" id="prestamos-libros-pane" role="tabpanel" aria-labelledby="prestamos-libros-tab" tabindex="0">
    <div class="table-responsive pt-2">
        <table class="table table-bordered table-striped dataTable w-100" id="tabla-prestamos-libros">
            <thead>
                <tr>
                    <th class="text-center" scope="col">#</th>
                    <th class="text-center" scope="col">Nro. Préstamo</th>
                    <th class="text-center" scope="col">Libros Prestados
                    </th>
                    <th class="text-center" scope="col">F. Devolución Obj.
                    </th>
                    <th class="text-center" scope="col">Estado de Entrega</th>
                    <th class="text-center" scope="col">Estado de Reg.</th>
                    <th class="text-center" scope="col">F. Registro</th>
                </tr>
            </thead>
            <tbody>
                @if (isset($estudiante->persona->prestamos))
                    @foreach ($estudiante->persona->prestamos as $prestamo)
                        <tr>
                            <td class="text-center align-middle">{{ $loop->iteration }}</td>

                            <td class="text-center align-middle">
                                <b>{{ $prestamo->id_prestamo_libro }}</b>
                            </td>

                            <td class="align-middle">
                                @if (isset($prestamo->libros) && count($prestamo->libros) > 0)
                                    @foreach ($prestamo->libros as $index => $libro)
                                        @php
                                            $estadoTexto = '';
                                            $css = '';
                                            if ($prestamo->estado == 0) {
                                                $estadoTexto = '<i class="fa-solid fa-circle-xmark me-1"></i>(ANULADO)';
                                                $css = 'text-danger fw-bold';
                                            } else {
                                                // Evaluamos según el estado del libro en el pivote
                                                if ($libro->estado == 2 && is_null($libro->pivot->fecha_retorno)) {
                                                    $estadoTexto = '(EN USO)';
                                                    $css = 'text-primary fw-bold';
                                                } elseif (!is_null($libro->pivot->fecha_retorno)) {
                                                    $fechaRetorno = \Carbon\Carbon::parse(
                                                        $libro->pivot->fecha_retorno,
                                                    )->format('d/m/Y H:i');
                                                    $estadoTexto = "(DEVUELTO EL {$fechaRetorno})";
                                                    $css = 'text-success opacity-75';
                                                } else {
                                                    $estadoTexto = '(DISPONIBLE)';
                                                    $css = 'text-muted';
                                                }
                                            }
                                        @endphp
                                        <small class="text-muted">{{ $index + 1 }}.</small>
                                        <span class="{{ $css }}">{!! $estadoTexto !!}</span>
                                        <code class="text-dark-aquamarine">{{ $libro->codigo }}</code>
                                        - {{ $libro->titulo }}
                                        @if (!$loop->last)
                                            <br>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="text-center align-middle">
                                {{ \Carbon\Carbon::parse($prestamo->fecha_devolucion)->format('d/m/Y') }}
                            </td>

                            <td class="align-middle">
                                @if ($prestamo->estado == 0)
                                    <span class="text-muted small">N/A</span>
                                @elseif(isset($prestamo->libros) && count($prestamo->libros) > 0)
                                    @foreach ($prestamo->libros as $index => $libro)
                                        @php
                                            $fechaDevolucion = \Carbon\Carbon::parse(
                                                $prestamo->fecha_devolucion,
                                            )->startOfDay();
                                            $fechaRetorno = $libro->pivot->fecha_retorno
                                                ? \Carbon\Carbon::parse($libro->pivot->fecha_retorno)->startOfDay()
                                                : null;
                                            $mensaje = '';
                                            $clase = '';

                                            if (!$fechaRetorno) {
                                                $hoy = \Carbon\Carbon::now()->startOfDay();
                                                // diffInDays con 'false' devuelve valor positivo si $hoy es mayor (retraso)
                                                $diasAtraso = $fechaDevolucion->diffInDays($hoy, false);

                                                if ($diasAtraso > 0) {
                                                    $mensaje = "{$diasAtraso} días retraso";
                                                    $clase = 'text-danger fw-bold';
                                                } elseif ($diasAtraso == 0) {
                                                    $mensaje = 'Vence hoy';
                                                    $clase = 'text-warning fw-bold';
                                                } else {
                                                    $mensaje = abs($diasAtraso) . ' día(s) rest.';
                                                    $clase = 'text-primary';
                                                }
                                            } else {
                                                $diasAtraso = $fechaDevolucion->diffInDays($fechaRetorno, false);
                                                if ($diasAtraso > 0) {
                                                    $mensaje = "{$diasAtraso} d. con retraso";
                                                    $clase = 'text-danger';
                                                } else {
                                                    $mensaje = 'A tiempo';
                                                    $clase = 'text-success';
                                                }
                                            }
                                        @endphp
                                        <small class="text-muted">{{ $index + 1 }}.</small>
                                        <span class="{{ $clase }}">{{ $mensaje }}</span>
                                        @if (!$loop->last)
                                            <br>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="text-center align-middle">
                                @if ($prestamo->estado == 1)
                                    <span class="badge bg-success shadow-sm">ACTIVO</span>
                                @elseif ($prestamo->estado == 0)
                                    <span class="badge bg-danger shadow-sm">ANULADO</span>
                                @else
                                    <span class="badge bg-warning shadow-sm">DESCONOCIDO</span>
                                @endif
                            </td>

                            <td class="text-center align-middle">
                                {{ \Carbon\Carbon::parse($prestamo->fecha_registro)->format('d/m/Y H:i:s') }}
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>
