<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>{{ helper_titulo_pagina() }} | Error</title>

    <!-- Icono -->
    <link rel="icon" type="image/x-icon" href="{{ URL::to('/') }}/public/favicon.ico">
    <!-- Bootstrap CSS -->
    <link href="{{ asset('/public/dependencies/bootstrap-5.3.8/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome CSS -->
    <link href="{{ asset('/public/dependencies/fontawesome-pro-plus-v7.0.1/css/all.css') }}" rel="stylesheet">
</head>

<body class="d-flex flex-column min-vh-100">

    <main class="flex-grow-1 d-flex justify-content-center align-items-center">
        <div class="container text-center">
            <img class="img-fluid mx-auto bg-light rounded" alt="Logo"
                src="{{ URL::to('/') }}/public/img/error.png" style="width: 125px;">

            @yield('content')

        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="{{ asset('/public/dependencies/bootstrap-5.3.8/js/bootstrap.bundle.min.js') }}"></script>
    <script></script>
</body>

</html>
