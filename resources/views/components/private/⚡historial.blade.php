<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\User;

new class extends Component {
    public $id = null;
    public function mount(int $id)
    {
        if ($id == auth()->user()->id) {
            $this->id = $id;
        }
    }

    #[Computed]
    public function pagos()
    {
        return User::find($this->id)->compras()->paginate(6);
    }
};
?>

<div class="px-6 py-2">

    <h2 class="text-3xl font-bold mb-5">Historial de Pagos</h2>

    <div class="space-y-4">
        @forelse ($this->pagos as $compra)
            <div tabindex="0"
                class="collapse collapse-arrow bg-base-100 border border-base-300 shadow-md hover:shadow-xl transition-all duration-300">

                <div class="collapse-title flex items-center justify-between">
                    <h3 class="font-semibold text-lg">
                        {{ $compra->documento->name }}
                    </h3>
                    <span class="text-base-content/50">
                        {{ $compra->documento->category->name }}
                    </span>
                    @if ($compra->pago->estado == 'aprobado')
                        <span class="badge badge-success">
                            {{ $compra->pago->estado }}
                        </span>
                    @else
                        @if ($compra->pago->estado == 'rechazado')
                            <span class="badge badge-error">
                                {{ $compra->pago->estado }}
                            </span>
                        @else
                            <span class="badge badge-warning">
                                {{ $compra->pago->estado }}
                            </span>
                        @endif
                    @endif
                </div>

                <div class="collapse-content">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                        <p>
                            <span class="font-semibold text-base-content/70">Precio</span><br>
                            {{ $compra->precio }}
                        </p>

                        <p>
                            <span class="font-semibold text-base-content/70">Fecha de compra</span><br>
                            {{ $compra->created_at->format('d/m/Y') }}
                        </p>

                        <p>
                            <span class="font-semibold text-base-content/70">Fecha de verificación</span><br>
                            {{ $compra->pago?->fecha_verificacion ? $compra->pago->fecha_verificacion->format('d/m/Y') : 'Sin verificar' }}
                        </p>

                    </div>

                </div>
            </div>

        @empty
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body text-center">
                    <span class="text-base-content/60">
                        No hay compras registradas.
                    </span>
                </div>
            </div>
        @endforelse
    </div>

</div>
