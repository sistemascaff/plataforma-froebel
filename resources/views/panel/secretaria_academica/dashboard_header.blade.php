<ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
    <ul class="nav nav-pills">
        <li class="nav-item mx-1">
            <a class="nav-link {{ request()->is('panel') ? 'active' : '' }}" aria-current="page"
                href="{{ route('dashboard') }}"><i class="fa-solid fa-duotone fa-dashboard"></i>
                Panel</a>
        </li>
        <li class="nav-item mx-1">
            <a class="nav-link {{ request()->is('estudiantes') ? 'active' : '' }}" aria-current="page"
                href="{{ route('estudiantes.index') }}"><i class="fa-solid fa-duotone fa-user-graduate"></i>
                Estudiantes</a>
        </li>
        <li class="nav-item mx-1">
            <a class="nav-link {{ request()->is('estudiantes_licencias') ? 'active' : '' }}" aria-current="page"
                href="{{ route('estudiantes_licencias.index') }}"><i class="fa-solid fa-duotone fa-file-medical"></i>
                Licencias</a>
        </li>
    </ul>
</ul>
