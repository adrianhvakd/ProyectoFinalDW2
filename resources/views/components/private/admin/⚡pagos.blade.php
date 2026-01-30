<?php

use Livewire\Component;
use App\Models\Pago;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\URL;

new class extends Component {
    public $comprobante = null;
    public $url = null;
    public $id = null;
    #[Computed]
    public function pagos()
    {
        return Pago::where('estado', 'pendiente')->paginate(5);
    }

    public function verPago($id)
    {
        $this->id = $id;
        $this->comprobante = Pago::find($this->id)->comprobante;
        $this->url = URL::temporarySignedRoute('comprobante.show', now()->addMinutes(5), ['filename' => $this->comprobante]);
        $this->dispatch('open-modal');
    }

    public function cerrarModal()
    {
        $this->comprobante = null;
        $this->id = null;
    }

    public function aprobarPago()
    {
        try {
            Pago::where('id', $this->id)->update([
                'estado' => 'aprobado',
                'fecha_verificacion' => now(),
                'verificado_por' => auth()->user()->id,
            ]);
            flash()->use('theme.aurora')->option('timeout', 3000)->success('Pago aprobado');
        } catch (\Exception $e) {
            flash()->use('theme.aurora')->option('timeout', 3000)->error('Error al aprobar el pago');
        }
        return redirect()->route('admin-pagos');
    }

    public function rechazarPago()
    {
        try {
            Pago::where('id', $this->id)->update([
                'estado' => 'rechazado',
                'fecha_verificacion' => now(),
                'verificado_por' => auth()->user()->id,
            ]);
            flash()->use('theme.aurora')->option('timeout', 2000)->success('Pago rechazado');
        } catch (\Exception $e) {
            flash()->use('theme.aurora')->option('timeout', 2000)->error('Error al rechazar el pago');
        }
        return redirect()->route('admin-pagos');
    }
};
?>

<div class="p-6 space-y-6">
    <dialog id="pago_modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Comprobante</h3>
            <img src="{{ $this->url }}" alt="Comprobante no encontrado">
            <div class="modal-action">
                <button class="btn btn-accent" wire:click="aprobarPago()">Aprobar</button>
                <button class="btn btn-error" wire:click="rechazarPago()">Rechazar</button>
                <button class="btn" onclick="pago_modal.close()">Cerrar</button>
            </div>
        </div>
    </dialog>
    <h2 class="text-2xl font-bold">Gestión de Pagos Pendientes</h2>
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
                @forelse ($this->pagos as $pago)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $pago->compra->usuario->name }}</td>
                        <td>{{ $pago->compra->documento->name }}</td>
                        <td>{{ $pago->created_at }}</td>
                        <td>
                            <span class="badge badge-warning">Pendiente</span>
                        </td>
                        <td>
                            <button class="btn btn-xs btn-primary btn-outline"
                                wire:click="verPago('{{ $pago->id }}')">
                                Ver
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-base-content/60">
                            No hay pagos registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $this->pagos->links() }}
    </div>

    @script
        <script>
            const pago_modal = document.getElementById('pago_modal');
            Livewire.on('open-modal', () => {
                pago_modal.showModal();
            });
        </script>
    @endscript
</div>
