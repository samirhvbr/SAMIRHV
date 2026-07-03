<?php

namespace App\Services\AiMemory;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Acesso de BAIXO NÍVEL, SOMENTE LEITURA, ao SQLite do ai-memory.
 *
 * ▸ POR QUE ISSO EXISTE E POR QUE PODE "PARAR DE FUNCIONAR"
 *   O ai-memory (github.com/akitaonrails/ai-memory) roda como container Docker
 *   NO MESMO servidor de produção do Samirhv e mantém seu índice num arquivo
 *   SQLite em modo WAL, dentro do volume `ai-memory-data`
 *   (host: /var/lib/docker/volumes/ai-memory-data/_data/db/memory.sqlite).
 *   Esta classe abre esse arquivo como um SEGUNDO leitor. O módulo é, portanto,
 *   ACOPLADO AO HOST: se o app Samirhv migrar de máquina, se o volume/container
 *   mudar, ou se o usuário do PHP-FPM (www-data) perder permissão de leitura
 *   sobre `memory.sqlite` + `-wal` + `-shm`, as consultas param. Nesse caso
 *   `isAvailable()` retorna false e a UI mostra o aviso explicativo — nunca um
 *   erro 500. Ver docs/AI-MEMORY.md e config/aimemory.php.
 *
 * ▸ SOMENTE LEITURA (invariante de segurança)
 *   O ai-memory é o ÚNICO writer legítimo do arquivo: ele serializa escritas
 *   por um writer-actor e mantém triggers de FTS5 (pages_fts) e de invariante
 *   workspace×projeto. Gravar por fora corromperia tudo isso. Aplicamos
 *   `PRAGMA query_only = 1` na conexão e só expomos SELECT.
 *
 * ▸ TIMESTAMPS
 *   O ai-memory grava tempo como INTEGER = MICROSSEGUNDOS desde a epoch (UTC).
 *   Use `ts()` para converter em Carbon já no fuso de exibição.
 */
class AiMemoryDatabase
{
    /** Memo por requisição do resultado de isAvailable(). */
    private ?bool $available = null;

    /** Garante que o PRAGMA query_only é aplicado uma única vez. */
    private bool $guarded = false;

    /** Caminho do memory.sqlite no host (para a mensagem de degradação). */
    public function path(): string
    {
        return (string) config('aimemory.path');
    }

    /** Volume Docker de origem (texto da UI). */
    public function dockerVolume(): string
    {
        return (string) config('aimemory.docker_volume', 'ai-memory-data');
    }

    /** Fuso de exibição dos timestamps. */
    public function timezone(): string
    {
        return (string) config('aimemory.timezone', 'America/Sao_Paulo');
    }

    /**
     * O banco do ai-memory está acessível e legível AGORA?
     *
     * Nunca lança: qualquer falha (arquivo ausente, extensão pdo_sqlite
     * indisponível, permissão negada, lock) vira `false`. O resultado é
     * memoizado na instância (registrada como singleton) para não repetir
     * o stat/SELECT a cada repositório numa mesma página.
     */
    public function isAvailable(): bool
    {
        if ($this->available !== null) {
            return $this->available;
        }

        $path = $this->path();

        // Short-circuit barato: sem exceção ruidosa quando o arquivo nem existe
        // ou o www-data não consegue lê-lo (app fora do servidor, volume movido…).
        if ($path === '' || ! is_file($path) || ! is_readable($path)) {
            return $this->available = false;
        }

        try {
            $this->connection()->select('SELECT 1');

            return $this->available = true;
        } catch (Throwable) {
            return $this->available = false;
        }
    }

    /**
     * SELECT read-only. Retorna array de stdClass (padrão do query builder).
     * Só chame quando isAvailable() — os repositórios já degradam antes.
     */
    public function select(string $sql, array $bindings = []): array
    {
        return $this->connection()->select($sql, $bindings);
    }

    /** Primeira linha de um SELECT, ou null. */
    public function selectOne(string $sql, array $bindings = []): ?object
    {
        return $this->connection()->select($sql, $bindings)[0] ?? null;
    }

    /** Valor escalar da primeira coluna da primeira linha (ex.: COUNT(*)). */
    public function scalar(string $sql, array $bindings = []): mixed
    {
        $row = $this->selectOne($sql, $bindings);
        if ($row === null) {
            return null;
        }

        return array_values((array) $row)[0] ?? null;
    }

    /**
     * Pagina um SELECT raw reaproveitando o LengthAwarePaginator — assim as
     * views usam `{{ $x->links() }}` e `->withQueryString()` igual ao resto do
     * admin. `$sql` NÃO deve conter LIMIT/OFFSET (adicionamos aqui).
     */
    public function paginate(string $sql, array $bindings, string $countSql, array $countBindings, int $perPage): LengthAwarePaginator
    {
        $total = (int) $this->scalar($countSql, $countBindings);
        $page = max(1, (int) LengthAwarePaginator::resolveCurrentPage());
        $offset = ($page - 1) * $perPage;

        $items = $total > 0
            ? $this->select($sql.' LIMIT ? OFFSET ?', [...$bindings, $perPage, $offset])
            : [];

        return new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
    }

    /**
     * Conexão read-only já "trancada" com PRAGMA query_only. Privado de
     * propósito: ninguém deve pegar a conexão crua e escrever.
     */
    private function connection(): ConnectionInterface
    {
        $connection = DB::connection((string) config('aimemory.connection', 'aimemory'));

        if (! $this->guarded) {
            // Trava a nível de engine: qualquer INSERT/UPDATE/DELETE/DDL nesta
            // conexão passa a falhar. Cinto e suspensório sobre o "só SELECT".
            $connection->statement('PRAGMA query_only = 1');
            $this->guarded = true;
        }

        return $connection;
    }
}
