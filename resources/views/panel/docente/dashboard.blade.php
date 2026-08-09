@extends('layouts.app')

@section('content')
    <h1 class="text-center text-info fw-bold">
        <i class="fa-solid fa-duotone fa-dashboard me-2"></i>{{ $head_title }}
    </h1>

    <h2 class="text-center"><i
            class="fa-solid fa-duotone fa-door-open me-2"></i>{{ Auth::user()->persona?->sexo == 'M' ? 'Bienvenido' : 'Bienvenida' }},
        <span class="text-info fw-bold">
            <i
                class="fa-solid fa-duotone {{ helper_tipo_perfil_a_font_awesome_icono(Auth::user()->persona?->tipo_perfil) }}"></i>
            {{ Auth::user()->persona?->nombres_apellidos }}
            [{{ Auth::user()->correo }}]
        </span>
    </h2>

    {{-- Si es cumpleaños del usuario, se muestra un mensaje de felicitación. --}}
    @if (date('m-d') === date('m-d', strtotime(Auth::user()->persona?->fecha_nacimiento)))
        <div class="alert alert-warning alert-dismissible fade show shadow-sm text-center border-warning mt-4 mb-4"
            role="alert">
            <h4 class="alert-heading fw-bold mb-3" style="color: #d97706;">
                <i class="fa-solid fa-cake-candles fa-bounce mx-2"></i>
                ¡Feliz Cumpleaños, {{ explode(' ', Auth::user()->persona?->nombres ?? 'Usuario')[0] }}!
                <i class="fa-solid fa-party-horn fa-shake mx-2"></i>
            </h4>

            <p class="mb-0 fs-5">
                Muchas gracias por tu dedicación y aporte a nuestra institución y sobretodo a la educación. ¡Que
                hoy tengas un día lleno de alegría, amor y momentos inolvidables! 🎉🎂🎁
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-header">
            <span class="h2 text-info fw-bold align-middle"><i class="fa-solid fa-duotone fa-bars"></i> MENÚ</span>
        </div>

        <div class="card-body">
            @include('panel.docente.dashboard_botones')
        </div>
    </div>
@endsection

@section('scripts')
    @include('panel.docente.dashboard_scripts')
@endsection
