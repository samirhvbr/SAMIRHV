<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Histórico DURÁVEL das estatísticas do ai-memory, no banco do PRÓPRIO app
 * (MySQL/MariaDB). O ai-memory pode ser zerado/recriado (é um índice derivado);
 * esta tabela guarda um retrato por dia para que a evolução de uso continue
 * existindo mesmo assim. Alimentada por `php artisan aimemory:snapshot`
 * (agendado diariamente). Ver docs/AI-MEMORY.md.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_memory_stat_snapshots', function (Blueprint $table) {
            $table->id();
            $table->date('captured_on')->unique();   // um retrato por dia (idempotente)

            // Totais no momento do retrato.
            $table->unsignedInteger('workspaces')->default(0);
            $table->unsignedInteger('projects')->default(0);
            $table->unsignedInteger('pages')->default(0);
            $table->unsignedInteger('sessions')->default(0);
            $table->unsignedInteger('observations')->default(0);
            $table->unsignedInteger('embeddings')->default(0);
            $table->unsignedInteger('handoffs_open')->default(0);
            $table->unsignedInteger('proposals_pending')->default(0);

            // Blob completo do retrato, para compat futura (novas métricas sem migration).
            $table->json('raw_json')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_memory_stat_snapshots');
    }
};
