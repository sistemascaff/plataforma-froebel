@extends('layouts.public')

@section('content')
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        @if (!session('tiene_acceso'))
            <i class="fa-solid fa-duotone fa-party-horn"></i> ¡Bienvenido/a a la plataforma del Colegio
            Alemán Federico Froebel!, <i class="fa-solid fa-duotone fa-circle-info text-warning"></i> <b>en este momento
                estás navegando como <span class="text-warning">invitado</span></b>.
            <br>

            ¿Ya tienes una cuenta? <a href="{{ route('login') }}" class="alert-link">Iniciar sesión</a>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        @else
            Bienvenido, <i
                class="fa-solid fa-duotone {{ helper_tipo_perfil_a_font_awesome_icono(session('tipo_perfil')) }}"></i>
            <b>[{{ session('tipo_perfil') }}]
                {{ trim(session('nombres') . ' ' . session('apellido_paterno') . ' ' . session('apellido_materno')) }}</b>.
            <br>

            Te encuentras en el index público, ¿necesitas dirigirte a tu panel? <a href="{{ route('dashboard') }}"
                class="alert-link">Haz clic aquí.</a>
        @endif
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row">

                <div class="col-6 col-md-4 col-lg-2 d-flex justify-content-center">
                    <a class="btn btn-sq-lg btn-info" href="{{ route('libros.public.index') }}">
                        <div>
                            <i class="fa-solid fa-duotone fa-book-open fa-2xl"></i><br />Biblioteca
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
@endsection
