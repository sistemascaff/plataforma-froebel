<ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
    <ul class="nav nav-pills">
        <li class="nav-item mx-1">
            <a class="nav-link {{ request()->is('panel') ? 'active' : '' }}" aria-current="page"
                href="{{ route('dashboard') }}"><i class="fa-solid fa-duotone fa-dashboard"></i>
                Panel</a>
        </li>

        <li class="nav-item mx-1">
            <a class="nav-link {{ request()->is('libros*') ? 'active' : '' }}" aria-current="page"
                href="{{ route('libros.index') }}"><i class="fa-solid fa-duotone fa-book-open"></i>
                Libros</a>
        </li>

        <li class="nav-item mx-1">
            <a class="nav-link {{ request()->is('prestamos_libros*') ? 'active' : '' }}" aria-current="page"
                href="{{ route('prestamos_libros.index') }}"><i class="fa-solid fa-duotone fa-books fa-rotate-270"></i>
                Préstamos de libros</a>
        </li>

        <li class="nav-item mx-1">
            <a class="nav-link {{ request()->is('prestamos_libros/reportes') ? 'active' : '' }}" aria-current="page"
                href="{{ route('prestamos_libros.reportes') }}"><i class="fa-solid fa-duotone fa-chart-column"></i>
                Reportes</a>
        </li>

        <li class="nav-item mx-1">
            <a class="nav-link {{ request()->is('prestamos_libros/crear') ? 'active' : '' }}" aria-current="page"
                href="{{ route('prestamos_libros.crear') }}"><i class="fa-solid fa-duotone fa-plus"></i>
                Crear préstamo</a>
        </li>
    </ul>
</ul>
