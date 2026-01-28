<?php

use Livewire\Component;

new class extends Component
{
    //
};
?>

<section class="py-20 bg-base-200">
    <h3 class="text-3xl font-bold text-center mb-10">Beneficios de NormFlow</h3>
    <div class="max-w-6xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 text-center">

            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow rounded-xl">
                <div class="card-body items-center">
                    <div class="flex items-center justify-center w-15 h-15 rounded-full bg-primary/10 mb-4 text-primary">
                        <span class="material-icons-outlined" style="font-size: 2rem;">
                            lock
                        </span>
                    </div>
                    <h3 class="card-title text-lg md:text-xl font-semibold">Acceso seguro</h3>
                    <p class="text-sm md:text-base text-base-content/70 mt-2">
                        Autenticación protegida y control de acceso para tus documentos.
                    </p>
                </div>
            </div>

            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow rounded-xl">
                <div class="card-body items-center">
                    <div class="flex items-center justify-center w-15 h-15 rounded-full bg-primary/10 mb-4 text-primary">
                        <span class="material-icons-outlined" style="font-size: 2rem;">
                            article
                        </span>
                    </div>
                    <h3 class="card-title text-lg md:text-xl font-semibold">Documentos actualizados</h3>
                    <p class="text-sm md:text-base text-base-content/70 mt-2">
                        Normas siempre en su versión más reciente y validada.
                    </p>
                </div>
            </div>

            <div class="card bg-base-100 shadow-xl hover:shadow-2xl transition-shadow rounded-xl">
                <div class="card-body items-center">
                    <div class="flex items-center justify-center w-15 h-15 rounded-full bg-primary/10 mb-4 text-primary">
                        <span class="material-icons-outlined" style="font-size: 2rem;">
                            shopping_cart
                        </span>
                    </div>
                    <h3 class="card-title text-lg md:text-xl font-semibold">Compra segura</h3>
                    <p class="text-sm md:text-base text-base-content/70 mt-2">
                        Compra con garantía y protección de datos.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>
