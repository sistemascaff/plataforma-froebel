<ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
    <ul class="nav nav-pills">
        <li class="nav-item mx-1">
            <a class="nav-link {{ request()->is('panel') ? 'active' : '' }}" aria-current="page"
                href="{{ route('dashboard') }}"><i class="fa-solid fa-duotone fa-dashboard"></i>
                Panel</a>
        </li>

        <li class="nav-item mx-1">
            <a class="nav-link {{ request()->is('estudiantes*') ? 'active' : '' }}" aria-current="page"
                href="{{ route('estudiantes.index') }}"><i class="fa-solid fa-duotone fa-user-graduate"></i>
                Estudiantes</a>
        </li>

        <li class="nav-item mx-1">
            <a class="nav-link {{ request()->is('docentes*') ? 'active' : '' }}" aria-current="page"
                href="{{ route('docentes.index') }}"><i class="fa-solid fa-duotone fa-chalkboard-user"></i>
                Docentes</a>
        </li>

        <li class="nav-item mx-1">
            <a class="nav-link {{ request()->is('asignaturas*') ? 'active' : '' }}" aria-current="page"
                href="{{ route('asignaturas.index') }}"><i class="fa-solid fa-duotone fa-book-reader"></i>
                Asignaturas</a>
        </li>

        <li class="nav-item mx-1">
            <a class="nav-link {{ request()->is('prestamos_libros*') ? 'active' : '' }}" aria-current="page"
                href="{{ route('prestamos_libros.index') }}"><i class="fa-solid fa-duotone fa-books fa-rotate-270"></i>
                Préstamos de libros</a>
        </li>
    </ul>
</ul>
