@extends('layouts.errors')

@section('content')
    <h1>419 - Página expirada</h1>
    <p>Tu sesión ha expirado o el formulario ya no es válido.</p>
    <a class="btn btn-primary" href="{{ route('main.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left me-1"></i>
        Volver
    </a>
@endsection
