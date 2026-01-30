<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Document;
use App\Models\Category;

new class extends Component {
    public $search = null;
    public $category = null;
    #[Computed]
    public function catalog()
    {
        if ($this->search && $this->category) {
            return Document::with('category')
                ->withCount('vistas_documentos')
                ->withAvg('calificaciones_documentos', 'calificacion')
                ->where('name', 'like', '%' . $this->search . '%')
                ->where('category_id', $this->category)
                ->paginate(8);
        }
        if ($this->search) {
            return Document::with('category')
                ->withCount('vistas_documentos')
                ->withAvg('calificaciones_documentos', 'calificacion')
                ->where('name', 'like', '%' . $this->search . '%')
                ->paginate(8);
        }
        if ($this->category) {
            return Document::with('category')->withCount('vistas_documentos')->withAvg('calificaciones_documentos', 'calificacion')->where('category_id', $this->category)->paginate(8);
        }
        return Document::with('category')->withCount('vistas_documentos')->withAvg('calificaciones_documentos', 'calificacion')->paginate(8);
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
};
?>

<div class="bg-base-100 mt-2">

    <h3 class="text-3xl font-bold mb-5 px-6">Catálogo de Normas</h3>

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
                                        {{ $document->vistas_documentos_count }}
                                    </span>
                                </span>
                            </span>
                        </h3>

                        <p class="text-sm text-base-content/50 flex justify-between">
                            <span>{{ $document->category->name ?? 'Sin categoría' }}</span>
                            <span>Versión {{ $document->version }}</span>
                        </p>

                        <p class="text-sm text-base-content/80 line-clamp-3">
                            {{ $document->description }}
                        </p>

                        <div class="flex items-center justify-between mt-4">
                            <a href="#" class="btn btn-secondary btn-sm">
                                Comprar
                            </a>

                            <a href="#" class="btn btn-primary btn-sm">
                                Ver opiniones
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-span-3 text-center text-base-content/70">
                    No hay documentos disponibles
                </p>
            @endforelse

        </div>
    </div>

    {{ $this->catalog()->links() }}
</div>
