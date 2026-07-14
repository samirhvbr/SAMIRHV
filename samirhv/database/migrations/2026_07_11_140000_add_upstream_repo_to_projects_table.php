<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Repositório OSS original de que este projeto é fork, no formato
            // "owner/repo" (ex: "akitaonrails/ai-usagebar"). O monitor compara a
            // última release deste repo com a nossa versão local. Null = projeto
            // sem upstream (não entra na comparação).
            $table->string('upstream_repo', 140)->nullable()->after('external_url');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('upstream_repo');
        });
    }
};
