<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'page_view')) {
                return;
            }

            // Página curada (Blade) que substitui a página genérica de download em /p/{slug}.
            // Uso: projeto de documentação (sem binários hospedados aqui) que precisa de uma
            // página rica — screenshots, instalação por SO, etc. Ex: 'projects.ai-usagebar'.
            // Vazio (null) = comportamento padrão (página de download por SO).
            $table->string('page_view', 120)->nullable()->after('icon');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'page_view')) {
                $table->dropColumn('page_view');
            }
        });
    }
};
