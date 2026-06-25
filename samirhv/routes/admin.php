<?php

use App\Http\Controllers\Admin\AccessAuditController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectFileController;
use Illuminate\Support\Facades\Route;

// Já carregado dentro do grupo 'web' (ver bootstrap/app.php). Aqui só auth + admin.
Route::prefix('admin')->name('admin.')
    ->middleware(['auth', 'admin', 'password.changed'])
    ->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Projetos (CRUD)
        Route::get('/projetos', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projetos/novo', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projetos', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projetos/{project}/editar', [ProjectController::class, 'edit'])->name('projects.edit');
        Route::put('/projetos/{project}', [ProjectController::class, 'update'])->name('projects.update');
        Route::delete('/projetos/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

        // Arquivos de um projeto (upload/gerência)
        Route::get('/projetos/{project}/arquivos', [ProjectFileController::class, 'index'])->name('projects.files.index');
        Route::post('/projetos/{project}/arquivos', [ProjectFileController::class, 'store'])->name('projects.files.store');
        Route::patch('/projetos/{project}/arquivos/{file}/disponivel', [ProjectFileController::class, 'toggleAvailable'])->name('projects.files.available');
        Route::delete('/projetos/{project}/arquivos/{file}', [ProjectFileController::class, 'destroy'])->name('projects.files.destroy');

        // Auditorias
        Route::get('/auditoria', [AuditController::class, 'index'])->name('audit.index');
        Route::get('/auditoria-acesso', [AccessAuditController::class, 'index'])->name('access-audit.index');

        // Perfil / troca de senha
        Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile');
        Route::post('/perfil/senha', [ProfileController::class, 'updatePassword'])->name('profile.password');
    });
