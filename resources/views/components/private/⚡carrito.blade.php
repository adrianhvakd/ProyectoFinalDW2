<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\Pago;
use App\Models\intenciones_compra;
use App\Models\Notificaciones;

new class extends Component {
    use WithFileUploads;

    public $cartCount = 0;
    public $cartTotal = 0;
    public $step = 1;
    public $comprobante;

    protected $listeners = [
        'cartUpdated' => 'updateCart',
    ];

    protected $rules = [
        'comprobante' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ];

    public function mount()
    {
        $this->updateCart();
    }

    public function updateCart()
    {
        $cart = session()->get('cart', []);
        $this->cartCount = count($cart);
        $this->cartTotal = collect($cart)->sum('price');
    }

    public function verCarrito()
    {
        $this->step = 1;
        $this->dispatch('open-modal');
    }

    public function removeFromCart($documentId)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$documentId])) {
            unset($cart[$documentId]);
            session()->put('cart', $cart);
            $this->updateCart();
        }
    }

    public function checkout()
    {
        if ($this->cartCount === 0) {
            return;
        }
        $this->step = 2;
    }

    public function confirmarPago()
    {
        $this->validate();

        DB::transaction(function () {
            $filename = Str::uuid() . '.webp';
            $path = storage_path('app/private/comprobantes/' . $filename);

            // Intervention Image v3 (API correcta)
            $manager = new ImageManager(new Driver());

            $image = $manager
                ->read($this->comprobante->getRealPath())
                ->scale(width: 800)
                ->toWebp(80);

            $image->save($path);

            $pago = Pago::create([
                'user_id' => auth()->id(),
                'monto_total' => $this->cartTotal,
                'comprobante' => $filename,
                'estado' => 'pendiente',
            ]);

            foreach (session('cart', []) as $item) {
                intenciones_compra::create([
                    'user_id' => auth()->id(),
                    'documento_id' => $item['id'],
                    'pago_id' => $pago->id,
                    'precio' => $item['price'],
                    'estado' => 'pendiente',
                ]);
            }

            Notificaciones::create([
                'user_id' => auth()->id(),
                'titulo' => 'Compra registrada',
                'mensaje' => 'Tu pago fue recibido y está en proceso de aprobación.',
                'ruta' => 'historial',
            ]);

            session()->forget('cart');
            $this->updateCart();
            $this->step = 3;
            $this->comprobante = null;
        });
    }

    public function closeModal()
    {
        $this->dispatch('close-modal');
        $this->step = 1;
        $this->comprobante = null;
    }
};
?>

<div>
    <dialog id="carrito_modal" class="modal" wire:ignore.self>
        <div class="modal-box w-11/12 max-w-3xl">

            <ul class="steps w-full mb-6">
                <li class="step {{ $step >= 1 ? 'step-primary' : '' }}">Resumen</li>
                <li class="step {{ $step >= 2 ? 'step-primary' : '' }}">Pago</li>
                <li class="step {{ $step >= 3 ? 'step-primary' : '' }}">Confirmación</li>
            </ul>
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2" wire:click="closeModal">✕</button>
            @if ($step === 1)
                @if ($cartCount > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Documento</th>
                                <th>Precio</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (session('cart', []) as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item['name'] }}</td>
                                    <td>${{ $item['price'] }}</td>
                                    <td>
                                        <button class="btn btn-error btn-xs"
                                            wire:click="removeFromCart({{ $item['id'] }})">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2">Total</td>
                                <td colspan="2">{{ $cartTotal }} Bs.</td>
                            </tr>
                        </tfoot>
                    </table>

                    <button class="btn btn-primary mt-4 w-full" wire:click="checkout">
                        Continuar al pago
                    </button>
                @else
                    <p class="text-center">El carrito está vacío</p>
                @endif
            @endif

            @if ($step === 2)
                <div class="space-y-4">
                    <p class="font-semibold text-center">
                        Escanea el QR para pagar
                    </p>

                    <p class="text-center">
                        Monto a pagar: {{ $cartTotal }} Bs.
                    </p>

                    <img src="{{ asset('storage/images/qr_pago.webp') }}" class="w-48 mx-auto rounded">

                    <input type="file" wire:model="comprobante" class="file-input file-input-bordered w-full"
                        accept="image/*">

                    @error('comprobante')
                        <span class="text-error text-sm">{{ $message }}</span>
                    @enderror

                    <div class="flex justify-between">
                        <button class="btn" wire:click="$set('step',1)">
                            Volver
                        </button>
                        <button class="btn btn-primary" wire:click="confirmarPago">
                            Enviar comprobante
                        </button>
                    </div>
                </div>
            @endif

            @if ($step === 3)
                <div class="text-center space-y-4">
                    <span class="material-icons-outlined text-6xl text-success">
                        check_circle
                    </span>
                    <h3 class="text-lg font-bold">
                        Compra registrada
                    </h3>
                    <p>
                        Tu pago fue enviado correctamente.<br>
                        Te notificaremos cuando sea aprobado.
                    </p>

                    <button class="btn btn-primary" onclick="carrito_modal.close()">
                        Cerrar
                    </button>
                </div>
            @endif

        </div>
    </dialog>

    <div class="dropdown dropdown-center">
        <label tabindex="0" class="btn btn-ghost btn-circle">
            <div class="indicator">
                <span class="material-icons-outlined text-xl">shopping_cart</span>
                @if ($cartCount > 0)
                    <span class="badge badge-sm indicator-item bg-primary">
                        {{ $cartCount }}
                    </span>
                @endif
            </div>
        </label>

        <div tabindex="0" class="dropdown-content card w-64 bg-base-200 shadow">
            <div class="card-body">
                <span class="font-bold">{{ $cartCount }} items</span>
                <span class="text-sm">Subtotal: {{ $cartTotal }} Bs.</span>
                <button class="btn btn-primary btn-block" wire:click="verCarrito">
                    Ver carrito
                </button>
            </div>
        </div>
    </div>

    @script
        <script>
            Livewire.on('open-modal', () => {
                document.getElementById('carrito_modal').showModal();
            });
            Livewire.on('close-modal', () => {
                document.getElementById('carrito_modal').close();
            });
        </script>
    @endscript

</div>
