@extends('layouts.app')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <h1 class="text-info fw-bold mb-0 me-auto">
            <i class="fa-solid fa-duotone fa-chart-pie me-2"></i>Reportes y Métricas de Asistencias
        </h1>
        <a class="btn btn-secondary shadow-sm" href="{{ route('estudiantes_asistencias.index') }}">
            <i class="fa-solid fa-duotone fa-arrow-left me-1"></i>Volver
        </a>
    </div>

    <div class="alert alert-info border-info border-start border-4 shadow-sm mb-4 d-flex align-items-center" role="alert">
        <i class="fa-solid fa-duotone fa-circle-info fa-2x me-3"></i>
        <div>
            <h5 class="alert-heading fw-bold mb-1">Información del Reporte</h5>
            <p class="mb-0">
                Por defecto, se muestra la información de <b>hoy</b>. Al aplicar los filtros,
                tanto las tablas como los gráficos y tarjetas se recalcularán automáticamente en tiempo real.
            </p>
        </div>
    </div>

    {{-- PANEL DE FILTROS DINÁMICOS --}}
    @include('estudiantes_asistencias.reportes_filters')

    {{-- A) TARJETAS DE MÉTRICAS --}}
    @include('estudiantes_asistencias.reportes_metricas')

    {{-- DASHBOARD DE GRÁFICOS (TABS) --}}
    @include('estudiantes_asistencias.reportes_graficos')

    {{-- TABLAS DE RESULTADOS (TABS) --}}
    @include('estudiantes_asistencias.reportes_tablas')
@endsection

@section('scripts')
    @include('estudiantes_asistencias.reportes_scripts')
@endsection
