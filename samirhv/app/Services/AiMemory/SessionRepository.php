<?php

namespace App\Services\AiMemory;

use Illuminate\Pagination\LengthAwarePaginator;

/** Sessões (um agente trabalhando) e a timeline de observações de cada uma. */
class SessionRepository
{
    public function __construct(private readonly AiMemoryDatabase $db) {}

    /** Sessões mais recentes primeiro, opcionalmente de um projeto. */
    public function paginate(?string $projectHex, int $perPage): LengthAwarePaginator
    {
        $where = '1 = 1';
        $bind = [];
        if ($projectHex) {
            $where .= ' AND lower(hex(s.project_id)) = ?';
            $bind[] = strtolower($projectHex);
        }

        $sql = "SELECT lower(hex(s.id)) AS id_hex, s.agent_kind, s.cwd, s.started_at, s.ended_at,
                       pr.name AS project,
                       (SELECT COUNT(*) FROM observations o WHERE o.session_id = s.id) AS obs_count
                  FROM sessions s
                  JOIN projects pr ON pr.id = s.project_id
                 WHERE {$where}
                 ORDER BY s.started_at DESC";

        return $this->db->paginate($sql, $bind, "SELECT COUNT(*) FROM sessions s WHERE {$where}", $bind, $perPage);
    }

    /** Uma sessão por id hex, com projeto e página-resumo (se houver). */
    public function find(string $hexId): ?object
    {
        return $this->db->selectOne(
            "SELECT lower(hex(s.id)) AS id_hex, s.agent_kind, s.cwd, s.started_at, s.ended_at,
                    pr.name AS project, w.name AS workspace,
                    lower(hex(s.project_id)) AS project_hex,
                    lower(hex(s.summary_page_id)) AS summary_page_hex,
                    sp.title AS summary_title,
                    (SELECT COUNT(*) FROM observations o WHERE o.session_id = s.id) AS obs_count
               FROM sessions s
               JOIN projects pr ON pr.id = s.project_id
               JOIN workspaces w ON w.id = s.workspace_id
               LEFT JOIN pages sp ON sp.id = s.summary_page_id
              WHERE lower(hex(s.id)) = ?",
            [strtolower($hexId)]
        );
    }

    /** Observações da sessão em ordem cronológica (timeline). */
    public function observations(string $sessionHex): array
    {
        return $this->db->select(
            "SELECT lower(hex(id)) AS id_hex, kind, title, importance, created_at
               FROM observations
              WHERE lower(hex(session_id)) = ?
              ORDER BY created_at ASC",
            [strtolower($sessionHex)]
        );
    }
}
