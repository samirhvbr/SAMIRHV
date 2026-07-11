<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Projetos curados da vitrine, na ordem oficial:
 *   1. ShvIA (híbrido: site + app desktop)
 *   2. GitHub Desktop (download)
 *   3. ai-usagebar (documentação: página curada de instalação)
 *   4. SShvTerm (projeto-link: mora no site oficial)
 *
 * updateOrCreate por slug: idempotente E autoritativo — rodar de novo
 * sincroniza título/descrição/ordem/flags com o que está aqui no código.
 * (Os arquivos de download continuam sendo gerenciados pelo admin, aba Arquivos.)
 */
class ProjectsSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Híbrido: link pra plataforma web + downloads do app desktop (Tauri).
        Project::updateOrCreate(
            ['slug' => 'shvia'],
            [
                'title' => 'ShvIA',
                'description' => "Assistente de IA interno da Blue3 para apoio operacional e consulta de conhecimento corporativo. Chat com múltiplos modelos, ditado por voz e leitura em voz alta, e um modo Code para tarefas de desenvolvimento.\n\nUse online direto no navegador (sempre na última versão) ou baixe o app desktop para Windows, macOS e Linux.",
                'category' => 'Assistente IA',
                'icon' => 'fa-solid fa-robot',
                'page_view' => null,
                'external_url' => 'https://ia.blue3.com.br',
                'redirect_to_site' => false, // híbrido: abre /p/shvia com botão "usar online" + downloads
                'is_published' => true,
                'sort_order' => 1,
            ]
        );

        // 2) Download: build da comunidade do GitHub Desktop (a GitHub não publica p/ Linux).
        Project::updateOrCreate(
            ['slug' => 'github-desktop'],
            [
                'title' => 'GitHub Desktop',
                'description' => "GitHub Desktop é o cliente Git visual e open-source da GitHub — Electron, TypeScript e React. Commits, branches, histórico, pull requests e resolução de conflitos numa interface limpa, sem decorar comandos.\n\nA GitHub não distribui o app para Linux. Este é um build da comunidade que compila do código-fonte e empacota como .deb para Debian, Ubuntu e derivados — e também gera instaladores .exe/.msi para Windows.",
                'category' => 'Aplicativo Desktop',
                'icon' => 'fa-brands fa-github',
                'page_view' => null,
                'external_url' => null,
                'redirect_to_site' => false,
                'is_published' => true,
                'sort_order' => 2,
            ]
        );

        // 3) Documentação: página curada 'projects.ai-usagebar' (instalação por SO). Sem binários
        //    hospedados aqui — instala via AUR/crates.io/build. Autoria de Fabio Akita.
        Project::updateOrCreate(
            ['slug' => 'ai-usagebar'],
            [
                'title' => 'ai-usagebar',
                'description' => "Monitor de uso dos seus planos de IA — Anthropic Claude, OpenAI Codex, Z.AI, OpenRouter e DeepSeek — direto na barra do sistema (Waybar/GNOME no Linux, menu bar no macOS, bandeja no Windows) e num TUI de terminal.\n\nProjeto de Fabio Akita (akitaonrails/ai-usagebar), escrito em Rust. As integrações nativas de desktop mostradas aqui (GNOME, macOS e Windows) são contribuições deste fork. Veja como instalar em cada sistema.",
                'category' => 'Monitor de uso de IA',
                'icon' => 'fa-solid fa-gauge-high',
                'page_view' => 'projects.ai-usagebar',
                'external_url' => null,
                'redirect_to_site' => false,
                'is_published' => true,
                'sort_order' => 3,
            ]
        );

        // 4) Projeto-link puro: mora no site oficial (redirect ligado). Fica por último.
        Project::updateOrCreate(
            ['slug' => 'sshvterm'],
            [
                'title' => 'SShvTerm',
                'description' => 'Cliente SSH/SFTP desktop e multiplataforma, com sync zero-knowledge. Tem um agente de IA que opera o terminal — propõe e executa comandos no PTY visível, sob uma política allow · ask · deny que você controla (Anthropic, OpenAI, xAI/Grok e mais). Baixe pelo site oficial.',
                'category' => 'Cliente SSH',
                'icon' => 'fa-solid fa-terminal',
                'page_view' => null,
                'external_url' => 'https://sshvterm.com',
                'redirect_to_site' => true, // link puro: abre o site direto
                'is_published' => true,
                'sort_order' => 4,
            ]
        );

        $this->command?->info('Projetos sincronizados: ShvIA (1) · GitHub Desktop (2) · ai-usagebar (3) · SShvTerm (4).');
    }
}
