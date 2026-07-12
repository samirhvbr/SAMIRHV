# GitHub View — Migração Rails → Laravel (dentro do SAMIRHV)

> **Status:** planejamento (só o plano; nada de código ainda).
> **Objetivo:** portar o `github-visualize` (Rails 8.1, do Fabio Akita) para uma
> **feature nativa no `/admin`** do SAMIRHV — item de menu **"GitHub View"** —,
> reaproveitando o máximo do original. **Não** rodar Rails ao lado.
> **Origem:** fork em `github.com/samirhvbr/github-visualize` (upstream
> `akitaonrails/github-visualize`). **Última atualização:** 12/07/2026.

---

## 1. Contexto e decisão

- O app original é um **dashboard self-hosted** que monitora repositórios do
  GitHub e "replaya" o progresso deles com visualizações animadas (timeline de
  commits, heatmap por hora, "race to green" de CI, cards do dashboard).
- **Decisão (já tomada):** portar para **Laravel dentro do SAMIRHV**, não manter
  Rails como serviço à parte. Motivos: herda a **auth do admin** (`auth,admin,
  password.changed`), o **MySQL**, o **deploy** e o layout do admin; zero runtime
  Ruby no servidor; o port é **tradução 1:1** (Rails↔Laravel são primos) e o
  "uau" (charts em canvas) é **JS agnóstico**, transfere quase verbatim.
- ⚠️ **Licença/crédito:** o repo do Akita **não tem licença** (all rights
  reserved). Forkar no GitHub é ok; **republicar um port é obra derivada**. Como
  o SAMIRHV é público, antes de subir: **pedir OK/licença ao Akita** e **creditar**
  ele com destaque (README + tela). Ver [[estado-atual]].

## 2. O app hoje (o que estamos portando)

Rails **8.1.3** (Ruby 3.4.5), slim (sem mailer/storage/cable/action-text).
**SQLite** + **Solid Queue/Cache** (sem Redis). **Hotwire** (Turbo + Stimulus),
importmap + Propshaft, **Tailwind 4**. Charts em **canvas hand-rolled** (Stimulus).
Dados via **GitHub GraphQL** (commits com additions/deletions em lote) + REST
(repos e Actions runs). **Sem auth** no original ("rede confiável").

**Features:** replay da timeline (linhas +/− por bucket, log-scale, contador
animado, feed `git log` com scan bar) · heatmap dia×hora · "race to green" (1 lane
por workflow) · dashboard (barras commits/repo, cards ordenáveis, autocomplete,
sync status ao vivo) · replay ao rolar (janela 15/42/60/90 dias) · respeita
`prefers-reduced-motion`.

## 3. Princípio da migração

**Traduzir, não reescrever.** Cada peça Rails tem análogo direto no Laravel.
Onde é JS puro (canvas), **reaproveitar**. Onde é convenção (models, jobs,
controllers), **traduzir 1:1**. Sem inventar arquitetura nova.

| Rails 8.1 | Laravel (no SAMIRHV) | Dificuldade |
|---|---|---|
| Models ActiveRecord | Eloquent + migrations (tabelas `github_*`) | trivial |
| Controllers | `App\Http\Controllers\Admin\GitHubView\*` | fácil |
| Jobs Solid Queue | Jobs na fila (driver DB) ou on-demand + scheduler | fácil |
| `Github::Client` (GraphQL+REST) | `GitHubClient` via `Http::` (mesmas queries) | fácil |
| Presenters de visualização | View models / classes PHP | fácil |
| Views `.erb` (Tailwind) | Blade no **layout do admin** | mecânico |
| Stimulus (canvas charts) | **reaproveitar** como módulos ES | baixo |
| Turbo (sync ao vivo) | polling (`fetch`) — o controller já faz | baixo |
| SQLite | **MySQL** do samirhv (nossa regra) | trivial |

## 4. Modelo de dados (migrations)

Prefixar `github_` p/ não colidir com as tabelas do samirhv. Colunas exatas do
`db/schema.rb` do original:

**`github_repositories`**
`owner`, `name` — **unique(owner,name)** · `description` · `default_branch` ·
`last_synced_at` · `sync_status` (`pending|syncing|synced|failed`, default `pending`) ·
`sync_error` · `sync_progress` · timestamps.

**`github_commits`**
`repository_id` (FK) · `sha` — **unique(repository_id,sha)** · `message` ·
`author_login` · `committed_at` · `additions` (int, 0) · `deletions` (int, 0) ·
timestamps. Índices: `(repository_id, committed_at)`, `(repository_id, sha)` unique.

**`github_workflow_runs`**
`repository_id` (FK) · `github_id` (**bigint**) — **unique(repository_id,github_id)** ·
`workflow_name` · `run_number` · `status` · `conclusion` · `branch` ·
`run_started_at` · timestamps. Índices: `(repository_id, github_id)` unique,
`(repository_id, run_started_at)`.

**Models Eloquent** (portar os métodos do Rails):
- `Repository`: `hasMany(commits/workflowRuns)`; `fullName` (`owner/name`),
  `githubUrl`; `startSync()/finishSync()/failSync($e)` (mexem em
  `sync_status/sync_error/sync_progress/last_synced_at`); const `SYNC_STATUSES`;
  validação do formato de `owner`/`name` (`/\A(?!\.{1,2}\z)[\w.-]+\z/`);
  `defaultOwner()` = `config('services.github.owner')` (env `GITHUB_OWNER`).
- `Commit`: `belongsTo(repository)`, scope `chronological` (order `committed_at`),
  `summary()` (message truncada 100).
- `WorkflowRun`: `belongsTo(repository)`, scope `chronological` (`run_started_at`),
  `green()` (`conclusion==success`), `red()` (`failure|timed_out|startup_failure`).

## 5. Cliente GitHub (`GitHubClient`)

Reusar **as mesmas** chamadas do `app/services/github/client.rb`, via `Http::`:
- **GraphQL** `HISTORY_QUERY` → `repositoryOverview($owner,$name, since:, maxCommits:)`
  paginado (100/página, `cursor`, `since` p/ incremental), retornando
  `additions/deletions/committedDate/messageHeadline/author.login` por commit +
  `description`/`defaultBranch`. **Copiar a query GraphQL literal** — não reinventar.
- **REST** `authenticatedLogin()` (`/user`), `userRepositories()` (`/user/repos?
  affiliation=owner&sort=pushed`, p/ o autocomplete), `workflowRuns($owner,$name)`
  (`/repos/:o/:n/actions/runs`).
- **Token:** `GITHUB_TOKEN` (fine-grained: **Contents:read + Actions:read**).
  Erros tipados: `MissingToken`, `NotFound`, `Error` genérico.

## 6. Sincronização (jobs)

- `SyncRepositoryJob($repository)`: `startSync()` → **commits** (GraphQL, upsert por
  `(repository_id,sha)`, incremental via `since = max(committed_at)+1s`, teto
  `INITIAL_COMMIT_LIMIT=2000`, atualizando `sync_progress`) → grava
  `description/default_branch` → **workflow_runs** (REST, upsert por
  `(repository_id,github_id)`, teto `WORKFLOW_RUN_LIMIT=300`) → `finishSync()`.
  Em erro tipado → `failSync($msg)`.
- `SyncAllRepositoriesJob`: enfileira o sync de cada repo.
- **Execução:** como é ferramenta de **1 usuário**, começar **on-demand** (botão
  "sync") + `schedule:run` diário; `queue:work` 24/7 é opcional (o samirhv não
  roda fila hoje). `upsert` do Eloquent cobre o `upsert_all`.

## 7. Rotas e controllers (sob `/admin/github-view`)

Mapa direto das rotas do `config/routes.rb` (tudo sob o middleware do admin):

| Rails | Laravel (`/admin/github-view`) | Controller |
|---|---|---|
| `root dashboard#index` | `GET /admin/github-view` | `DashboardController@index` |
| `POST /repositories` | `POST …/repositories` | `RepositoryController@store` |
| `GET /suggestions` | `GET …/suggestions` | `SuggestionController@index` (autocomplete) |
| `GET repos/:owner/:name` | `GET …/repos/{owner}/{name}` | `RepositoryController@show` |
| `DELETE repos/:owner/:name` | `DELETE …/repos/{owner}/{name}` | `RepositoryController@destroy` |
| `POST …/sync` | `POST …/repos/{owner}/{name}/sync` | `SyncController@store` (dispara o job) |
| `GET …/status` | `GET …/repos/{owner}/{name}/status` | `SyncStatusController@show` (polling JSON/partial) |

Namespace sugerido: `App\Http\Controllers\Admin\GitHubView\*`, models em
`App\Models\GitHubView\*` (ou `GithubRepository/GithubCommit/GithubWorkflowRun`).

## 8. Presenters → view models

Portar `app/presenters/visualizations/*` como classes PHP que recebem o
`Repository` e devolvem os dados já no formato do chart:
- `RepositoryOverview` (cards, barras commits/repo, atividade diária, último CI).
- `CommitTimeline` (buckets +/− por janela de dias, log-scale, feed).
- `CommitHeatmap` (matriz dia×hora, contagem por célula) — **usa timezone**
  (`APP_TIME_ZONE`) pro bucket; atenção ao fuso.
- `CiLanes` (1 lane por workflow, ticks green/red por run).

## 9. Frontend (a parte com mais decisão)

Os controllers Stimulus são **canvas puro** (~18 KB total) — **portáveis**:
`heatmap` (3.4k), `timeline` (5.2k), `ci_lanes` (2.7k), `bar_scale` (0.5k),
`autocomplete` (3.4k), `sync_status` (2.1k, faz **polling** via `fetch`),
`reveal` (0.4k, IntersectionObserver p/ replay ao rolar).

**Decisão a tomar (ver §11):**
- **(A) Manter Stimulus** no samirhv (via importmap ou npm) → reaproveita os
  controllers quase sem tocar. Menos esforço.
- **(B) Reescrever em Alpine** (mais idiomático no ecossistema Laravel/Canvas do
  samirhv) → mais trabalho, mais "nativo".

**Markup:** as views ERB usam **Tailwind 4**; o admin do samirhv usa CSS próprio
(tema Canvas), **não** Tailwind. Os **canvas em si não dependem de Tailwind** (só
o container/cards). Opções: (a) trazer Tailwind escopado só nas telas do
github-view, ou (b) reescrever o layout dos cards com o CSS do admin. Preferir
**(b)** p/ não introduzir Tailwind no samirhv.

**Sync ao vivo:** manter **polling** (`GET …/status`) — o `sync_status_controller`
já faz isso; sem Turbo/WebSocket.

## 10. Config, segurança e crédito

- **.env do samirhv:** `GITHUB_TOKEN`, `GITHUB_OWNER` (owner default do add-form),
  `APP_TIME_ZONE` (bucket dos charts). **Sem** `SECRET_KEY_BASE` (é do Rails).
  Adicionar bloco em `config/services.php` (`github.token`, `github.owner`).
- **Segurança:** herda o gate do admin (nada exposto sem login) → resolve o "sem
  auth" do original. Token com **escopo mínimo** (Contents+Actions read). Ver
  [../SECURITY_GUIDELINES.md](../SECURITY_GUIDELINES.md).
- **Crédito Akita:** citar autoria na tela do github-view e no README; pedir
  licença antes de deixar público.
- **Regra do projeto:** MySQL/MariaDB (nunca SQLite) — já satisfeito por construção.

## 11. Riscos e decisões em aberto

- [ ] **Stimulus (A) vs Alpine (B)** nos charts (§9). Recomendo **A** p/ o 1º corte
      (reaproveita o canvas), migrar p/ B depois se valer.
- [ ] **Tailwind escopado vs CSS do admin** no markup dos cards (§9). Recomendo CSS
      do admin.
- [ ] **Fila:** on-demand + scheduler vs `queue:work` 24/7 (§6).
- [ ] **Rate-limit do GitHub** (GraphQL tem custo por página) — os tetos 2000/300
      já protegem; validar com repos grandes.
- [ ] **Timezone** do bucket do heatmap (`APP_TIME_ZONE`) — conferir DST/fuso.
- [ ] **Multi-usuário?** No original é single-tenant; no admin do samirhv é só o
      Samir — manter single-tenant (sem `user_id` nas tabelas) por ora.

## 12. Ordem sugerida (incremental, ver rodar cedo)

1. **Fatia vertical mínima:** migrations (3 tabelas) → models → `GitHubClient`
   (só o GraphQL de commits) → `SyncRepositoryJob` (só commits) → rota
   `/admin/github-view` + item de menu → **1 visualização: o heatmap**
   (presenter + Blade + reaproveitar `heatmap_controller.js`). **Rodar com dados
   reais.**
2. Timeline replay (presenter + `timeline_controller.js`).
3. Workflow runs (REST no client + job) + "race to green" (`ci_lanes`).
4. Dashboard completo (cards ordenáveis, barras, autocomplete via `/suggestions`
   + `userRepositories`), sync status por polling.
5. Polimento: janelas 15/42/60/90d, `prefers-reduced-motion`, crédito ao Akita.

## 13. Aceite ("pronto" quando)

- [ ] Item **"GitHub View"** no menu do `/admin`, atrás da auth.
- [ ] Adicionar um repo (`owner/name`) → sync → heatmap + timeline + CI animam com
      dados reais do GitHub.
- [ ] Sync incremental (não rebaixa nem duplica) e status ao vivo por polling.
- [ ] Zero runtime Ruby; roda no deploy padrão do samirhv (MySQL).
- [ ] Crédito ao Akita visível; token com escopo mínimo no `.env`.

## 14. Referências (arquivos do fork)

`db/schema.rb` · `config/routes.rb` · `app/models/{repository,commit,workflow_run}.rb` ·
`app/services/github/client.rb` (GraphQL `HISTORY_QUERY`) ·
`app/jobs/sync_repository_job.rb` · `app/presenters/visualizations/*` ·
`app/javascript/controllers/*` (canvas) · `.env.example` (envs) ·
`README.md` (features). Fork: `github.com/samirhvbr/github-visualize`.
