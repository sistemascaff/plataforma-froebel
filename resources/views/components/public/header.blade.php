<header
    class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom mx-3">
    <div class="col-md-3 mb-2 mb-md-0">
        <a href="{{ route('main.index') }}">
            <img class="img-fluid mx-auto bg-light rounded" alt="Logo"
                src="{{ URL::to('/') }}/public/img/orgullo_froebeliano.png" style="width: 150px;">
        </a>
    </div>

    <ul class="nav col-12 col-md-auto mb-2 justify-content-center mb-md-0 fw-bold">
        <li><a href="{{ route('main.index') }}" class="nav-link px-2 {{ request()->is('/') ? 'link-secondary' : '' }}">Inicio</a></li>
        @if (session('tiene_acceso'))
            <li><a href="{{ route('dashboard') }}" class="nav-link px-2 text-info">Panel</a></li>
        @endif
        <li><a href="{{ route('libros.public.index') }}" class="nav-link px-2 {{ request()->is('biblioteca') ? 'link-secondary' : '' }}">Biblioteca</a></li>
    </ul>

    <div class="col-md-3 text-end">
        <button class="btn btn-light me-2" id="toggle-theme">
            <i class="fa-chisel fa-regular fa-sun"></i>
        </button>
        @if (session('tiene_acceso'))
            <a href="{{ route('dashboard') }}" type="button" class="btn btn-primary me-2">
                <i class="fa-solid fa-duotone fa-dashboard"></i>
                Ir al panel
            </a>
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modal_sign_out">
                <i class="fa-solid fa-duotone fa-sign-out"></i> Cerrar sesión</button>
        @else
            <a href="{{ route('login') }}" type="button" class="btn btn-primary me-2">
                <i class="fa-solid fa-duotone fa-sign-in"></i>
                Iniciar sesión
            </a>
        @endif

    </div>
</header>
