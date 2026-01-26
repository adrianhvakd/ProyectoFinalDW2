<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<div class="navbar bg-base-100 shadow-sm px-4 sticky top-0 z-50">
    <a href="{{ route('home') }}" class="flex items-center space-x-2">
        <img src="{{ asset('storage/logo2.png') }}" alt="Logo" class="h-10 w-10 object-contain">
        <p class="text-xl font-bold text-[#0A2540] hidden lg:block">
            Norm<span class="text-[#00D4AA]">Flow</span>
        </p>
    </a>


    <div class="flex-1 flex justify-end space-x-2">
        @if (!auth()->check())
            <a href="{{ route('login') }}" class="btn btn-accent btn-sm btn-soft">Iniciar Sesión</a>
            <a href="#" class="btn btn-sm btn-soft">Registrarse</a>
        @else
            <a href="#" class="btn btn-accent btn-sm btn-soft">Dashboard</a>
            <form action="{{ route('logout') }}" method="post">
                @csrf
                <button type="submit" class="btn btn-error btn-sm btn-soft">Cerrar Sesión</button>
            </form>
        @endif
    </div>
</div>
