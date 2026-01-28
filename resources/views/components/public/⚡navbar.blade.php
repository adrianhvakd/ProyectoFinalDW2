<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="navbar bg-base-100 shadow-sm px-4">
    <a href="{{ route('home') }}" class="flex items-center space-x-2">
        <img src="{{ asset('storage/images/logo.png') }}" alt="Logo" class="h-10 w-10 object-contain">
        <p class="text-xl font-bold hidden lg:block">
            Norm<span class="text-[#605DFF]">Flow</span>
        </p>
    </a>


    <div class="flex-1 flex justify-end space-x-2">
        @if (!auth()->check())
            <a href="{{ route('login') }}" class="btn hover:btn-neutral btn-sm btn-outline">Iniciar Sesión</a>
            <a href="#" class="btn btn-sm btn-primary">Registrarse</a>
        @else
            <a href="{{ route('dashboard') }}" class="btn hover:btn-neutral btn-sm btn-outline">Dashboard</a>
            <form action="{{ route('logout') }}" method="post">
                @csrf
                <button type="submit" class="btn btn-primary btn-sm">Cerrar Sesión</button>
            </form>
        @endif
    </div>
</div>
