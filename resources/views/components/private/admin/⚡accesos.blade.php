<?php

use Livewire\Component;
use App\Models\Accesos_documento;
use Livewire\Attributes\Computed;

new class extends Component {
    public $id_acceso = null;
    public $estado_acceso = null;

    #[Computed]
    public function accesos()
    {
        return Accesos_documento::paginate(10);
    }

    public function confirmar($id, $estado)
    {
        $this->id_acceso = $id;
        $this->estado_acceso = $estado;
        $this->dispatch('open-modal');
    }

    public function confirmar_modal()
    {
        if ($this->estado_acceso == 'activo') {
            $acceso = Accesos_documento::find($this->id_acceso);
            $acceso->estado = 'revocado';
            $acceso->update();
        } else {
            $acceso = Accesos_documento::find($this->id_acceso);
            $acceso->estado = 'activo';
            $acceso->update();
        }
        return redirect()->route('admin-accesos');
    }
};
?>

<div class="p-6 space-y-6">

    <dialog id="acceso_modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">
                {{ $estado_acceso == 'activo' ? '¿Estás seguro de revocar el acceso?' : '¿Estás seguro de activar el acceso?' }}
            </h3>
            <p class="py-4">
                {{ $estado_acceso == 'activo' ? 'Se revocará el acceso al documento' : 'Se activará el acceso al documento' }}
            </p>
            <div class="modal-action">
                <button class="btn btn-accent" wire:click="confirmar_modal">Confirmar</button>
                <button class="btn" onclick="acceso_modal.close()">Cancelar</button>
            </div>
        </div>
    </dialog>
    <h2 class="text-2xl font-bold">Gestión de Accesos</h2>
    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-200">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Documento</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->accesos as $acceso)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $acceso->compra->user->name }}</td>
                        <td>{{ $acceso->compra->documento->name }}</td>
                        <td>{{ $acceso->compra->created_at->format('d/m/Y') }}</td>
                        <td>
                            <span
                                class="badge badge-{{ $acceso->estado == 'activo' ? 'success' : 'error' }}">{{ $acceso->estado }}</span>
                        </td>
                        <td>
                            @if ($acceso->estado == 'activo')
                                <button class="btn btn-xs btn-error btn-outline"
                                    wire:click="confirmar('{{ $acceso->id }}', '{{ $acceso->estado }}')">
                                    <span class="material-icons-outlined" style="font-size: 1rem;">block</span> Revocar
                                </button>
                            @else
                                <button class="btn btn-xs btn-success btn-outline"
                                    wire:click="confirmar('{{ $acceso->id }}', '{{ $acceso->estado }}')">
                                    <span class="material-icons-outlined" style="font-size: 1rem;">check</span> Activar
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-base-content/60">
                            No hay accesos registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $this->accesos->links() }}
    </div>

    @script
        <script>
            const acceso_modal = document.getElementById('acceso_modal');
            Livewire.on('open-modal', () => {
                acceso_modal.showModal();
            });
            Livewire.on('close-modal', () => {
                acceso_modal.close();
            });
        </script>
    @endscript
</div>
