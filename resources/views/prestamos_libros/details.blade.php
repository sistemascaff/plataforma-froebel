@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-books fa-rotate-270"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-secondary mb-3" href="{{ route('prestamos_libros.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver</a>

    <a class="btn btn-primary mb-3 ms-2" href="{{ route('prestamos_libros.imprimir', $prestamo_libro->id_prestamo_libro) }}" target="_blank" rel="noopener noreferrer">
        <i class="fa-solid fa-duotone fa-print"></i> Imprimir</a>

    <a class="btn btn-warning mb-3 ms-2" href="{{ route('prestamos_libros.editar', $prestamo_libro->id_prestamo_libro) }}">
        <i class="fa-solid fa-duotone fa-edit"></i> Editar</a>

    <div></div>

    <label for="persona">Persona:</label>
    <p class="form-control mb-3" id="persona">
        {{ trim(
            '(' .
                $prestamo_libro->persona->tipo_perfil .
                ') ' .
                $prestamo_libro->persona->apellido_paterno .
                ' ' .
                $prestamo_libro->persona->apellido_materno .
                ' ' .
                $prestamo_libro->persona->nombres,
        ) }}
    </p>

    <label for="curso">Curso:</label>
    <p class="form-control mb-3" id="curso"> {{ $prestamo_libro->curso }}</p>

    <label for="celular">Celular:</label>
    <p class="form-control mb-3" id="celular"> {{ $prestamo_libro->celular ?? '-' }}</p>

    <label for="celular">Fecha de devolución:</label>
    <p class="form-control mb-3" id="celular"> {{ date('d/m/Y', strtotime($prestamo_libro->fecha_devolucion)) }}</p>

    <h2 class="text-info fw-bold">Detalles</h2>

    <table class="table table-bordered table-striped mb-3" id="detalles">
        <thead>
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Título</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($prestamo_libro->libros as $libro)
                <tr>
                    <td class="fw-bold text-primary">{{ $loop->iteration }}</td>
                    <td class="codigo">{{ $libro->codigo }}</td>
                    <td class="titulo">{{ $libro->titulo }}</td>
                    <td>
                        @if ($prestamo_libro->estado != 0)
                            @if ($libro->pivot->fecha_retorno == null)
                                <button type="button" class="btn btn-success btn-sm btn-marcar"
                                    data-id="{{ $libro->id_libro }}" data-toggle="tooltip" title="Marcar como devuelto">
                                    <i class="fa fa-check"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-warning btn-sm btn-marcar"
                                    data-id="{{ $libro->id_libro }}" data-toggle="tooltip" title="Marcar como pendiente">
                                    <i class="fa fa-xmark"></i>
                                </button>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mb-3"></div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const prestamoId = "{{ $prestamo_libro->id_prestamo_libro }}";

            $(document).on('click', '.btn-marcar', function() {
                const $btn = $(this);
                const idLibro = $btn.data('id');
                const accion = $btn.hasClass('btn-success') ? 'devolver' : 'pendiente';

                Swal.fire({
                    theme: localStorage.getItem('theme') || 'light',
                    title: "Confirmación",
                    html: `¿Estás seguro de marcar este libro como <b class="text-primary">${accion}</b>?`,
                    icon: "info",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Si, confirmar",
                    cancelButtonText: "No, cancelar"
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    Swal.fire({
                        theme: localStorage.getItem('theme') || 'light',
                        title: 'Procesando...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const url =
                        `{{ route('prestamos_libros.marcar', ['prestamo_libro' => $prestamo_libro->id_prestamo_libro, 'libro' => ':id_libro']) }}`
                        .replace(':id_libro', idLibro);

                    $.ajax({
                        url: url,
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.close();

                            if (response && response.success) {
                                const type = response.type || '';

                                if (type === 'devuelto') {
                                    // Cambiar a estado "pendiente" visualmente
                                    $btn.removeClass('btn-success').addClass(
                                        'btn-warning');
                                    $btn.attr('title', 'Marcar como pendiente');
                                    $btn.find('i').attr('class', 'fa fa-xmark');
                                } else if (type === 'revertido') {
                                    // Cambiar a estado "devuelto" visualmente
                                    $btn.removeClass('btn-warning').addClass(
                                        'btn-success');
                                    $btn.attr('title', 'Marcar como devuelto');
                                    $btn.find('i').attr('class', 'fa fa-check');
                                }

                                // Mensaje de éxito
                                Swal.fire({
                                    theme: localStorage.getItem('theme') ||
                                        'light',
                                    icon: 'success',
                                    title: response.message ||
                                        'Operación realizada',
                                    showConfirmButton: false,
                                    timer: 1400
                                });
                            } else {
                                const msg = (response && response.message) ? response
                                    .message : 'Ocurrió un error';
                                Swal.fire({
                                    theme: localStorage.getItem('theme') ||
                                        'light',
                                    icon: 'error',
                                    title: 'Error',
                                    html: msg
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.close();
                            let msg = 'Ocurrió un error en la petición.';
                            if (xhr && xhr.responseJSON && xhr.responseJSON.message)
                                msg = xhr.responseJSON.message;
                            Swal.fire({
                                theme: localStorage.getItem('theme') || 'light',
                                icon: 'error',
                                title: 'Error',
                                html: msg
                            });
                        }
                    });
                });
            });
        });
    </script>
@endsection
