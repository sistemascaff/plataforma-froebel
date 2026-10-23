@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-calendar"></i> {{ $head_title }}
        </h1>
        <a class="btn btn-secondary shadow-sm" href="{{ route('gestiones.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left"></i> Volver
        </a>
    </div>

    @php
        $estado = match ($gestion->estado) {
            0 => 'ARCHIVADO',
            1 => 'ACTIVO',
            default => 'DESCONOCIDO',
        };
        $badgeClass = match ($gestion->estado) {
            0 => 'bg-secondary',
            1 => 'bg-success',
            default => 'bg-secondary',
        };
    @endphp

    {{-- Tarjeta de Información General de la Gestión --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-duotone fa-circle-info me-2"></i>Información de la Gestión</h5>
            <span class="badge {{ $badgeClass }} fs-6">{{ $estado }}</span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <label class="text-muted small text-uppercase">Año de Gestión</label>
                    <p class="fs-5 fw-semibold mb-0">{{ $gestion->anio }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Contenedor Principal Unificado con Nav-Tabs --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header p-0 border-bottom-0">
            @include('gestiones.details_nav_tabs')
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="gestionTabsContent">

                {{-- TAB 1: PERIODOS --}}
                @include('gestiones.details_periodos')

                {{-- TAB 2: DIMENSIONES --}}
                @include('gestiones.details_dimensiones')

                {{-- TAB 3: MALLAS CURRICULARES --}}
                @include('gestiones.details_mallas_curriculares')

                {{-- TAB 4: HORARIOS DE ASIGNATURAS --}}
                @include('gestiones.details_horarios_asignaturas')
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            $(".dataTable").DataTable({
                ...dtDefaults,
            });

            // Reajusta las cabeceras de DataTables al cambiar entre pestañas para evitar desalineaciones
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                $.fn.dataTable.tables({
                    visible: true,
                    api: true
                }).columns.adjust();
            });
        });
    </script>
@endsection
