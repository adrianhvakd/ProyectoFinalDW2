<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<section class="hero min-h-[60vh] relative overflow-hidden">
    <img 
        src="{{ asset('storage/images/hero.png') }}" 
        alt="Fondo"
        class="absolute inset-0 w-full h-full object-cover z-0"
    >

    <div class="absolute inset-0 bg-black/50 z-10"></div>

    <div class="hero-content text-center text-neutral-content relative z-20">
        <div class="max-w-xl">
            <h1 class="text-4xl md:text-5xl font-bold">
                Acceso digital a normas ISO y estándares internacionales
            </h1>

            <p class="py-6 opacity-90">
                Accede de forma rápida, segura y organizada a una biblioteca completa de normas ISO y estándares
                internacionales. Mantente siempre actualizado con documentación confiable.
            </p>

            <div class="flex justify-center gap-4">
                <a href="#" class="btn btn-primary">
                    Explorar normas
                </a>
                <a href="#" class="btn btn-outline btn-white">
                    Ver paquetes
                </a>
            </div>
        </div>
    </div>
</section>
