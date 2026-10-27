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

    {{-- Tarjeta de Horario Semanal --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header p-3">
            <div class="row align-items-center g-2">
                {{-- Título --}}
                <div class="col-12 col-md-auto me-md-auto text-center text-md-start">
                    <h5 class="fw-bold mb-0 text-primary">
                        <i class="fa-duotone fa-calendar-week me-1"></i> Mi Horario Semanal
                    </h5>
                </div>

                {{-- Badges de Estado --}}
                <div class="col-12 col-md-auto">
                    <div class="d-flex flex-wrap justify-content-center justify-content-md-end gap-2">
                        <span class="badge bg-primary text-nowrap px-2 py-1 shadow-sm fs-7" id="periodo-actual-badge">
                            <i class="fa-solid fa-duotone fa-info-circle me-1"></i>Cargando...
                        </span>
                        <span class="badge bg-info text-dark text-nowrap px-2 py-1 shadow-sm fs-7" id="fecha-actual-badge">
                            <i class="fa-duotone fa-calendar-day me-1"></i>Cargando...
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            {{-- Pestañas (Nav Tabs) --}}
            <ul class="nav nav-tabs nav-tabs-custom" id="tabsHorario" role="tablist">
                <!-- Se renderiza por JS -->
            </ul>

            {{-- Contenido de las Pestañas --}}
            <div class="tab-content mt-4" id="contenidoHorario">
                <div class="text-center py-5" id="cargandoHorario">
                    <i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i>
                    <p class="mt-2 text-muted fw-bold">Cargando horario...</p>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('panel.docente.dashboard_scripts')
@endsection
