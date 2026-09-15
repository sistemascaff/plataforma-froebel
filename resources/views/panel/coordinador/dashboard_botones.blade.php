<div class="row">
    <div class="col-6 col-md-4 col-lg-2 d-flex justify-content-center my-3">
        <a class="btn btn-sq-lg btn-info" href="{{ route('asignaturas.index') }}">
            <div>
                <i class="fa-solid fa-duotone fa-book-reader fa-2xl"></i>
                <br>Asignaturas
            </div>
        </a>
    </div>

    <div class="col-6 col-md-4 col-lg-2 d-flex justify-content-center my-3">
        <a class="btn btn-sq-lg btn-info" href="{{ route('listas_asignaturas.index') }}">
            <div>
                <i class="fa-solid fa-duotone fa-clipboard-list fa-2xl"></i>
                <br>Gestión de listas de asignaturas
            </div>
        </a>
    </div>

    <div class="col-6 col-md-4 col-lg-2 d-flex justify-content-center my-3">
        <a class="btn btn-sq-lg btn-info" href="{{ route('estudiantes_asistencias.index') }}">
            <div>
                <i class="fa-solid fa-duotone fa-clipboard-list-check fa-2xl"></i>
                <br>Gestión de asistencias de estudiantes
            </div>
        </a>
    </div>
</div>