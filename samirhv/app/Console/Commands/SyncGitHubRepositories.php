<?php

namespace App\Console\Commands;

use App\Jobs\GitHubView\SyncRepositoryJob;
use App\Models\GitHubView\Repository;
use Illuminate\Console\Command;

/**
 * Sincroniza (incremental) TODOS os repositórios do GitHub View com o GitHub.
 * Roda SÍNCRONO, um a um — não precisa de `queue:work`. Porte do
 * SyncAllRepositoriesJob do github-visualize. Agendado em routes/console.php
 * (requer `* * * * * php artisan schedule:run` no servidor, já ativo).
 * Ver .continue/migracao-github-visualize.md (§6).
 */
class SyncGitHubRepositories extends Command
{
    protected $signature = 'github-view:sync';

    protected $description = 'Sincroniza os repositórios monitorados do GitHub View (incremental) com o GitHub';

    public function handle(): int
    {
        if (blank(config('services.github.token'))) {
            $this->warn('GITHUB_TOKEN não configurado — sync pulado.');

            return self::SUCCESS; // não é falha do cron; apenas não há o que fazer
        }

        // Mais desatualizados primeiro (se o run for interrompido, os stalest têm prioridade).
        $repositories = Repository::query()->orderBy('last_synced_at')->get();

        if ($repositories->isEmpty()) {
            $this->info('Nenhum repositório monitorado.');

            return self::SUCCESS;
        }

        $ok = 0;
        $failed = 0;

        foreach ($repositories as $repository) {
            $this->line("→ {$repository->fullName()}");

            try {
                SyncRepositoryJob::dispatchSync($repository);
            } catch (\Throwable $e) {
                // Rede de segurança: MissingToken (DI) ou erro inesperado não derruba o run.
                $repository->failSync($e->getMessage());
            }

            $repository->refresh();
            if ($repository->sync_status === 'failed') {
                $failed++;
                $this->warn("  falhou: {$repository->sync_error}");
            } else {
                $ok++;
            }
        }

        $this->info("GitHub View sync: {$ok} ok · {$failed} com falha · {$repositories->count()} repos.");

        return $failed > 0 ? self::FAILURE : self::SUCCESS;
    }
}
