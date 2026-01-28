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

    <div class="max-w-6xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">

            @forelse ($this->documents() as $document)
                <div class="card bg-base-200 shadow-xl hover:shadow-2xl transition-all rounded-xl">
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

                        <p class="text-sm text-base-content/80 line-clamp-3">
                            {{ $document->description }}
                        </p>

                        <div class="flex items-center justify-between mt-4">
                            <a href="#" class="btn btn-secondary btn-sm">
                                Comprar
                            </a>

                            <a href="#" class="btn btn-primary btn-sm">
                                Ver
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
</article>
