<!DOCTYPE html>
<html lang="en" data-bs-theme="{{ session('temaPreferido') ? session('temaPreferido') : 'dark' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ helper_titulo_pagina() }} | INICIAR SESIÓN</title>

    <!-- Icono -->
    <link rel="icon" type="image/x-icon" href="{{ URL::to('/') }}/public/favicon.ico">
    @include('components.dependencies_css')
</head>

<body>
    <div class="container">
        <div class="row justify-content-center align-items-center vh-100">
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header text-center">
                        <h1 class="fw-bold">PLATAFORMA CAFF</h1>
                        <img class="img-fluid mx-auto d-block w-50 h-50 rounded border border-dark" alt="Logo"
                            src="{{ URL::to('/') }}/public/img/logo_plataforma_froebel.png">
                    </div>
                    <div class="card-body">
                        <form action="{{ route('login.verificar') }}" method="POST">

                            @csrf

                            <div class="input-group mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-duotone fa-user"></i></span>
                                    <input type="text" class="form-control" name="correo" required
                                        placeholder="Usuario"
                                        value="{{ session('login_correo') ? session('login_correo') : '' }}"
                                        autofocus>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-duotone fa-key"></i></span>
                                    <input type="password" class="form-control" name="contrasenha" required
                                        placeholder="Contraseña" id="passwordInput"
                                        value="{{ session('login_contrasenha') ? session('login_contrasenha') : '' }}">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fa-solid fa-duotone fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Acceder <i
                                    class="fa-solid fa-duotone fa-sign-in"></i></button>
                        </form>

                        @if (session('mensaje'))
                            <br>
                            <div class="alert alert-warning">
                                <h5 class="font font-weight-bold"><i
                                        class="fa-solid fa-duotone fa-triangle-exclamation"></i> ¡ATENCIÓN!
                                </h5>
                                <a>{{ session('mensaje') }}</a>
                            </div>
                        @endif

                    </div>
                    <div class="card-footer text-center rounded">
                        <strong>Copyright &copy; {{ date('Y') }}.</strong>
                        Todos los derechos reservados.
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('passwordInput');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    </script>
    @include('components.dependencies_js')
</body>

</html>
