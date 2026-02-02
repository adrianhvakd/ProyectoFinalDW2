<?php

use Livewire\Component;
use App\Models\Pago;
use App\Models\Intenciones_compra;
use App\Models\Compra;
use App\Models\Accesos_documento;
use App\Models\Notificaciones;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use App\Models\User;

new class extends Component {
    public $id = null;
    public $url = null;
    public $filename = null;
    #[Computed]
    public function pagos()
    {
        return Pago::with(['user', 'intenciones.documento'])->paginate(5);
    }

    #[Computed]
    public function pagosPendientes()
    {
        return Pago::with(['user', 'intenciones.documento'])
            ->where('estado', 'pendiente')
            ->paginate(5);
    }

    public function verPago($id)
    {
        $this->id = $id;
        $pago = Pago::findOrFail($this->id);
        $this->filename = $pago->comprobante;
        $this->url = URL::temporarySignedRoute('comprobante.show', now()->addMinutes(5), ['filename' => $this->filename]);
        $this->dispatch('open-modal');
    }

    public function cerrarModal()
    {
        $this->id = null;
        $this->filename = null;
        $this->url = null;
    }

    public function aprobarPago()
    {
        DB::transaction(function () {
            $pago = Pago::with('intenciones')->findOrFail($this->id);

            $pago->update([
                'estado' => 'aprobado',
                'fecha_verificacion' => now(),
                'verificado_por' => auth()->id(),
            ]);

            foreach ($pago->intenciones as $intencion) {
                $intencion->update(['estado' => 'completada']);

                $compra = Compra::create([
                    'intencion_compra_id' => $intencion->id,
                    'user_id' => $intencion->user_id,
                    'documento_id' => $intencion->documento_id,
                ]);

                Accesos_documento::create([
                    'user_id' => $intencion->user_id,
                    'documento_id' => $intencion->documento_id,
                    'compra_id' => $compra->id,
                    'estado' => 'activo',
                ]);

                Notificaciones::create([
                    'user_id' => $intencion->user_id,
                    'titulo' => 'Compra aprobada',
                    'mensaje' => "Tu compra del documento '{$intencion->documento->name}' fue aprobada exitosamente.",
                    'ruta' => 'mis-documentos',
                ]);
            }

            flash()->use('theme.aurora')->option('timeout', 3000)->success('Pago aprobado y compras registradas');
        });

        $this->cerrarModal();
        return redirect()->route('admin-pagos');
    }

    public function rechazarPago()
    {
        DB::transaction(function () {
            $pago = Pago::findOrFail($this->id);
            $pago->update([
                'estado' => 'rechazado',
                'fecha_verificacion' => now(),
                'verificado_por' => auth()->id(),
            ]);

            foreach ($pago->intenciones as $intencion) {
                $intencion->update(['estado' => 'cancelada']);

                Notificaciones::create([
                    'user_id' => $intencion->user_id,
                    'titulo' => 'Pago rechazado',
                    'mensaje' => "Tu pago del documento '{$intencion->documento->name}' fue rechazado.",
                    'ruta' => 'historial',
                ]);
            }

            flash()->use('theme.aurora')->option('timeout', 3000)->success('Pago rechazado');
        });

        $this->cerrarModal();
        return redirect()->route('admin-pagos');
    }
};
?>

<div class="p-6 space-y-6">
    <dialog id="pago_modal" class="modal">
        <div class="modal-box">
            <h3 class="text-lg font-bold">Comprobante</h3>
            <img src="{{ $this->url }}" alt="Comprobante no encontrado" draggable="false"
                class="max-w-full max-h-full object-contain">
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
                @forelse ($this->pagosPendientes as $pago)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $pago->user->name }}</td>
                        <td>
                            @foreach ($pago->intenciones as $intencion)
                                {{ $intencion->documento->name }}@if (!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </td>
                        <td>{{ $pago->created_at->format('d/m/Y H:i') }}</td>
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
                                No hay pagos pendientes.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $this->pagosPendientes->links() }}
        </div>

        <h2 class="text-2xl font-bold">Gestión de Pagos</h2>
        <div class="overflow-x-auto rounded-box border border-base-content/5 bg-base-200">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Usuario</th>
                        <th>Documento</th>
                        <th>Verificado por</th>
                        <th>Total</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->pagos as $pago)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $pago->user->name }}</td>
                            <td class="max-w-64 overflow-hidden text-ellipsis">
                                @foreach ($pago->intenciones as $intencion)
                                    {{ $intencion->documento->name }}@if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                            </td>
                            @if ($pago->verificado_por)
                                <td>{{ User::find($pago->verificado_por)->email }}</td>
                            @endif
                            <td>{{ $pago->monto_total }}</td>
                            <td>{{ $pago->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ($pago->estado == 'pendiente')
                                    <span class="badge badge-warning">{{ $pago->estado }}</span>
                                @elseif ($pago->estado == 'aprobado')
                                    <span class="badge badge-success">{{ $pago->estado }}</span>
                                @else
                                    <span class="badge badge-error">{{ $pago->estado }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-base-content/60">
                                    No hay pagos pendientes.
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
