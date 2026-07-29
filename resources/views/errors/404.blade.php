@extends('layouts.errors')

@section('content')
    <h1>404 - Página no encontrada</h1>
    <p>La página que estás buscando no existe, ha sido eliminada o cambió de lugar.</p>
    <a class="btn btn-primary" href="{{ url()->previous() }}">
        <i class="fa-solid fa-duotone fa-arrow-left me-1"></i>
        Volver
    </a>
@endsection
