<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * GitHub View — `workflow_name` e `branch` viram TEXT. O nome de um run do GitHub
 * Actions (com `run-name:` dinâmico) pode passar de 255 chars e estourar o
 * VARCHAR original no MySQL ("Data too long for column 'workflow_name'"). Mesma
 * classe do ajuste de datetime: o SQLite do original era leniente, o MySQL não.
 * Migration separada (a de criação já rodou em produção — isto preserva os dados).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('github_workflow_runs', function (Blueprint $table) {
            $table->text('workflow_name')->nullable()->change();
            $table->text('branch')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('github_workflow_runs', function (Blueprint $table) {
            $table->string('workflow_name')->nullable()->change();
            $table->string('branch')->nullable()->change();
        });
    }
};
