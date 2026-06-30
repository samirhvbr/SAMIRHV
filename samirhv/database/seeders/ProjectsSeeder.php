<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Cria os projetos iniciais que antes viviam "chumbados" no menu/HTML:
 * ShvIA e SShvTerm (projetos-link → sites próprios) e GitHub Desktop (download).
 * Idempotente: usa firstOrCreate por slug, então pode rodar de novo sem duplicar.
 */
class ProjectsSeeder extends Seeder
{
    public function run(): void
    {
        Project::firstOrCreate(
            ['slug' => 'shvia'],
            [
                'title' => 'ShvIA',
                'description' => 'Assistente de IA interno da Blue3 para apoio operacional e consulta de conhecimento corporativo. Acesse pela plataforma oficial.',
                'category' => 'Assistente IA',
                'icon' => 'fa-solid fa-robot',
                'external_url' => 'https://ia.blue3.com.br',
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

        $this->command?->info('Projetos garantidos: ShvIA (link) + SShvTerm (link) + GitHub Desktop (download).');
    }
}
