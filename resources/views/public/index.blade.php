@extends('layouts.public.app')

@section('content')
    <nav class="sticky top-0 z-50">
        <livewire:public.navbar />
    </nav>
    <main class="flex-1">
        <livewire:public.hero />
        <livewire:public.benefits />
        <livewire:public.catalog />
    </main>
    <footer>
        <livewire:public.footer />
    </footer>
@endsection
