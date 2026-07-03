<?php

namespace App\Services\AiMemory;

/**
 * Busca full-text nas páginas usando o MESMO índice FTS5 do ai-memory
 * (tabela `pages_fts`, colunas title/body). Ordena por bm25 (menor = melhor).
 */
class SearchRepository
{
    public function __construct(private readonly AiMemoryDatabase $db) {}

    /** Resultados com trecho destacado (sentinelas <<< >>> viram <mark> na view). */
    public function search(string $query, int $limit = 30): array
    {
        $match = $this->toMatch($query);
        if ($match === '') {
            return [];
        }

        return $this->db->select(
            "SELECT lower(hex(p.id)) AS id_hex, p.title, p.path, p.tier, pr.name AS project,
                    snippet(pages_fts, 1, '<<<', '>>>', '…', 14) AS snippet
               FROM pages_fts
               JOIN pages p ON p.rowid = pages_fts.rowid
               JOIN projects pr ON pr.id = p.project_id
              WHERE pages_fts MATCH ? AND p.is_latest = 1
              ORDER BY bm25(pages_fts)
              LIMIT ?",
            [$match, $limit]
        );
    }

    /**
     * Traduz a busca do usuário para uma expressão MATCH segura: cada token
     * vira uma frase entre aspas seguida de `*` (prefixo) — assim "oauth" acha
     * "oauth2" e "auth" acha "authentication". As aspas neutralizam a sintaxe
     * do FTS5 (operadores AND/OR/NOT/NEAR, aspas) que causaria erro de query;
     * múltiplos tokens combinam por AND implícito.
     */
    private function toMatch(string $query): string
    {
        preg_match_all('/[\p{L}\p{N}_\/.-]+/u', $query, $matches);
        $tokens = array_slice($matches[0] ?? [], 0, 10);
        $quoted = array_map(static fn (string $t) => '"'.str_replace('"', '""', $t).'"*', $tokens);

        return implode(' ', $quoted);
    }
}
