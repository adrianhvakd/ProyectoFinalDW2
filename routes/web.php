<?php

use App\Http\Controllers\Auth;
use App\Http\Controllers\Catalogo;
use App\Http\Controllers\Dashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('public.index');
})->name('home');

Route::get('/login', [Auth::class, 'login'])->name('login');
Route::get('/register', [Auth::class, 'register'])->name('register');
Route::post('/logout', [Auth::class, 'logout'])->name('logout');

Route::get('/dashboard', [Dashboard::class, 'index'])->name('dashboard');
Route::get('/catalogo', [Catalogo::class, 'index'])->name('catalogo');
