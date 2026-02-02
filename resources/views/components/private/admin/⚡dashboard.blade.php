<?php

use Livewire\Component;
use App\Models\User;
use App\Models\Compra;
use App\Models\Accesos_documento;
use App\Models\Intenciones_compra;

new class extends Component {
    public $usuariosActivos;
    public $documentosVendidos;
    public $pagosPendientes;
    public $accesosTotales;

    public function mount()
    {
        $this->usuariosActivos = User::where('active', true)->count();
        $this->documentosVendidos = Compra::count();
        $this->pagosPendientes = Intenciones_compra::whereHas('pago', fn($q) => $q->where('estado', 'pendiente'))->count();
        $this->accesosTotales = Accesos_documento::count();
    }
};
?>

<div class="p-6 space-y-6">

    <h1 class="text-3xl font-bold">Panel de Administración</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="stat bg-base-200">
            <div class="stat-title">Usuarios</div>
            <div class="stat-value text-primary">{{ $usuariosActivos }}</div>
        </div>

        <div class="stat bg-base-200">
            <div class="stat-title">Documentos vendidos</div>
            <div class="stat-value">{{ $documentosVendidos }}</div>
        </div>

        <div class="stat bg-base-200">
            <div class="stat-title">Pagos pendientes</div>
            <div class="stat-value text-warning">{{ $pagosPendientes }}</div>
        </div>

        <div class="stat bg-base-200">
            <div class="stat-title">Accesos totales</div>
            <div class="stat-value text-success">{{ $accesosTotales }}</div>
        </div>
    </div>

</div>
