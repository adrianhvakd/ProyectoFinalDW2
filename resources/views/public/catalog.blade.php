@extends('layouts.public.catalog')

@section('title', 'Catálogo de Normas')

@section('content')

    <div class="sticky top-0 z-50">
        @livewire('public.navbar')
    </div>


    <main class=" bg-base-100">
        <div class="max-w-7xl mx-auto py-10">
            @livewire('private.list-catalogo')
        </div>
    </main>

@endsection
