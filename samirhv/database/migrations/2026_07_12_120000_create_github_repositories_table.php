<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * GitHub View — repositórios monitorados. Porte da tabela `repositories` do
 * github-visualize (Rails/SQLite) para o MySQL do samirhv. Os dados NÃO migram
 * do SQLite: as tabelas nascem aqui e são preenchidas por re-sync da API do
 * GitHub. Tipos ajustados p/ MySQL (text onde SQLite era string ilimitado).
 * Ver .continue/migracao-github-visualize.md (§4, §4.1).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('github_repositories', function (Blueprint $table) {
            $table->id();
            $table->string('owner');
            $table->string('name');
            $table->text('description')->nullable();            // text: pode passar de 255
            $table->string('default_branch')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->string('sync_status')->default('pending');  // pending|syncing|synced|failed
            $table->text('sync_error')->nullable();             // text: mensagem de erro pode ser longa
            $table->string('sync_progress')->nullable();
            $table->timestamps();

            $table->unique(['owner', 'name']);                  // MySQL utf8mb4_*_ci = case-insensitive (como o Rails)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('github_repositories');
    }
};
