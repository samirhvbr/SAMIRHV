<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * GitHub View — commits por repositório (additions/deletions vêm da GraphQL em
 * lote). Porte da tabela `commits` do github-visualize. Ver §4.1: `message` é
 * `text` (headline pode passar de 255 no MySQL).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('github_commits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repository_id')->constrained('github_repositories')->cascadeOnDelete();
            $table->string('sha');
            $table->text('message')->nullable();        // text: headline pode passar de 255
            $table->string('author_login')->nullable();
            $table->timestamp('committed_at');
            $table->integer('additions')->default(0);
            $table->integer('deletions')->default(0);
            $table->timestamps();

            $table->unique(['repository_id', 'sha']);   // alvo do upsert incremental
            $table->index(['repository_id', 'committed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('github_commits');
    }
};
