<?php

namespace App\Console\Commands;

use App\Models\ProjectFile;
use App\Support\FilenameInspector;
use Illuminate\Console\Command;

/**
 * Classifica os arquivos já existentes por SO/arquitetura/tipo, inferindo do
 * nome (original_name), e preenche released_at com a data de criação quando
 * ausente. Idempotente: só toca nas linhas onde `os` ainda é null. Rode após
 * a migration de colunas de SO (e novamente, sem dano, quando quiser).
 */
class BackfillFileOs extends Command
{
    protected $signature = 'downloads:backfill-os';

    protected $description = 'Classifica os arquivos existentes por SO/arquitetura/tipo (inferido do nome) e preenche released_at';

    public function handle(): int
    {
        $pending = ProjectFile::withTrashed()->whereNull('os')->get();

        if ($pending->isEmpty()) {
            $this->info('Nada a fazer: todos os arquivos já têm SO definido.');

            return self::SUCCESS;
        }

        $touched = 0;
        foreach ($pending as $file) {
            $info = FilenameInspector::inspect($file->original_name ?: $file->filename);

            $file->os = $info['os'];
            $file->arch = $info['arch'];
            $file->file_type = $info['file_type'];
            if ($file->released_at === null) {
                $file->released_at = $file->created_at;
            }
            $file->save();

            $touched++;
            $this->line(sprintf(
                '  %s → os=%s arch=%s tipo=%s',
                $file->original_name,
                $info['os'] ?? '—',
                $info['arch'] ?? '—',
                $info['file_type'] ?? '—',
            ));
        }

        $this->info("OK: {$touched} arquivo(s) classificado(s).");

        return self::SUCCESS;
    }
}
