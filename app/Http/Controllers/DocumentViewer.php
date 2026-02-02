<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentViewer extends Controller
{
    public function show(Request $request, Document $document)
    {
        \Log::info('=== VIEWER: Iniciando ===', [
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'full_url' => $request->fullUrl(),
            'has_signature' => $request->hasValidSignature(),
            'query_params' => $request->query(),
        ]);

        if (! $request->hasValidSignature()) {
            \Log::error('VIEWER: Firma inválida', [
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

        \Log::info('VIEWER: Verificando acceso', [
            'has_access' => $hasAccess,
        ]);

        abort_unless($hasAccess, 403, 'No tienes acceso a este documento');

        \Log::info('VIEWER: Acceso concedido, mostrando vista');

        return view('private.user.viewer', compact('document'));
    }
}
