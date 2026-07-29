@extends('layouts.errors')

@section('content')
    <h1>403 - Acceso denegado</h1>
    <p>Lo sentimos, no tienes los permisos necesarios para ver esta sección.</p>
    <a class="btn btn-primary" href="{{ route('dashboard') }}">
        <i class="fa-solid fa-duotone fa-dashboard me-1"></i>
        Volver al panel
    </a>
@endsection
