<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\SiteController;
use Illuminate\Support\Facades\Route;

// ── Site público ──────────────────────────────────────────────
Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/downloads', [SiteController::class, 'downloads'])->name('downloads');
Route::get('/p/{project}', [SiteController::class, 'show'])->name('project.show');

// Download com contagem + auditoria (único caminho para baixar; disco privado).
Route::get('/d/{file}', [DownloadController::class, 'track'])->name('download.track');

// ── Autenticação do painel (sem cadastro) ─────────────────────
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])
    ->middleware('throttle:login')
    ->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
