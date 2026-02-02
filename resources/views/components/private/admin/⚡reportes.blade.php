<?php

use Livewire\Component;
use App\Models\Report;
use App\Models\Notificaciones;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use App\Jobs\GenerateReportJob;

new class extends Component {
    #[Computed]
    public function reports()
    {
        return Report::with('user')->latest()->paginate(5);
    }

    public function generarReporte($tipo)
    {
        GenerateReportJob::dispatch($tipo, auth()->id());
        Notificaciones::create([
            'user_id' => auth()->id(),
            'titulo' => 'Su reporte está siendo generado',
            'mensaje' => 'Se le notificará cuando el reporte esté listo',
            'ruta' => 'admin-reportes',
        ]);
        flash()->use('theme.aurora')->option('timeout', 2500)->success('Su reporte está siendo generado');
    }

    public function descargar($id)
    {
        $report = Report::findOrFail($id);

        if (Storage::disk('private_reports')->exists($report->file_path)) {
            return Storage::disk('private_reports')->download($report->file_path);
        }

        flash()->use('theme.aurora')->option('timeout', 2500)->error('El archivo no se encontró');
    }
};
?>

<div class="p-6 space-y-6">

    <h2 class="text-3xl font-bold">Reportes del sistema</h2>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

        @foreach ($this->reports as $report)
            <div class="card bg-base-200 shadow">
                <div class="card-body">
                    <h3 class="font-semibold">{{ $report->name }}</h3>

                    <p class="text-sm opacity-60">
                        {{ $report->created_at->diffForHumans() }}
                    </p>

                    <p class="text-xs opacity-50">
                        Creado por: {{ $report->user->name }}
                    </p>

                    <div class="flex gap-2 pt-3">
                        <button wire:click="descargar({{ $report->id }})" class="btn btn-sm btn-primary flex-1">
                            Descargar
                        </button>
                    </div>
                </div>
            </div>
        @endforeach

    </div>

    <div class="fixed bottom-8 right-8 dropdown dropdown-top dropdown-end">
        <label tabindex="0" class="btn btn-primary btn-circle btn-xl shadow-lg">
            <span class="material-icons-outlined">add</span>
        </label>

        <ul tabindex="0" class="dropdown-content menu p-2 shadow bg-base-200 rounded-box w-72">

            <li><a wire:click="generarReporte('documentos-mas-vistos')">
                    Documentos más vistos</a></li>

            <li><a wire:click="generarReporte('accesos-documentos')">
                    Accesos a documentos</a></li>
        </ul>
    </div>
</div>
