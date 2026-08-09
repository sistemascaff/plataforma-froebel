<ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
    <ul class="nav nav-pills">
        <li class="nav-item mx-1">
            <a class="nav-link {{ request()->is('panel') ? 'active' : '' }}" aria-current="page"
                href="{{ route('dashboard') }}"><i class="fa-solid fa-duotone fa-dashboard"></i>
                Panel</a>
        </li>
        <li class="nav-item mx-1">
            <a class="nav-link {{ request()->is('asignaturas/docente') ? 'active' : '' }}" aria-current="page"
                href="{{ route('asignaturas.docente') }}"><i class="fa-solid fa-duotone fa-book-reader"></i>
                Mis asignaturas</a>
        </li>
    </ul>
</ul>
