<header class="p-3 mb-3 border-bottom">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start fw-bold">
            <div class="me-2">
                <i
                    class="fa-duotone fa-solid {{ helper_tipo_perfil_a_font_awesome_icono(Auth::user()->persona?->tipo_perfil) }} fa-lg"></i>
                {{ Auth::user()->correo }}
            </div>

            @if (Auth::user()->persona?->tipo_perfil === 'ADMIN')
                @include('panel.admin_super.dashboard_header')
            @elseif (Auth::user()->persona?->tipo_perfil === 'BIBLIOTECARIA')
                @include('panel.biblioteca.dashboard_header')
            @elseif (Auth::user()->persona?->tipo_perfil === 'DOCENTE' ||
                    Auth::user()->persona?->tipo_perfil === 'COORDINADOR' ||
                    Auth::user()->persona?->tipo_perfil === 'SUBDIRECTOR')
                @include('panel.docente.dashboard_header')
            @endif

            <button class="btn btn-light me-2" id="toggle-theme" data-toggle="tooltip" title="Cambiar tema">
                <i class="fa-solid fa-sun text-warning"></i>
            </button>

            <div class="dropdown text-end">

                <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle"
                    data-bs-toggle="dropdown" aria-expanded="false"> <img src="{{ URL::to('/') }}/public/img/user.jpeg"
                        alt="mdo" width="32" height="32" class="rounded-circle"> </a>
                <ul class="dropdown-menu text-small" style="">
                    <li><a class="dropdown-item" href="#"><i
                                class="fa-solid fa-duotone {{ helper_tipo_perfil_a_font_awesome_icono(Auth::user()->persona?->tipo_perfil) }}"></i>
                            {{ Auth::user()->correo }}</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="{{ route('main.index') }}"><i
                                class="fa-solid fa-duotone fa-house"></i>
                            Index</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    </li>
                    <li><button type="button" class="dropdown-item" data-bs-toggle="modal"
                            data-bs-target="#modal_sign_out">
                            <i class="fa-solid fa-duotone  fa-sign-out"></i> Cerrar sesión</button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</header>
