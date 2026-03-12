@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold"><i class="fa-solid fa-duotone fa-stairs"></i> {{ $head_title }}
    </h1>

    <a class="btn btn-secondary mb-3" href="{{ route('niveles.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver</a>

    <label for="nivel">Nivel:</label>
    <p class="form-control mb-3" id="nivel">
        {{ $nivel->nivel }}
    </p>

    @php
        $estado = match ($nivel->estado) {
            0 => 'ARCHIVADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $class = match ($nivel->estado) {
            0 => 'alert alert-secondary',
            1 => 'alert alert-success',
            default => 'alert alert-secondary',
        };
    @endphp

    <div class="{{ $class }} fw-bold mb-3">
        Estado: {{ $estado }}
    </div>


    <h2 class="text-info fw-bold">Grados</h2>

    <table class="table table-bordered table-striped mb-3 dataTable" id="grados">
        <thead>
            <tr>
                <th>#</th>
                <th>Grado</th>
                <th>Posición Ordinal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($nivel->grados as $grado)
                <tr>
                    <td>{{ $loop->index + 1 }}</td>
                    <td>{{ $grado->grado }}</td>
                    <td>{{ $grado->posicion_ordinal }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mb-3"></div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $(".dataTable").DataTable({
                @include('components.datatables.datatables_global_properties')
                @include('components.datatables.datatables_language_property')
            });
        });
    </script>
@endsection
