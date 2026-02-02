<?php

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Document;
use App\Models\Vistas_documento;
use App\Models\Calificaciones_documento;

new class extends Component {
    public $documentId = null;
    public $document = null;

    public $opinions;
    public $perPage = 5;
    public $page = 1;
    public $loading = false;

    public $showDescription = false;

    public $newRating = 5;
    public $newComment = '';
    public $userRating = null;

    public $showRatingForm = false;

    public function mount($documentId)
    {
        if (auth()->check()) {
            if (auth()->user()->role != 'admin') {
                Vistas_documento::create([
                    'user_id' => auth()->id() ?? null,
                    'documento_id' => $documentId,
                ]);
            }
        } else {
            Vistas_documento::create([
                'user_id' => null,
                'documento_id' => $documentId,
            ]);
        }

        $this->reset(['opinions', 'page']);
        $this->loading = true;
        $this->documentId = $documentId;

        $this->opinions = collect();

        $this->document = Document::with(['category', 'calificaciones_documentos.user'])
            ->withCount('vistas_documentos')
            ->withAvg('calificaciones_documentos', 'calificacion')
            ->findOrFail($documentId);

        $this->checkUserRating();
        $this->loadMore();

        $this->loading = false;
    }

    public function toggleDescription()
    {
        $this->showDescription = !$this->showDescription;
    }

    public function checkUserRating()
    {
        $this->userRating = Calificaciones_documento::where('user_id', auth()->id())
            ->where('documento_id', $this->documentId)
            ->first();

        if ($this->userRating) {
            $this->newRating = $this->userRating->calificacion;
            $this->newComment = $this->userRating->comentario ?? '';
        }
    }

    public function toggleRatingForm()
    {
        $this->showRatingForm = !$this->showRatingForm;

        if ($this->showRatingForm && $this->userRating) {
            $this->newRating = $this->userRating->calificacion;
            $this->newComment = $this->userRating->comentario ?? '';
        }
    }

    public function submitRating()
    {
        $this->validate([
            'newRating' => 'required|integer|min:1|max:5',
            'newComment' => 'nullable|string|max:500',
        ]);

        if ($this->userRating) {
            $this->userRating->update([
                'calificacion' => $this->newRating,
                'comentario' => $this->newComment,
            ]);
        } else {
            Calificaciones_documento::create([
                'user_id' => auth()->id(),
                'documento_id' => $this->documentId,
                'calificacion' => $this->newRating,
                'comentario' => $this->newComment,
            ]);
        }

        $this->reset(['opinions', 'page', 'showRatingForm']);
        $this->mount($this->documentId);
    }

    public function cancelRating()
    {
        $this->showRatingForm = false;

        if ($this->userRating) {
            $this->newRating = $this->userRating->calificacion;
            $this->newComment = $this->userRating->comentario ?? '';
        } else {
            $this->newRating = 5;
            $this->newComment = '';
        }
    }

    public function loadMore()
    {
        if (!$this->documentId) {
            return;
        }

        $userOpinion = Calificaciones_documento::with('user')
            ->where('documento_id', $this->documentId)
            ->where('user_id', auth()->id())
            ->first();

        $newOpinions = Calificaciones_documento::with('user')
            ->where('documento_id', $this->documentId)
            ->where('user_id', '!=', auth()->id())
            ->latest()
            ->skip(($this->page - 1) * $this->perPage)
            ->take($this->perPage)
            ->get();

        if ($this->page === 1 && $userOpinion) {
            $this->opinions = collect([$userOpinion])->merge($newOpinions);
        } else {
            $this->opinions = $this->opinions->merge($newOpinions);
        }

        $this->page++;
    }
};
?>

<div class="h-full mt-0 pt-0">

    @if ($loading)
        <div class="flex justify-center py-10">
            <span class="loading loading-spinner loading-lg"></span>
        </div>
    @endif

    @if ($document && !$loading)
        <div class="card bg-base-200 shadow-xl mb-6 cursor-pointer hover:bg-base-300 transition-colors"
            wire:click="toggleDescription">
            @if (!$showRatingForm)
                <div class="card-body">

                    @if (!$showDescription)
                        <h3 class="card-title font-semibold flex items-center justify-between">
                            {{ $document->name }}

                            <span class="flex items-center gap-3">
                                <span class="flex items-center gap-1">
                                    <span class="material-icons-outlined text-yellow-500 text-lg">
                                        star
                                    </span>
                                    <span class="text-sm">
                                        {{ number_format($document->calificaciones_documentos->avg('calificacion') ?? 0, 1) }}
                                    </span>
                                </span>

                                <span class="flex items-center gap-1">
                                    <span class="material-icons-outlined text-base-content/60 text-lg">
                                        visibility
                                    </span>
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

                        <p class="text-xs text-base-content/40 italic mt-1">
                            Click para ver descripción
                        </p>
                    @else
                        <div class="max-h-32 overflow-y-auto">
                            <p class="text-xs">
                                {{ $document->description }}
                            </p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <div>
            @if (!$showRatingForm)
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-lg">
                        Calificaciones ({{ $opinions->count() }})
                    </h4>
                    @if (auth()->check())
                        <button wire:click="toggleRatingForm" class="btn btn-sm btn-primary gap-2">
                            <span class="material-icons-outlined text-base">
                                {{ $userRating ? 'edit' : 'add' }}
                            </span>
                            {{ $userRating ? 'Editar mi calificación' : 'Agregar calificación' }}
                        </button>
                    @else
                        <p class="text-base-content/60 text-sm ml-4 lg:ml-0">
                            Debes iniciar sesión para calificar
                            <a href="{{ route('login') }}" class="text-secondary">
                                Iniciar sesión
                            </a>
                        </p>
                    @endif
                </div>

                @if ($opinions->isEmpty())
                    <div class="text-center py-8">
                        <span class="material-icons-outlined text-6xl text-base-content/20">
                            rate_review
                        </span>
                        <p class="text-sm opacity-60 mt-2">
                            Este documento aún no tiene calificaciones
                        </p>
                    </div>
                @else
                    <div class="max-h-96 overflow-y-auto space-y-2 pr-2">
                        @foreach ($opinions as $opinion)
                            <div
                                class="card shadow {{ $opinion->user_id === auth()->id() ? 'bg-primary/10 border-2 border-primary' : 'bg-base-200' }}">
                                <div class="card-body p-4">

                                    <div class="flex items-center gap-3 mb-2">
                                        <img src="{{ asset('storage/images/profile/' . $opinion->user->profile_picture) }}"
                                            class="w-10 h-10 rounded-full" draggable="false">

                                        <div class="flex-1">
                                            <div class="flex items-center gap-2">
                                                <p class="font-semibold text-xs">
                                                    {{ $opinion->user->name }}
                                                </p>
                                                @if ($opinion->user_id === auth()->id())
                                                    <span class="badge badge-primary badge-xs">Tú</span>
                                                @endif
                                            </div>

                                            <p class="text-xs flex items-center gap-1 opacity-70">
                                                <span class="material-icons-outlined text-yellow-500"
                                                    style="font-size: 1rem">
                                                    star
                                                </span>
                                                <span> {{ $opinion->calificacion }} / 5 </span>
                                            </p>
                                        </div>

                                    </div>

                                    @if ($opinion->comentario)
                                        <p
                                            class="text-xs {{ $opinion->user_id === auth()->id() ? 'font-medium' : '' }}">
                                            {{ $opinion->comentario }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        <div wire:intersect="loadMore" class="h-1"></div>
                    </div>
                @endif
            @else
                <div class="card">
                    <div class="card-body p-4">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold flex items-center gap-2">
                                {{ $userRating ? 'Editar mi calificación' : 'Nueva calificación' }}
                            </h4>

                            <button wire:click="cancelRating" class="btn btn-sm ">
                                <span class="material-icons-outlined">
                                    arrow_back
                                </span>
                                Volver
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="text-xs font-medium mb-2 block">
                                    Calificación <span class="text-error">*</span>
                                </label>
                                <div class="rating rating-lg gap-1">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <input type="radio" wire:model.live="newRating" value="{{ $i }}"
                                            class="mask mask-star-2 bg-yellow-500 cursor-pointer" name="rating" />
                                    @endfor
                                </div>
                                <p class="text-xs text-base-content/50 mt-1">
                                    Seleccionado: {{ $newRating }} / 5
                                </p>
                            </div>

                            <div>
                                <label class="text-xs font-medium mb-2 block">
                                    Comentario (opcional)
                                </label>
                                <textarea wire:model="newComment" class="textarea textarea-bordered w-full text-sm" rows="4" maxlength="500"
                                    placeholder="Comparte tu opinión sobre este documento..."></textarea>
                                <p class="text-xs text-base-content/50 mt-1">
                                    {{ strlen($newComment) }}/500 caracteres
                                </p>
                                @error('newComment')
                                    <p class="text-xs text-error mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex gap-2">
                                <button wire:click="submitRating" class="btn btn-primary flex-1 gap-2">
                                    <span class="material-icons-outlined text-base">
                                        {{ $userRating ? 'save' : 'send' }}
                                    </span>
                                    {{ $userRating ? 'Guardar cambios' : 'Publicar calificación' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

</div>
