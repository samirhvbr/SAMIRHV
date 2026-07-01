<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Só relevante para projeto com external_url:
            //   true  = clicar abre o site direto (link puro, ex: SShvTerm).
            //   false = abre a página /p/{slug} com botão "usar online" + downloads (híbrido, ex: ShvIA).
            $table->boolean('redirect_to_site')->default(false)->after('external_url');
        });

        // Preserva o comportamento atual: todo projeto-link existente redirecionava direto.
        DB::table('projects')->whereNotNull('external_url')->update(['redirect_to_site' => true]);

        // ShvIA é híbrido (site + app desktop): mostra a página /p/shvia, não redireciona.
        DB::table('projects')->where('slug', 'shvia')->update(['redirect_to_site' => false]);
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('redirect_to_site');
        });
    }
};
