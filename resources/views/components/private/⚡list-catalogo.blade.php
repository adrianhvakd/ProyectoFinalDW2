<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Document;
use App\Models\Category;

new class extends Component {
    public $search = null;
    public $category = null;
    public $documentId = null;
    public $selectedDocumentId = null;
    #[Computed]
    public function catalog()
    {
        if ($this->search && $this->category) {
            return Document::with('category')
                ->withCount('vistas_documentos')
                ->withAvg('calificaciones_documentos', 'calificacion')
                ->where('active', true)
                ->where('name', 'like', '%' . $this->search . '%')
                ->where('category_id', $this->category)
                ->paginate(8);
        }
        if ($this->search) {
            return Document::with('category')
                ->withCount('vistas_documentos')
                ->withAvg('calificaciones_documentos', 'calificacion')
                ->where('active', true)
                ->where('name', 'like', '%' . $this->search . '%')
                ->paginate(8);
        }
        if ($this->category) {
            return Document::with('category')->withCount('vistas_documentos')->withAvg('calificaciones_documentos', 'calificacion')->where('active', true)->where('category_id', $this->category)->paginate(8);
        }
        return Document::with('category')->withCount('vistas_documentos')->withAvg('calificaciones_documentos', 'calificacion')->where('active', true)->paginate(8);
    }

    #[Computed]
    public function categories()
    {
        return Category::all();
    }

    public function resetFilters()
    {
        $this->search = null;
        $this->category = null;
    }

    public function verOpiniones($documentId)
    {
        $this->documentId = $documentId;
        $this->dispatch('abrirOpiniones');
    }

    public function cerrarOpiniones()
    {
        $this->documentId = null;
        $this->dispatch('cerrarOpiniones');
    }

    public function confirmarCarrito($documentId)
    {
        $this->selectedDocumentId = $documentId;
        $this->dispatch('abrirConfirmarCarrito');
    }

    public function cerrarConfirmarCarrito()
    {
        $this->documentId = null;
        $this->dispatch('cerrarConfirmarCarrito');
    }

    public function agregarAlCarrito()
    {
        if (!$this->selectedDocumentId) {
            return;
        }

        $document = Document::findOrFail($this->selectedDocumentId);

        $cart = session()->get('cart', []);

        if (count($cart) >= 5) {
            flash()->use('theme.aurora')->option('timeout', 3000)->warning('El maximo de documentos en el carrito es 5');
            $this->dispatch('cerrarConfirmarCarrito');
            return;
        }

        if (isset($cart[$document->id])) {
            flash()->use('theme.aurora')->option('timeout', 3000)->warning('El documento ya se encuentra en el carrito');
            $this->dispatch('cerrarConfirmarCarrito');
            return;
        } else {
            $cart[$document->id] = [
                'id' => $document->id,
                'name' => $document->name,
                'price' => $document->price ?? 0,
            ];
        }

        session()->put('cart', $cart);

        $this->dispatch('cartUpdated');

        $this->dispatch('cerrarConfirmarCarrito');
        flash()->use('theme.aurora')->option('timeout', 3000)->success('Documento agregado al carrito');
        $this->selectedDocumentId = null;
    }
};
?>

<article class="bg-base-100 mt-2">

    <h3 class="text-3xl font-bold mb-5 px-6">Catálogo de Normas</h3>

    <dialog id="opiniones_modal" class="modal" wire:ignore.self>
        <div class="modal-box w-11/12 max-w-3xl">
            <h3 class="text-lg font-bold mb-4">Documento</h3>

            @if ($this->documentId)
                @livewire('public.opiniones', ['documentId' => $this->documentId])
            @endif

            <div class="modal-action">
                <button class="btn" wire:click="cerrarOpiniones">Cerrar</button>
            </div>
        </div>
    </dialog>

    <dialog id="confirmar_carrito_modal" class="modal" wire:ignore.self>
        <div class="modal-box w-11/12 max-w-3xl">
            <h3 class="text-lg font-bold mb-4">¿Estás seguro de agregar este documento al carrito?</h3>

            <div class="modal-action">
                <button class="btn btn-primary" wire:click="agregarAlCarrito">Agregar</button>
                <button class="btn" wire:click="cerrarConfirmarCarrito">Cancelar</button>
            </div>
        </div>
    </dialog>



    <div class="px-6 mx-auto">
        <div class="flex items-center gap-4 mb-4">
            <label class="input w-full">
                <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none"
                        stroke="currentColor">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </g>
                </svg>
                <input type="search" placeholder="Buscar" wire:model.live.debounce.300ms="search" />
            </label>

            <select class="select" wire:model.live.debounce.300ms="category">
                <option value="" selected hidden>Buscar por categoría</option>
                @foreach ($this->categories() as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <button class="btn btn-primary btn-md md:btn-md" wire:click="resetFilters">
                Reiniciar
            </button>
        </div>

        <div class="max-w-5xl grid grid-cols-1 md:grid-cols-3 gap-10">

            @forelse ($this->catalog() as $document)
                <div class="card bg-base-200 shadow-xl hover:shadow-2xl transition-all rounded-xl">
                    <div class="card-body">

                        <h3 class="card-title font-semibold flex items-center justify-between">
                            {{ $document->name }}

                            <span class="flex items-center gap-3">
                                <span class="flex items-center gap-1">
                                    <span class="material-icons-outlined text-yellow-500 text-lg">star</span>
                                    <span class="text-sm">
                                        {{ number_format($document->calificaciones_documentos->avg('calificacion'), 1) }}
                                    </span>
                                </span>

                                <span class="flex items-center gap-1">
                                    <span class="material-icons-outlined text-base-content/60 text-lg">visibility</span>
                                    <span class="text-sm">
                                        {{ $document->vistas_documentos->count() }}
                                    </span>
                                </span>
                            </span>
                        </h3>

                        <p class="text-sm text-base-content/50 flex justify-between">
                            <span>{{ $document->category->name ?? 'Sin categoría' }}</span>
                            <span>Versión {{ $document->version }}</span>
                        </p>

                        <div class="text-2xl font-bold text-primary">
                            Bs {{ number_format($document->price, 2) }}
                        </div>

                        <div class="flex items-center justify-between mt-4">
                            @if (auth()->check())
                                @if ($document->compras->contains('user_id', Auth::id()))
                                    <div class="badge badge-outline badge-success">Comprado</div>
                                @else
                                    <button wire:click="confirmarCarrito({{ $document->id }})"
                                        class="btn btn-secondary btn-sm">
                                        Comprar
                                    </button>
                                @endif
                            @else
                                <div class="flex items-center gap-2">
                                    <p class="text-sm text-base-content/50">
                                        Debes iniciar sesión para comprar
                                        <a href="{{ route('login') }}" class="text-secondary">
                                            Iniciar sesión
                                        </a>
                                    </p>
                                </div>
                            @endif

                            <button wire:click="verOpiniones({{ $document->id }})" class="btn btn-primary btn-sm">
                                Ver opiniones
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-span-3 text-center text-base-content/70">
                    No hay documentos disponibles
                </p>
            @endforelse

        </div>
        {{ $this->catalog()->links() }}
    </div>

    @script
        <script>
            Livewire.on('abrirOpiniones', () => {
                document.getElementById('opiniones_modal').showModal();
            });

            Livewire.on('cerrarOpiniones', () => {
                document.getElementById('opiniones_modal').close();
            });

            Livewire.on('abrirConfirmarCarrito', () => {
                document.getElementById('confirmar_carrito_modal').showModal();
            });

            Livewire.on('cerrarConfirmarCarrito', () => {
                document.getElementById('confirmar_carrito_modal').close();
            });
        </script>
    @endscript
</article>
