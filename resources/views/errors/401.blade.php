@extends('layouts.errors')

@section('content')
    <h1>401 - No autorizado</h1>
    <p>Necesitas iniciar sesión para poder acceder a esta página.</p>
    <a class="btn btn-primary" href="{{ route('login') }}">
        <i class="fa-solid fa-duotone fa-sign-in me-1"></i>
        Iniciar sesión
    </a>
@endsection
