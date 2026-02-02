<?php

namespace App\Jobs;

use App\Models\Accesos_documento;
use App\Models\Document;
use App\Models\Notificaciones;
use App\Models\Report;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class GenerateReportJob implements ShouldQueue
{
    use Queueable;

    protected string $tipo;

    protected int $userId;

    public function __construct(string $tipo, int $userId)
    {
        $this->tipo = $tipo;
        $this->userId = $userId;
    }

    public function handle(): void
    {
        $filename = $this->tipo.'-'.now()->format('YmdHis').'.pdf';
        $path = "reports/{$filename}";

        switch ($this->tipo) {

            case 'documentos-mas-vistos':
                $data = Document::withCount('vistas_documentos')
                    ->orderByDesc('vistas_documentos_count')
                    ->take(10)
                    ->get();

                $view = 'private.admin.reports.documentos-mas-vistos';
                break;

            case 'accesos-documentos':
                $data = Accesos_documento::with(['documento', 'user'])
                    ->latest()
                    ->get();

                $view = 'private.admin.reports.accesos-documentos';
                break;

            default:
                return;
        }

        $pdf = Pdf::loadView($view, [
            'data' => $data,
            'generated_at' => now(),
        ]);

        Storage::disk('local')->put($path, $pdf->output());

        Report::create([
            'name' => ucfirst(str_replace('-', ' ', $this->tipo)),
            'file_path' => $filename,
            'creado_por' => $this->userId,
        ]);

        Notificaciones::create([
            'user_id' => $this->userId,
            'titulo' => 'Reporte generado exitosamente',
            'mensaje' => 'Su reporte está listo para descargar',
            'ruta' => 'admin-reportes',
        ]);
    }
}
