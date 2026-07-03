<?php

namespace App\Console\Commands;

use App\Models\AiMemoryStatSnapshot;
use App\Services\AiMemory\AiMemoryDatabase;
use App\Services\AiMemory\StatsRepository;
use Illuminate\Console\Command;

/**
 * Grava o retrato diário das estatísticas do ai-memory na tabela durável
 * `ai_memory_stat_snapshots` (MySQL). Idempotente por dia (updateOrCreate em
 * captured_on). Agendado em routes/console.php (diário).
 *
 * Se o ai-memory estiver indisponível (app fora do servidor, volume/permissão),
 * NÃO grava nada e sai com sucesso — o histórico já existente é preservado.
 */
class SnapshotAiMemoryStats extends Command
{
    protected $signature = 'aimemory:snapshot';

    protected $description = 'Grava um retrato diário das estatísticas do ai-memory (histórico durável em MySQL)';

    public function handle(AiMemoryDatabase $db, StatsRepository $stats): int
    {
        if (! $db->isAvailable()) {
            $this->warn("ai-memory indisponível em [{$db->path()}] — retrato pulado, histórico preservado.");

            return self::SUCCESS;
        }

        $counts = $stats->counts();

        $snapshot = AiMemoryStatSnapshot::updateOrCreate(
            ['captured_on' => today()],
            [...$counts, 'raw_json' => $counts],
        );

        $this->info('Retrato de '.$snapshot->captured_on->format('d/m/Y').': '
            .$counts['pages'].' páginas, '
            .$counts['sessions'].' sessões, '
            .$counts['observations'].' observações.');

        return self::SUCCESS;
    }
}
