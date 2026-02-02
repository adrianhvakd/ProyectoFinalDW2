<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Pago;
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
        return User::find($this->id)->pagos()->paginate(6);
    }
};
?>

<div class="px-6 py-2">

    <h2 class="text-3xl font-bold mb-5">Historial de Pagos</h2>

    <div class="space-y-4">
        @forelse ($this->pagos as $pago)
            <div tabindex="0"
                class="collapse collapse-arrow bg-base-200 border border-base-300 shadow-md hover:shadow-xl transition-all duration-300">

                <div class="collapse-title flex items-center justify-between">
                    <h3 class="font-semibold text-lg">
                        {{ $pago->created_at->format('d/m/Y H:i:s') }}
                    </h3>
                    @if ($pago->estado == 'aprobado')
                        <span class="badge badge-success">
                            {{ $pago->estado }}
                        </span>
                    @else
                        @if ($pago->estado == 'pendiente')
                            <span class="badge badge-warning">
                                {{ $pago->estado }}
                            </span>
                        @else
                            <span class="badge badge-error">
                                {{ $pago->estado }}
                            </span>
                        @endif
                    @endif
                </div>

                <div class="collapse-content">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                        <p>
                            <span class="font-semibold text-base-content/70">Precio</span><br>
                            {{ $pago->monto_total }} Bs.
                        </p>

                        <p>
                            <span class="font-semibold text-base-content/70">Documentos</span><br>
                            @foreach ($pago->intenciones as $intencion)
                                @if ($loop->last)
                                    {{ $intencion->documento->name }}
                                @else
                                    {{ $intencion->documento->name }},
                                @endif
                            @endforeach
                        </p>

                        <p>
                            <span class="font-semibold text-base-content/70">Fecha de verificación</span><br>
                            {{ $pago->fecha_verificacion ? $pago->fecha_verificacion->format('d/m/Y') : 'Sin verificar' }}
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
