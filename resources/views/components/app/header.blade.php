<header class="p-3 mb-3 border-bottom">
    <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start fw-bold">
            <div class="me-2">
                <i
                    class="fa-duotone fa-solid {{ helper_tipo_perfil_a_font_awesome_icono(session('tipo_perfil')) }} fa-lg"></i>
                {{ session('correo') }}
            </div>
            <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('panel') ? 'active' : '' }}" aria-current="page"
                            href="{{ route('dashboard') }}"><i class="fa-solid fa-duotone fa-dashboard"></i>
                            Panel</a>
                    </li>

                    {{-- 
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('ventas/crear') ? 'active' : '' }}" aria-current="page"
                            href="{{ route('ventas.crear') }}"><i class="fa-solid fa-duotone fa-cart-plus"></i>
                            Añadir venta</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('ventas') ? 'active' : '' }}" aria-current="page"
                            href="{{ route('ventas.index') }}"><i class="fa-solid fa-duotone fa-cart-shopping"></i>
                            Ventas</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('abastecimientos') ? 'active' : '' }}" aria-current="page"
                            href="{{ route('abastecimientos.index') }}"><i class="fa-solid fa-duotone fa-cart-flatbed-boxes"></i>
                            Abastecimientos</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('productos') ? 'active' : '' }}" aria-current="page"
                            href="{{ route('productos.index') }}"><i class="fa-solid fa-duotone fa-boxes-stacked"></i>
                            Productos</a>
                    </li>
                    --}}
                </ul>
            </ul>
            <div class="dropdown text-end"> <a href="#"
                    class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown"
                    aria-expanded="false"> <img src="{{ URL::to('/') }}/public/img/user.jpeg" alt="mdo"
                        width="32" height="32" class="rounded-circle"> </a>
                <ul class="dropdown-menu text-small" style="">
                    <li><a class="dropdown-item" href="#"><i
                                class="fa-solid fa-duotone {{ helper_tipo_perfil_a_font_awesome_icono(session('tipo_perfil')) }}"></i>
                            {{ session('correo') }}</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item"
                            href="{{ route('main.index') }}"><i class="fa-solid fa-duotone fa-house"></i>
                            Index</a></li>
                    <li>
                    {{-- <li><a class="dropdown-item {{ request()->is('parametros') ? 'active' : '' }}"
                            href="{{ route('parametros.index') }}"><i class="fa-solid fa-duotone fa-sliders"></i>
                            Parámetros</a></li>
                    <li> --}}
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
