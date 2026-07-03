# Módulo AI-MEMORY (admin) — leitura da memória do ai-memory

> **TL;DR** — A aba **AI-MEMORY** do admin lê, **somente leitura**, o banco
> **SQLite do [ai-memory](https://github.com/akitaonrails/ai-memory)** — a memória
> de longo prazo dos agentes de código. Esse banco vive num **volume Docker no
> mesmo servidor de produção**. Por isso o módulo é **acoplado ao host**: se o
> app sair desse servidor (ou o volume/permissão mudar), a tela para de retornar
> dados e passa a mostrar um aviso. **Não é bug — é o acoplamento.** As
> estatísticas de uso são copiadas diariamente para uma tabela MySQL própria
> (`ai_memory_stat_snapshots`) para **sobreviver a um reset do ai-memory**.

---

## 1. O que é e por que existe

O `ai-memory` é um servidor (binário Rust, distribuído como imagem Docker
`akitaonrails/ai-memory`) que dá memória de longo prazo aos agentes de código
(Claude Code, Codex, etc.). Ele:

- guarda a **wiki em Markdown** como fonte da verdade (`<data>/wiki/`), e
- mantém um **índice derivado em SQLite** (`<data>/db/memory.sqlite`, modo **WAL**)
  com sessões, observações, páginas, handoffs, embeddings, auditoria e um índice
  **FTS5** para busca.

O admin do Samirhv abre esse `memory.sqlite` diretamente e mostra Dashboard,
Projetos, Páginas (wiki), Sessões, Observações, Handoffs e Busca.

## 2. O acoplamento de produção (LEIA ISTO)

```
┌────────────────────────── servidor de produção ──────────────────────────┐
│                                                                            │
│   PHP-FPM (Samirhv/Laravel)  ──lê (RO)──►  /var/lib/docker/volumes/        │
│        [www-data]                          ai-memory-data/_data/db/        │
│                                            memory.sqlite (+ -wal, -shm)     │
│                                                    ▲                        │
│   container ai-memory  ──escreve (writer)──────────┘                        │
│        [Docker]                                                            │
└────────────────────────────────────────────────────────────────────────────┘
```

- O caminho no host normalmente é
  `/var/lib/docker/volumes/ai-memory-data/_data/db/memory.sqlite`.
  Confirme com:
  ```bash
  docker volume inspect ai-memory-data -f '{{ .Mountpoint }}'   # + /db/memory.sqlite
  ```
- Configure em `.env` → `AI_MEMORY_SQLITE_PATH`. Sem isso, usa o default acima.
- **Se o app for movido para outro servidor, esse arquivo não existe lá.** A tela
  vai mostrar o aviso "AI-MEMORY indisponível" — e o texto do aviso explica
  exatamente o porquê. É o comportamento esperado.

## 3. Somente leitura — e por que NÃO gravamos aqui

O `ai-memory` é o **único writer legítimo** do arquivo: ele serializa escritas
por um *writer-actor* e mantém **triggers de FTS5** (`pages_fts`) e **triggers de
invariante** (workspace×projeto). Gravar por fora corromperia o índice.

Garantias no código:

- A conexão `aimemory` (em `config/database.php`) é usada só para `SELECT`.
- `App\Services\AiMemory\AiMemoryDatabase` aplica **`PRAGMA query_only = 1`** —
  qualquer `INSERT/UPDATE/DELETE/DDL` nessa conexão **falha**.
- `isAvailable()` nunca lança: arquivo ausente, `pdo_sqlite` faltando, permissão
  negada ou lock viram `false`, e a UI degrada.

> Ações de **escrita** do ai-memory (aprovar/rejeitar Auto Improve, gerar
> embeddings) são **Fase 2** e devem passar pela **API/MCP do ai-memory**, nunca
> por este SQLite. Ver §7.

## 4. Permissões (a causa nº 1 de "parou de funcionar")

O arquivo pertence ao volume Docker; por padrão o `www-data` **não** tem acesso.
Opções (a que você preferir), no servidor:

- **Grupo de leitura** (recomendado): dê leitura de grupo ao diretório do volume
  e coloque o `www-data` nesse grupo. Precisa de `r` no arquivo e `x` (traverse)
  nos diretórios até ele, incluindo `-wal` e `-shm`.
- **Bind mount somente-leitura**: monte o `/data` do ai-memory num diretório do
  host legível pelo `www-data` e aponte `AI_MEMORY_SQLITE_PATH` pra lá.

Teste rápido do ponto de vista do PHP:
```bash
sudo -u www-data test -r "$AI_MEMORY_SQLITE_PATH" && echo OK || echo "sem permissão"
sudo -u www-data php artisan aimemory:snapshot   # deve gravar um retrato
```

> **Nota WAL:** um leitor precisa enxergar `memory.sqlite`, `-wal` e `-shm`.
> Como o ai-memory mantém a conexão viva, o `-shm` existe; garanta leitura nos três.

## 5. Histórico durável (`ai_memory_stat_snapshots`)

O `memory.sqlite` é um índice **derivado** — pode ser recriado/zerado. Para que a
**evolução de uso não se perca**, um comando agendado grava um retrato diário no
**banco do próprio app (MySQL)**:

- Comando: `php artisan aimemory:snapshot` (idempotente por dia — `updateOrCreate`
  em `captured_on`). Se o ai-memory estiver indisponível, **não grava e preserva**
  o histórico existente.
- Agendamento: `routes/console.php` → `Schedule::command('aimemory:snapshot')->dailyAt('03:10')`.
  Requer o cron do Laravel no servidor: `* * * * * php artisan schedule:run`.
- O Dashboard mostra os totais **ao vivo** (do ai-memory) e a **evolução
  histórica** (desta tabela, que sobrevive a reset).

## 6. Mapa de código

| Camada | Arquivo |
| --- | --- |
| Conexão RO + `isAvailable` + paginação | `app/Services/AiMemory/AiMemoryDatabase.php` |
| Formatação de tempo (µs → local) | `app/Services/AiMemory/AiMemoryTime.php` |
| Consultas (1 por tela) | `app/Services/AiMemory/{Stats,Project,Page,Session,Observation,Handoff,Search}Repository.php` |
| Controller fino | `app/Http/Controllers/Admin/AiMemoryController.php` |
| Rotas | `routes/admin.php` (grupo `admin.ai-memory.*`) |
| Views | `resources/views/admin/ai-memory/*.blade.php` |
| Aviso de degradação | `resources/views/admin/ai-memory/_unavailable.blade.php` |
| Config | `config/aimemory.php`, conexão `aimemory` em `config/database.php` |
| Histórico | migration `..._create_ai_memory_stat_snapshots_table`, `App\Models\AiMemoryStatSnapshot`, `app/Console/Commands/SnapshotAiMemoryStats.php` |

### Schema do ai-memory (referência)
As consultas seguem as migrações do ai-memory
(`crates/ai-memory-store/migrations/V01..V25`). Pontos que valem lembrar:
- **timestamps = microssegundos** desde epoch (UTC) → dividir por 1.000.000;
- **ids = BLOB** (UUIDv7) → nas URLs usamos `lower(hex(id))` (32 chars);
- `pages`: versão atual `is_latest=1`; histórico via `supersedes`;
- busca via `pages_fts` (FTS5, colunas `title`+`body`), ordenada por `bm25`.

## 7. Fase 2 (fora do escopo atual)

- **Auto Improve** (aprovar/rejeitar propostas) e **gerar embeddings** —
  **escrita**, portanto via **API/MCP do ai-memory**, nunca neste SQLite.
- **Knowledge Graph** visual a partir da tabela `links`.
- Telas dedicadas de Workspaces / Embeddings / Auditoria do ai-memory.
