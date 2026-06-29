<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // URL externa do projeto. Preenchida = projeto-link (aponta pro site,
            // sem arquivos). Vazia = projeto de download (com arquivos no disco).
            $table->string('external_url', 2048)->nullable()->after('icon');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('external_url');
        });
    }
};
