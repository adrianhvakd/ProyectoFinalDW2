<?php

use Livewire\Component;
use App\Models\User;
use Livewire\Attributes\Computed;

new class extends Component {
    public $userId = null;

    #[Computed]
    public function usuarios()
    {
        return User::where('id', '!=', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function desactivarUsuario($userId)
    {
        User::where('id', $userId)->update([
            'active' => false,
        ]);
    }

    public function activarUsuario($userId)
    {
        User::where('id', $userId)->update([
            'active' => true,
        ]);
    }

    public function historialOpen($userId)
    {
        $this->userId = $userId;
        $this->dispatch('open-modal');
    }

    public function historialClose()
    {
        $this->userId = null;
    }
};
?>

<div class="p-6 space-y-6">

    <dialog id="historial_modal" class="modal">
        <div class="modal-box max-h-[80vh] overflow-y-auto">
            @if ($this->userId)
                @livewire('private.historial', ['id' => $this->userId])
            @endif
            <div class="modal-action">
                <button class="btn" onclick="historial_modal.close()">Cerrar</button>
            </div>
        </div>
    </dialog>
    <h2 class="text-2xl font-bold">Gestión de Usuarios</h2>

    <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-200">
        <table class="table table-zebra">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Estado</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($this->usuarios as $usuario)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        <td class="font-medium">
                            {{ $usuario->name }}
                        </td>

                        <td class="text-sm text-base-content/70">
                            {{ $usuario->email }}
                        </td>

                        <td>
                            @if ($usuario->active)
                                <span class="badge badge-success badge-sm">Activo</span>
                            @else
                                <span class="badge badge-error badge-sm">Inactivo</span>
                            @endif
                        </td>

                        <td class="flex flex-wrap gap-2 justify-center">
                            <button class="btn btn-xs btn-primary btn-outline"
                                wire:click="historialOpen({{ $usuario->id }})">
                                Historial
                            </button>

                            @if ($usuario->active)
                                <button wire:click="desactivarUsuario({{ $usuario->id }})"
                                    class="btn btn-xs btn-error btn-outline">
                                    Dar de baja
                                </button>
                            @else
                                <button wire:click="activarUsuario({{ $usuario->id }})"
                                    class="btn btn-xs btn-success btn-outline">
                                    Reactivar
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-base-content/60">
                            No hay usuarios registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $this->usuarios->links() }}
    </div>

    @script
        <script>
            const historial_modal = document.getElementById('historial_modal');
            Livewire.on('open-modal', () => {
                historial_modal.showModal();
            });
        </script>
    @endscript
</div>
