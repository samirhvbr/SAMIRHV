<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * GitHub View — runs de GitHub Actions por repositório (o "race to green").
 * Porte da tabela `workflow_runs` do github-visualize. Ver §4.1: `github_id` é
 * `unsignedBigInteger` (IDs de run são grandes; `integer` estoura no MySQL).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('github_workflow_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('repository_id')->constrained('github_repositories')->cascadeOnDelete();
            $table->unsignedBigInteger('github_id');        // ID grande da run no GitHub
            $table->string('workflow_name')->nullable();
            $table->integer('run_number')->nullable();
            $table->string('status')->nullable();
            $table->string('conclusion')->nullable();
            $table->string('branch')->nullable();
            $table->timestamp('run_started_at')->nullable();
            $table->timestamps();

            $table->unique(['repository_id', 'github_id']); // alvo do upsert
            $table->index(['repository_id', 'run_started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('github_workflow_runs');
    }
};
