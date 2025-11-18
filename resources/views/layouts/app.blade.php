<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ helper_titulo_pagina() }} | {{ $head_title }}</title>
    <!-- Icono -->
    <link rel="icon" type="image/x-icon" href="{{ URL::to('/') }}/public/favicon.ico">
    <!-- Token para formularios -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('components.dependencies_css')

</head>

<body class="d-flex flex-column min-vh-100">

    @include('components.app.header')

    <main class="flex-grow-1">
        <div class="container">
            @yield('content')
        </div>
    </main>
    
    @include('components.app.modal_sign_out')

    @include('components.app.footer')

    @include('components.dependencies_js')

    @yield('scripts')
</body>

</html>
