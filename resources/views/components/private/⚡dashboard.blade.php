<?php

use Livewire\Component;
use App\Models\Compra;
use App\Models\Accesos_documento;
use App\Models\Intenciones_Compra;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $totalCompras;
    public $accesosActivos;
    public $pagosPendientes;
    public $ultimosDocumentos;

    public function mount()
    {
        $user = Auth::user();
        $this->totalCompras = Compra::where('user_id', $user->id)->count();
        $this->accesosActivos = Accesos_Documento::where('user_id', $user->id)->where('estado', 'activo')->count();
        $this->pagosPendientes = Intenciones_Compra::where('user_id', $user->id)->whereHas('pago', fn($q) => $q->where('estado', 'pendiente'))->count();
        $this->ultimosDocumentos = Accesos_Documento::with('documento')->where('user_id', $user->id)->where('estado', 'activo')->latest()->take(5)->get();
    }
};
?>

<div class="p-6 space-y-6">

    <h1 class="text-3xl font-bold">Bienvenido</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="stat bg-base-200 rounded-xl">
            <div class="stat-title">Compras realizadas</div>
            <div class="stat-value text-primary">{{ $totalCompras }}</div>
        </div>

        <div class="stat bg-base-200 rounded-xl">
            <div class="stat-title">Accesos activos</div>
            <div class="stat-value text-success">{{ $accesosActivos }}</div>
        </div>

        <div class="stat bg-base-200 rounded-xl">
            <div class="stat-title">Pagos pendientes</div>
            <div class="stat-value text-warning">{{ $pagosPendientes }}</div>
        </div>
    </div>

    <div class="card bg-base-100 w-full">
        <div class="card-body w-full p-0">
            <h2 class="card-title">Mis últimos documentos</h2>

            <ul class="divide-y rounded-xl border border-base-300 bg-base-200 gap-2 px-4 min-h-10">
                @forelse($ultimosDocumentos as $acceso)
                    <li class="py-3 flex justify-between items-center">
                        <div>
                            <p class="font-semibold">{{ $acceso->documento->name }}</p>
                            <span class="text-xs text-gray-500">
                                Versión {{ $acceso->documento->version }}
                            </span>
                        </div>
                        <a href="#" class="btn btn-sm btn-primary btn-outline">
                            Ver
                        </a>
                    </li>
                @empty
                    <li class="text-sm text-gray-500">
                        Aún no tienes documentos activos.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>

</div>
