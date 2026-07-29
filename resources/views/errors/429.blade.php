@extends('layouts.errors')

@section('content')
    <h1>429 - Demasiadas peticiones</h1>
    <p>Has realizado demasiadas solicitudes en muy poco tiempo. Por favor, espera un momento y vuelve a intentarlo.</p>
    <a class="btn btn-primary" href="{{ route('main.index') }}">
        <i class="fa-solid fa-duotone fa-arrow-left me-1"></i>
        Volver
    </a>
@endsection
