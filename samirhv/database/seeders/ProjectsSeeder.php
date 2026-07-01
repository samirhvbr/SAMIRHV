<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Cria os projetos iniciais que antes viviam "chumbados" no menu/HTML:
 * ShvIA (híbrido: site + app desktop), SShvTerm (projeto-link) e GitHub Desktop (download).
 * Idempotente: usa firstOrCreate por slug, então pode rodar de novo sem duplicar.
 */
class ProjectsSeeder extends Seeder
{
    public function run(): void
    {
        // Híbrido: link pra plataforma web + downloads do app desktop (Tauri).
        // Os binários (.dmg/.msi/.AppImage/.deb) são enviados pelo admin, aba Arquivos.
        Project::firstOrCreate(
            ['slug' => 'shvia'],
            [
                'title' => 'ShvIA',
                'description' => "Assistente de IA interno da Blue3 para apoio operacional e consulta de conhecimento corporativo.\n\nUse online direto no navegador (sempre na última versão) ou baixe o app desktop para Windows, macOS e Linux.",
                'category' => 'Assistente IA',
                'icon' => 'fa-solid fa-robot',
                'external_url' => 'https://ia.blue3.com.br',
                'redirect_to_site' => false, // híbrido: abre /p/shvia com botão "usar online" + downloads
                'is_published' => true,
                'sort_order' => 1,
            ]
        );

        Project::firstOrCreate(
            ['slug' => 'sshvterm'],
            [
                'title' => 'SShvTerm',
                'description' => 'Terminal SSH/web com a cara do samirhv. Acesse pelo site oficial.',
                'category' => 'Terminal Web',
                'icon' => 'fa-solid fa-terminal',
                'external_url' => 'https://sshvterm.com',
                'redirect_to_site' => true, // link puro: abre o site direto
                'is_published' => true,
                'sort_order' => 1,
            ]
        );

        Project::firstOrCreate(
            ['slug' => 'github-desktop'],
            [
                'title' => 'GitHub Desktop',
                'description' => "GitHub Desktop é o cliente Git visual e open-source da GitHub — construído em Electron e escrito em TypeScript com React. Commits, branches, histórico, pull requests e resolução de conflitos numa interface limpa, sem decorar comandos.\n\nOficialmente a GitHub não distribui o app para Linux. Este é um build da comunidade que compila o GitHub Desktop a partir do código-fonte e o empacota como .deb, pronto pra instalar em Debian, Ubuntu e derivados.",
                'category' => 'Aplicativo Desktop',
                'icon' => 'fa-brands fa-github',
                'external_url' => null,
                'is_published' => true,
                'sort_order' => 2,
            ]
        );

        $this->command?->info('Projetos garantidos: ShvIA (híbrido: site + app desktop) + SShvTerm (link) + GitHub Desktop (download).');
    }
}
