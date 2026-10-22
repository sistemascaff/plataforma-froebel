@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-info fw-bold mb-0">
            <i class="fa-solid fa-duotone fa-clipboard-list-check me-2"></i>{{ $head_title }}
        </h1>
        <a class="btn btn-secondary shadow-sm" href="{{ route('asignaturas.detalles', $lista_asignatura->id_asignatura) }}">
            <i class="fa-solid fa-duotone fa-arrow-left me-1"></i> Volver a
            <b>{{ $lista_asignatura->asignatura->asignatura }}</b>
        </a>
    </div>

    @if (!empty($cambios) && count($cambios) > 0)
        <div class="mb-3">
            <button type="button" class="btn btn-warning shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCambios">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>Ver cambios recientes en la lista
            </button>
        </div>
    @endif

    @include('listas_asignaturas.details_info')

    {{-- Contenedor principal con pestañas (Nav-Tabs) --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header p-0 border-bottom-0">
            @include('listas_asignaturas.details_nav_tabs')
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="listaAsignaturaTabsContent">

                {{-- Pestaña de estudiantes inscritos --}}
                @include('listas_asignaturas.details_estudiantes')

                {{-- Pestaña de asistencias registradas --}}
                @include('listas_asignaturas.details_asistencias')

            </div>
        </div>
    </div>

    {{-- Modal de Cambios Automáticos --}}
    @if (!empty($cambios) && count($cambios) > 0)
        @include('listas_asignaturas.details_modal_cambios')
    @endif

    {{-- Modal para importar estudiantes por cursos (solo mixto) --}}
    @if ($lista_asignatura->asignatura->tipo_bloque === 'mixto')
        @include('listas_asignaturas.details_modal_cursos')
    @endif

    @include('components.app.img_modal')
@endsection

@section('scripts')
    @include('listas_asignaturas.details_scripts')
@endsection
