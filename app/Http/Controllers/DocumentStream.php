<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use setasign\Fpdi\Tcpdf\Fpdi;

class DocumentStream extends Controller
{
    public function stream(Request $request, Document $document)
    {
        \Log::info('=== STREAM: Iniciando ===', [
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'full_url' => $request->fullUrl(),
            'has_signature' => $request->hasValidSignature(),
            'query_params' => $request->query(),
        ]);

        if (! $request->hasValidSignature()) {
            \Log::error('STREAM: Firma inválida', [
                'url' => $request->fullUrl(),
                'expires' => $request->get('expires'),
                'signature' => $request->get('signature'),
            ]);
            abort(403, 'Enlace expirado o inválido');
        }

        $hasAccess = auth()->check() &&
            $document->usuariosConAcceso()
                ->where('user_id', auth()->id())
                ->where('estado', 'activo')
                ->exists();

        \Log::info('STREAM: Verificando acceso', [
            'has_access' => $hasAccess,
        ]);

        abort_unless($hasAccess, 403, 'No tienes acceso a este documento');

        $path = storage_path("app/private/documents/{$document->file_path}");

        \Log::info('STREAM: Verificando archivo', [
            'path' => $path,
            'exists' => file_exists($path),
        ]);

        if (! file_exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        try {
            $pdf = new Fpdi;

            $pdf->SetCreator('Sistema de Documentos');
            $pdf->SetAuthor(auth()->user()->name);
            $pdf->SetTitle($document->name);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);

            $pageCount = $pdf->setSourceFile($path);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($tplId);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplId);

                $this->addWatermark($pdf, $size);
            }

            \Log::info('STREAM: PDF generado exitosamente');

            $pdfContent = $pdf->Output('', 'S');

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="'.$document->name.'.pdf"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate, private')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0')
                ->header('X-Frame-Options', 'SAMEORIGIN')
                ->header('Content-Security-Policy', "frame-ancestors 'self'")
                ->header('X-Content-Type-Options', 'nosniff');

        } catch (\Exception $e) {
            \Log::error('STREAM: Error generando PDF', [
                'document_id' => $document->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            abort(500, 'Error al generar el documento');
        }
    }

    private function addWatermark(Fpdi $pdf, array $size)
    {
        $user = auth()->user();

        $pdf->SetFont('helvetica', 'B', 40);
        $pdf->SetTextColor(220, 220, 220);
        $pdf->SetAlpha(0.15);

        $centerX = $size['width'] / 2;
        $centerY = $size['height'] / 2;

        $watermarkText = strtoupper($user->email);
        $textWidth = $pdf->GetStringWidth($watermarkText);

        $pdf->StartTransform();
        $pdf->Rotate(45, $centerX, $centerY);
        $pdf->Text($centerX - ($textWidth / 2), $centerY, $watermarkText);
        $pdf->StopTransform();

        $pdf->SetAlpha(1);

        $pdf->SetFont('helvetica', '', 7);
        $pdf->SetTextColor(120, 120, 120);

        $footerText = $user->name.' | '.now()->format('d/m/Y H:i');
        $footerWidth = $pdf->GetStringWidth($footerText);

        $pdf->Text($size['width'] - $footerWidth - 5, $size['height'] - 5, $footerText);
    }
}
