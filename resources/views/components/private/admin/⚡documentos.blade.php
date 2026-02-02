<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;
use App\Models\Document;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

new class extends Component {
    use WithFileUploads;

    public $search = null;
    public $category = null;
    public $documentId = null;

    public $name;
    public $version;
    public $price;
    public $description;
    public $category_id;
    public $active = true;
    public $file;
    public $oldFilePath;

    #[Computed]
    public function catalog()
    {
        return Document::with('category')->withCount('vistas_documentos')->withAvg('calificaciones_documentos', 'calificacion')->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))->when($this->category, fn($q) => $q->where('category_id', $this->category))->paginate(8);
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

    public function abrirDocumento($id = null)
    {
        $this->resetForm();

        if ($id) {
            $doc = Document::findOrFail($id);
            $this->documentId = $doc->id;
            $this->name = $doc->name;
            $this->version = $doc->version;
            $this->price = $doc->price;
            $this->description = $doc->description;
            $this->category_id = $doc->category_id;
            $this->active = $doc->active;
            $this->oldFilePath = $doc->file_path;
        }

        $this->dispatch('abrirDocumento');
    }

    public function cerrarDocumento()
    {
        $this->resetForm();
        $this->dispatch('cerrarDocumento');
    }

    private function resetForm()
    {
        $this->reset(['documentId', 'name', 'version', 'price', 'description', 'category_id', 'active', 'file', 'oldFilePath']);
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'version' => 'required|string|max:50',
            'price' => 'required|numeric',
            'description' => 'required',
            'category_id' => 'required|exists:categories,id',
            'file' => $this->documentId ? 'nullable|file|mimes:pdf' : 'required|file|mimes:pdf',
        ]);

        $filePath = $this->oldFilePath;

        if ($this->file) {
            if ($this->oldFilePath && Storage::disk('local')->exists('documents/' . $this->oldFilePath)) {
                Storage::disk('local')->delete('documents/' . $this->oldFilePath);
            }

            $fileName = Str::uuid() . '.' . $this->file->getClientOriginalExtension();
            $this->file->storeAs('documents', $fileName);

            $filePath = $fileName;
        }

        Document::updateOrCreate(
            ['id' => $this->documentId],
            [
                'name' => $this->name,
                'version' => $this->version,
                'price' => $this->price,
                'description' => $this->description,
                'category_id' => $this->category_id,
                'file_path' => $filePath,
                'active' => $this->active,
                'agregado_por' => auth()->id(),
            ],
        );

        session()->flash('success', $this->documentId ? 'Documento actualizado' : 'Documento creado');

        $this->cerrarDocumento();
    }

    public function confirmDelete($id)
    {
        $this->documentId = $id;
        $this->dispatch('confirmarEliminacion');
    }

    public function cerrarEliminacion()
    {
        $this->documentId = null;
        $this->dispatch('cerrarEliminacion');
    }

    public function delete()
    {
        $doc = Document::findOrFail($this->documentId);

        if ($doc->file_path && Storage::disk('local')->exists('documents/' . $doc->file_path)) {
            Storage::disk('local')->delete('documents/' . $doc->file_path);
        }

        $doc->delete();

        $this->cerrarEliminacion();
        session()->flash('success', 'Documento eliminado');
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
};

?>

<div class="bg-base-100 mt-2">

    <dialog id="delete_modal" class="modal" wire:ignore.self>
        <div class="modal-box">
            <h3 class="font-bold text-lg text-error">¿Eliminar documento?</h3>
            <p class="py-4 opacity-70">
                Esta acción eliminará también el archivo físico.
            </p>
            <div class="modal-action">
                <button class="btn btn-error" wire:click="delete">
                    Sí, eliminar
                </button>
                <button class="btn" wire:click="cerrarEliminacion">
                    Cancelar
                </button>
            </div>
        </div>
    </dialog>


    <dialog id="documento_modal" class="modal" wire:ignore.self>
        <div class="modal-box w-11/12 max-w-3xl">
            <h3 class="text-lg font-bold mb-4">
                {{ $documentId ? 'Editar documento' : 'Nuevo documento' }}
            </h3>

            <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <input class="input input-bordered w-full" placeholder="Nombre" wire:model="name">

                <input class="input input-bordered w-full" placeholder="Versión" wire:model="version">

                <input type="number" step="0.01" class="input input-bordered w-full" placeholder="Precio"
                    wire:model="price">

                <select class="select select-bordered w-full" wire:model="category_id">
                    <option value="">Categoría</option>
                    @foreach ($this->categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>

                <textarea class="textarea textarea-bordered col-span-2" placeholder="Descripción" wire:model="description"></textarea>

                <input type="file" class="file-input file-input-bordered col-span-2" wire:model="file">

                @if ($oldFilePath)
                    <p class="text-sm opacity-70 col-span-2">
                        Archivo actual: {{ basename($oldFilePath) }}
                    </p>
                @endif

                <label class="flex items-center gap-2 col-span-2">
                    <input type="checkbox" class="toggle" wire:model="active">
                    Activo
                </label>

                <div class="modal-action col-span-2">
                    <button type="submit" class="btn btn-primary">
                        Guardar
                    </button>
                    <button type="button" class="btn" wire:click="cerrarDocumento">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>

    </dialog>

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
                @foreach ($this->categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <button class="btn btn-primary" wire:click="resetFilters">
                Reiniciar
            </button>
        </div>

        <div class="max-w-5xl grid grid-cols-1 md:grid-cols-3 gap-10">

            @forelse ($this->catalog as $document)
                <div class="card bg-base-200 shadow-xl hover:shadow-2xl transition-all rounded-xl">
                    <div class="card-body space-y-3">

                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-lg leading-tight">
                                    {{ $document->name }}
                                </h3>
                                <p class="text-sm opacity-60">
                                    {{ $document->category->name ?? 'Sin categoría' }} · v{{ $document->version }}
                                </p>
                            </div>

                            @if ($document->active)
                                <span class="badge badge-success">Activo</span>
                            @else
                                <span class="badge badge-error">Inactivo</span>
                            @endif
                        </div>

                        <div class="text-2xl font-bold text-primary">
                            Bs {{ number_format($document->price, 2) }}
                        </div>
                        <div class="flex items-center justify-between text-sm opacity-80">
                            <div class="flex items-center gap-1">
                                <span class="material-icons-outlined text-yellow-500 text-base">star</span>
                                {{ number_format($document->calificaciones_documentos->avg('calificacion'), 1) }}
                            </div>

                            <div class="flex items-center gap-1">
                                <span class="material-icons-outlined text-base-content/60 text-base">visibility</span>
                                {{ $document->vistas_documentos->count() }}
                            </div>

                            <div class="flex items-center gap-1">
                                <span class="material-icons-outlined text-primary text-base">shopping_cart</span>
                                {{ $document->compras->count() }}
                            </div>
                        </div>

                        <div class="flex gap-2 pt-2">
                            <button wire:click="verOpiniones({{ $document->id }})"
                                class="btn btn-sm btn-primary flex-1">
                                Ver
                            </button>

                            <button wire:click="abrirDocumento({{ $document->id }})"
                                class="btn btn-sm btn-secondary btn-ghost">
                                <span class="material-icons-outlined text-sm">edit</span>
                            </button>

                            <button wire:click="confirmDelete({{ $document->id }})"
                                class="btn btn-sm btn-error btn-ghost">
                                <span class="material-icons-outlined text-sm">delete</span>
                            </button>
                        </div>

                    </div>
                </div>
            @empty
                <p class="col-span-3 text-center opacity-70">
                    No hay documentos disponibles
                </p>
            @endforelse


        </div>

        <div class="mt-6">
            {{ $this->catalog->links() }}
        </div>

        <button wire:click="abrirDocumento" class="btn btn-primary btn-circle fixed bottom-8 right-8 btn-xl">
            <span class="material-icons-outlined">add</span>
        </button>
    </div>

    @script
        <script>
            Livewire.on('abrirOpiniones', () => {
                document.getElementById('opiniones_modal').showModal();
            });
            Livewire.on('cerrarOpiniones', () => {
                document.getElementById('opiniones_modal').close();
            });
            Livewire.on('abrirDocumento', () => {
                document.getElementById('documento_modal').showModal();
            });
            Livewire.on('cerrarDocumento', () => {
                document.getElementById('documento_modal').close();
            });
            Livewire.on('confirmarEliminacion', () => {
                document.getElementById('delete_modal').showModal();
            });
            Livewire.on('cerrarEliminacion', () => {
                document.getElementById('delete_modal').close();
            });
        </script>
    @endscript
</div>
