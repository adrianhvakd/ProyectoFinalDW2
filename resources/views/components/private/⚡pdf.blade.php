<?php

use Livewire\Component;
use Illuminate\Support\Facades\URL;
use App\Models\Document;

new class extends Component {
    public Document $document;
    public string $streamUrl;

    public function mount(Document $document)
    {
        $hasAccess =
            auth()->check() &&
            $document
                ->usuariosConAcceso()
                ->where('user_id', auth()->id())
                ->where('estado', 'activo')
                ->exists();

        abort_unless($hasAccess, 403, 'No tienes acceso a este documento');

        $this->document = $document;

        $this->streamUrl = URL::temporarySignedRoute('documentos.stream', now()->addMinutes(30), ['document' => $document->id]);

        \Log::info('Componente DocumentViewer montado', [
            'document_id' => $document->id,
            'user_id' => auth()->id(),
            'stream_url' => $this->streamUrl,
        ]);

        $this->dispatch('pdf-url-ready', streamUrl: $this->streamUrl);
    }
};
?>

<div id="pdf-container" class="w-screen h-screen overflow-auto bg-neutral-600">

    <div id="pdf-loading" class="flex flex-col items-center justify-center pt-12 text-white">
        <div class="loading loading-spinner loading-lg"></div>
        <p class="mt-4">Cargando documento...</p>
    </div>

    <canvas id="pdf-canvas" class="hidden mx-auto pointer-events-none select-none"></canvas>

    <div id="pdf-controls"
        class="fixed bottom-5 left-1/2 -translate-x-1/2 bg-black/80 px-5 py-2.5 rounded-lg hidden items-center gap-2.5 z-9999">

        <button id="prev-page" class="btn btn-sm btn-ghost text-white">
            <span class="material-icons-outlined">arrow_back</span> Anterior
        </button>

        <span id="page-info" class="text-white mx-4">
            <span id="current-page">1</span> / <span id="total-pages">-</span>
        </span>

        <button id="next-page" class="btn btn-sm btn-ghost text-white">
            <span class="material-icons-outlined">arrow_forward</span> Siguiente
        </button>

        <button id="zoom-out" class="btn btn-sm btn-ghost text-white ml-4">
            <span class="material-icons-outlined">zoom_out</span>
        </button>

        <button id="zoom-in" class="btn btn-sm btn-ghost text-white">
            <span class="material-icons-outlined">zoom_in</span>
        </button>
    </div>
</div>

@script
    <script>
        (function() {
            function initPdfViewer(streamUrl) {
                if (typeof pdfjsLib === 'undefined') {
                    console.log('Esperando a que PDF.js cargue...');
                    setTimeout(() => initPdfViewer(streamUrl), 100);
                    return;
                }

                console.log('PDF.js cargado, inicializando viewer...');
                console.log('URL del stream:', streamUrl);

                pdfjsLib.GlobalWorkerOptions.workerSrc =
                    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

                let pdfDoc = null;
                let pageNum = 1;
                let pageRendering = false;
                let pageNumPending = null;
                let scale = 1.5;
                const canvas = document.getElementById('pdf-canvas');
                const ctx = canvas.getContext('2d');

                // Cargar PDF
                fetch(streamUrl, {
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/pdf',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        console.log('Response status:', response.status);
                        console.log('Response headers:', {
                            contentType: response.headers.get('content-type'),
                            contentLength: response.headers.get('content-length')
                        });

                        if (!response.ok) {
                            return response.text().then(text => {
                                console.error('Response body:', text.substring(0, 500));
                                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                            });
                        }

                        return response.arrayBuffer();
                    })
                    .then(data => {
                        console.log('PDF descargado:', data.byteLength, 'bytes');
                        return pdfjsLib.getDocument({
                            data: data
                        }).promise;
                    })
                    .then(pdf => {
                        console.log('PDF cargado exitosamente. Paginas:', pdf.numPages);
                        pdfDoc = pdf;
                        document.getElementById('total-pages').textContent = pdf.numPages;
                        document.getElementById('pdf-loading').style.display = 'none';
                        document.getElementById('pdf-canvas').classList.remove('hidden');
                        document.getElementById('pdf-canvas').classList.add('block');
                        document.getElementById('pdf-controls').classList.remove('hidden');
                        document.getElementById('pdf-controls').classList.add('flex');
                        renderPage(pageNum);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('pdf-loading').innerHTML =
                            '<div class="text-center">' +
                            '<p class="text-red-500 text-lg mb-2">Error al cargar el documento</p>' +
                            '<p class="text-white text-sm mb-2">' + error.message + '</p>' +
                            '<p class="text-gray-400 text-xs">Revisa la consola para más detalles</p>' +
                            '</div>';
                    });

                function renderPage(num) {
                    pageRendering = true;
                    pdfDoc.getPage(num).then(page => {
                        const viewport = page.getViewport({
                            scale: scale
                        });
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;
                        page.render({
                            canvasContext: ctx,
                            viewport: viewport
                        }).promise.then(() => {
                            pageRendering = false;
                            if (pageNumPending !== null) {
                                renderPage(pageNumPending);
                                pageNumPending = null;
                            }
                        });
                    });
                    document.getElementById('current-page').textContent = num;
                }

                function queueRenderPage(num) {
                    if (pageRendering) {
                        pageNumPending = num;
                    } else {
                        renderPage(num);
                    }
                }

                document.getElementById('prev-page').addEventListener('click', () => {
                    if (pageNum <= 1) return;
                    pageNum--;
                    queueRenderPage(pageNum);
                });

                document.getElementById('next-page').addEventListener('click', () => {
                    if (pageNum >= pdfDoc.numPages) return;
                    pageNum++;
                    queueRenderPage(pageNum);
                });

                document.getElementById('zoom-in').addEventListener('click', () => {
                    scale += 0.25;
                    queueRenderPage(pageNum);
                });

                document.getElementById('zoom-out').addEventListener('click', () => {
                    if (scale <= 0.5) return;
                    scale -= 0.25;
                    queueRenderPage(pageNum);
                });

                document.addEventListener('keydown', e => {
                    if (!pdfDoc) return;
                    if (e.key === 'ArrowLeft' && pageNum > 1) {
                        pageNum--;
                        queueRenderPage(pageNum);
                    }
                    if (e.key === 'ArrowRight' && pageNum < pdfDoc.numPages) {
                        pageNum++;
                        queueRenderPage(pageNum);
                    }
                });

                document.addEventListener('contextmenu', e => e.preventDefault());
                document.addEventListener('selectstart', e => e.preventDefault());

                document.addEventListener('keydown', e => {
                    if ((e.ctrlKey || e.metaKey) && ['s', 'p', 'c'].includes(e.key.toLowerCase())) {
                        e.preventDefault();
                        e.stopPropagation();
                        alert('Esta acción está deshabilitada');
                        return false;
                    }
                    if (e.key === 'PrintScreen') {
                        e.preventDefault();
                        alert('Las capturas de pantalla están deshabilitadas');
                        return false;
                    }
                    if (e.key === 'F12') {
                        e.preventDefault();
                        return false;
                    }
                }, true);

                window.addEventListener('beforeprint', e => {
                    e.preventDefault();
                    alert('La impresión está deshabilitada');
                    return false;
                });

                setTimeout(() => {
                    alert('Tu sesión ha expirado por seguridad');
                    window.location.href = '/documentos';
                }, 30 * 60 * 1000);

                window.addEventListener('beforeunload', () => {
                    if (pdfDoc) pdfDoc.destroy();
                });
            }

            $wire.on('pdf-url-ready', (event) => {
                console.log('Evento recibido:', event);
                initPdfViewer(event.streamUrl);
            });
        })();
    </script>
@endscript

<style>
    @media print {
        body {
            display: none !important;
        }
    }
</style>
