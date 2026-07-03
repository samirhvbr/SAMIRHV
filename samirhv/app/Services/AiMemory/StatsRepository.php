<?php

namespace App\Services\AiMemory;

use Illuminate\Support\Carbon;
use Throwable;

/** Contagens e séries temporais para o Dashboard do módulo AI-MEMORY. */
class StatsRepository
{
    public function __construct(private readonly AiMemoryDatabase $db) {}

    /**
     * Totais atuais (ao vivo, do próprio ai-memory). Cada COUNT é tolerante:
     * se uma tabela não existir numa versão mais antiga do ai-memory, vira 0
     * em vez de derrubar o Dashboard.
     */
    public function counts(): array
    {
        return [
            'workspaces' => $this->count('SELECT COUNT(*) FROM workspaces'),
            'projects' => $this->count('SELECT COUNT(*) FROM projects'),
            'pages' => $this->count('SELECT COUNT(*) FROM pages WHERE is_latest = 1'),
            'sessions' => $this->count('SELECT COUNT(*) FROM sessions'),
            'observations' => $this->count('SELECT COUNT(*) FROM observations'),
            'embeddings' => $this->count('SELECT COUNT(*) FROM page_embeddings'),
            'handoffs_open' => $this->count("SELECT COUNT(*) FROM handoffs WHERE state = 'open'"),
            'proposals_pending' => $this->count("SELECT COUNT(*) FROM auto_improve_proposals WHERE status = 'pending'"),
        ];
    }

    /** [Y-m-d => total] contínuo dos últimos $days dias (observações criadas). */
    public function observationsByDay(int $days): array
    {
        return $this->byDay('observations', 'created_at', $days);
    }

    /** [Y-m-d => total] contínuo dos últimos $days dias (sessões iniciadas). */
    public function sessionsByDay(int $days): array
    {
        return $this->byDay('sessions', 'started_at', $days);
    }

    private function count(string $sql): int
    {
        try {
            return (int) $this->db->scalar($sql);
        } catch (Throwable) {
            return 0;
        }
    }

    /**
     * Agrupa por dia (bucket em UTC, que é como o ai-memory grava) e preenche
     * os dias sem dados com 0, para o gráfico não ter buracos.
     */
    private function byDay(string $table, string $column, int $days): array
    {
        $start = Carbon::now('UTC')->subDays($days - 1)->startOfDay();
        $sinceMicros = $start->timestamp * 1_000_000;

        $found = [];
        try {
            foreach ($this->db->select(
                "SELECT date({$column} / 1000000, 'unixepoch') AS d, COUNT(*) AS total
                   FROM {$table}
                  WHERE {$column} >= ?
                  GROUP BY d",
                [$sinceMicros]
            ) as $row) {
                $found[$row->d] = (int) $row->total;
            }
        } catch (Throwable) {
            $found = [];
        }

        $series = [];
        $cursor = $start->copy();
        for ($i = 0; $i < $days; $i++) {
            $key = $cursor->format('Y-m-d');
            $series[$key] = $found[$key] ?? 0;
            $cursor->addDay();
        }

        return $series;
    }
}
