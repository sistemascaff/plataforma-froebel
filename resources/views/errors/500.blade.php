@extends('layouts.errors')

@section('content')
    <h1>500 - Error interno del servidor</h1>
    <p>¡Ups! Algo salió mal de nuestro lado.</p>
    <a class="btn btn-primary" href="{{ route('main.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left me-1"></i>
        Volver
    </a>
@endsection
