<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Accesos_documento;
use App\Models\Document;
use Illuminate\Support\Facades\URL;

new class extends Component {
    public $search = null;
    public $category = null;

    #[Computed]
    public function documentos()
    {
        return Accesos_documento::query()
            ->where('user_id', auth()->id())
            ->where('estado', 'activo')
            ->whereHas('compra.intencion_compra.pago', function ($q) {
                $q->where('estado', 'aprobado');
            })
            ->whereHas('documento', function ($q) {
                $q->where('active', true);
            })
            ->when($this->search, function ($q) {
                $q->whereHas('documento', function ($doc) {
                    $doc->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->category, function ($q) {
                $q->whereHas('documento', function ($doc) {
                    $doc->where('category_id', $this->category);
                });
            })
            ->with(['documento.category', 'documento.calificaciones_documentos'])
            ->paginate(8);
    }

    #[Computed]
    public function categorias()
    {
        return Accesos_documento::query()
            ->where('user_id', auth()->id())
            ->where('estado', 'activo')
            ->whereHas('documento.category')
            ->with('documento.category')
            ->get()
            ->pluck('documento.category')
            ->unique('id');
    }

    public function resetFilters()
    {
        $this->reset(['search', 'category']);
    }

    public function getDocumentUrl(int $documentId)
    {
        $document = Document::findOrFail($documentId);

        abort_unless(auth()->user()->accesos_documentos()->where('documento_id', $document->id)->where('estado', 'activo')->exists(), 403, 'No tienes acceso a este documento');

        return URL::temporarySignedRoute(
            'documentos.ver',
            now()->addMinutes(30),
            ['document' => $document->id],
        );
    }
};
?>

<div class="px-6 py-2 h-full">
    <h3 class="text-3xl font-bold mb-5">Mis Documentos</h3>

    <div class="flex items-center gap-4 mb-4">
        <label class="input w-full">
            <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.3-4.3"></path>
                </g>
            </svg>
            <input type="search" placeholder="Buscar" wire:model.live.debounce.300ms="search" />
        </label>

        <select class="select" wire:model.live.debounce.300ms="category">
            <option value="" hidden>Buscar por categoría</option>
            @forelse ($this->categorias as $categoria)
                <option value="{{ $categoria->id }}">{{ $categoria->name }}</option>
            @empty
                <option disabled>No hay categorías</option>
            @endforelse
        </select>

        <button class="btn btn-primary btn-md md:btn-md" wire:click="resetFilters">
            Reiniciar
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        @forelse ($this->documentos as $acceso)
            <div class="card bg-base-200 shadow-lg hover:shadow-xl transition-all duration-300 w-xs">
                <div class="card-body gap-3">

                    <div class="flex justify-between">
                        <div class="flex flex-col">
                            <h2 class="card-title text-lg leading-tight">
                                {{ $acceso->documento->name }}
                            </h2>
                            <span class="text-xs text-base-content/50">
                                Versión {{ $acceso->documento->version }}
                            </span>
                        </div>
                        <div class="badge badge-primary badge-sm">
                            {{ $acceso->documento->category->name }}
                        </div>
                    </div>

                    <div class="flex items-center gap-2 text-sm">
                        <span class="material-icons-outlined text-yellow-500 text-base">
                            star
                        </span>
                        <span class="font-semibold">
                            {{ number_format($acceso->documento->calificaciones_documentos->avg('calificacion') ?? 0, 1) }}
                        </span>
                        <span class="text-base-content/50">/ 5.0</span>
                    </div>

                    <div class="card-actions justify-end mt-2">
                        <a href="{{ $this->getDocumentUrl($acceso->documento->id) }}" target="_blank"
                            class="btn btn-sm btn-primary btn-outline gap-2">
                            <span class="material-icons-outlined text-base">
                                visibility
                            </span>
                            Ver documento
                        </a>
                    </div>

                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="card bg-base-100 border border-base-300">
                    <div class="card-body text-center text-sm text-base-content/60">
                        No tienes documentos con acceso activo.
                    </div>
                </div>
            </div>
        @endforelse
    </div>

</div>
