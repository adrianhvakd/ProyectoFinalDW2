<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\Catalogo;
use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('public.index');
})->name('home');

Route::get('/login', [Auth::class, 'login'])->name('login');
Route::get('/register', [Auth::class, 'register'])->name('register');
Route::post('/logout', [Auth::class, 'logout'])->name('logout');

Route::get('/dashboard', [Dashboard::class, 'index'])->name('dashboard');
Route::get('/catalogo', [Catalogo::class, 'index'])->name('catalogo');
Route::get('/documentos', function () {
    return view('private.user.mis-documentos');
})->name('mis-documentos');
Route::get('/historial', function () {
    return view('private.user.historial');
})->name('historial');
Route::get('/admin/dashboard', function () {
    return view('private.admin.dashboard');
})->name('admin-dashboard');
Route::get('/admin/usuarios', function () {
    return view('private.admin.usuarios');
})->name('admin-usuarios');
Route::get('/admin/pagos', function () {
    return view('private.admin.pagos');
})->name('admin-pagos');

Route::get('/comprobante/{filename}', function ($filename) {
    $path = storage_path('app/private/images/comprobantes/'.$filename);

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
    return view('private.admin.accesos');
})->name('admin-accesos');

Route::get('/admin/documentos', function () {
    return view('private.admin.documentos');
})->name('admin-documentos');
