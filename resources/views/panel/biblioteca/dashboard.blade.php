@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold">
        <i class="fa-solid fa-duotone fa-dashboard mx-2"></i>
        {{ $head_title }}
    </h1>

    <h2 class="text-center"><i class="fa-solid fa-duotone fa-door-open mx-2"></i>Bienvenido,
        <span class="text-info fw-bold">
            <i class="fa-solid fa-duotone {{ helper_tipo_perfil_a_font_awesome_icono(session('tipo_perfil')) }}"></i>
            {{ session('correo') }}
        </span>
    </h2>

    <div class="card mb-3">
        <div class="card-header">
            <span class="h2 text-info fw-bold align-middle"><i class="fa-solid fa-duotone fa-bars"></i> MENÚ</span>
        </div>

        <div class="card-body">
            @include('panel.biblioteca.dashboard_botones')
        </div>
    </div>
@endsection

@section('scripts')
    @include('panel.biblioteca.dashboard_scripts')
@endsection
