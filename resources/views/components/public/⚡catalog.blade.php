<?php

use Livewire\Component;
use App\Models\Document;
use App\Models\Compra;
use App\Models\Vistas_documento;

new class extends Component {
    public $documents = [];
    public function documents()
    {
        $this->documents = Document::with('category')->withCount('vistas_documentos')->withAvg('calificaciones_documentos', 'calificacion')->where('active', true)->latest()->take(6)->get();

        return $this->documents;
    }
};
?>

<article class="py-20 bg-base-100">
    <h3 class="text-3xl font-bold text-center mb-10">Catálogo de Normas</h3>

    <div class="mx-auto px-6 max-w-6xl">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            @forelse ($this->documents() as $document)
                <div class="card bg-base-200 shadow-xl hover:shadow-2xl transition-all rounded-xl max-w-xs">
                    <div class="card-body">

                        <h3 class="card-title font-semibold flex items-center justify-between">
                            {{ $document->name }}

                            <span class="flex items-center gap-3">
                                <span class="flex items-center gap-1">
                                    <span class="material-icons-outlined text-yellow-500 text-lg">star</span>
                                    <span class="text-sm">
                                        {{ number_format($document->calificaciones_documentos_avg_calificacion ?? 0, 1) }}
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
                            <span>Versión: {{ $document->version }}</span>
                        </p>

                        <div class="text-2xl font-bold text-primary">
                            Bs {{ number_format($document->price, 2) }}
                        </div>

                    </div>
                </div>
            @empty
                <p class="col-span-3 text-center text-base-content/70">
                    No hay documentos disponibles
                </p>
            @endforelse
        </div>
        <div class="flex justify-end">
            <a href="{{ route('public-catalogo') }}" class="btn btn-primary btn-sm mt-4">Ver todos</a>
        </div>
    </div>
</article>
