<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\DocumentStream;
use App\Http\Controllers\DocumentViewer;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('public.index');
})->name('home');

Route::get('/login', [Auth::class, 'login'])->name('login');
Route::get('/register', [Auth::class, 'register'])->name('register');
Route::post('/logout', [Auth::class, 'logout'])->name('logout');

Route::get('/dashboard', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->role == 'admin') {
        return redirect()->route('admin-dashboard');
    }

    return view('private.user.dashboard');
})->name('dashboard');

Route::get('/catalogo', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->role == 'admin') {
        return redirect()->route('admin-dashboard');
    }

    return view('private.user.catalogo');
})->name('catalogo');

Route::get('/documentos', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->role == 'admin') {
        return redirect()->route('admin-dashboard');
    }

    return view('private.user.mis-documentos');
})->name('mis-documentos');

Route::get('/historial', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->role == 'admin') {
        return redirect()->route('admin-dashboard');
    }

    return view('private.user.historial');
})->name('historial');

Route::get('/admin/dashboard', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->role != 'admin') {
        return redirect()->route('dashboard');
    }

    return view('private.admin.dashboard');
})->name('admin-dashboard');

Route::get('/admin/usuarios', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->role != 'admin') {
        return redirect()->route('dashboard');
    }

    return view('private.admin.usuarios');
})->name('admin-usuarios');

Route::get('/admin/pagos', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->role != 'admin') {
        return redirect()->route('dashboard');
    }

    return view('private.admin.pagos');
})->name('admin-pagos');

Route::get('/comprobante/{filename}', function ($filename) {
    $path = storage_path('app/private/comprobantes/'.$filename);

    if (auth()->user()->role != 'admin') {
        abort(403, 'Acceso no autorizado');
    }

    if (! file_exists($path)) {
        abort(404, 'Comprobante no encontrado');
    }

    if (! request()->hasValidSignature()) {
        abort(403, 'Acceso no autorizado');
    }

    return Response::file($path);
})->name('comprobante.show');

Route::get('/admin/accesos', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->role != 'admin') {
        return redirect()->route('dashboard');
    }

    return view('private.admin.accesos');
})->name('admin-accesos');

Route::get('/admin/documentos', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->role != 'admin') {
        return redirect()->route('dashboard');
    }

    return view('private.admin.documentos');
})->name('admin-documentos');

Route::get('/admin/reportes', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    if (auth()->user()->role != 'admin') {
        return redirect()->route('dashboard');
    }

    return view('private.admin.reportes');
})->name('admin-reportes');

Route::get('/public/catalogo', function () {
    if (auth()->check()) {
        if (auth()->user()->role == 'admin') {
            return redirect()->route('admin-documentos');
        }

        return redirect()->route('catalogo');
    }

    return view('public.catalog');
})->name('public-catalogo');

Route::get('/documentos/ver/{document}', [DocumentViewer::class, 'show'])
    ->middleware(['auth'])
    ->name('documentos.ver');

Route::get('/documentos/stream/{document}', [DocumentStream::class, 'stream'])
    ->middleware(['auth'])
    ->name('documentos.stream');
