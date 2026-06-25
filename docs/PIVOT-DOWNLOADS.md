# Pivot 0.2.0 — De blog para Central de Projetos/Downloads

> Documentação da mudança que transformou o **Samirhv** de blog pessoal em uma
> central para organizar e disponibilizar projetos para download, com painel
> admin e auditoria. Layout (tema Canvas escuro) preservado.

**Data:** 2026-06-24 · **Versão:** 0.1.0 → 0.2.0 · **Stack:** Laravel 13 (PHP 8.3+), Blade, MySQL/MariaDB.

---

## 1. Visão geral

O site deixou de ser blog. Agora:

- O público navega projetos e baixa arquivos.
- O admin (você, único) faz login, gerencia projetos e seus arquivos (upload).
- Cada download é **contado por arquivo** e **auditado**, no mesmo padrão da auditoria do SShvTerm (KPIs, gráficos, top páginas/IPs, dispositivos/navegadores, bots, logins do painel, ações do admin).

O blog (rotas `/blog`, `BlogController`, views) foi **removido**.

---

## 2. Rotas

### Público (`routes/web.php`)
| Rota | Nome | Controller | O quê |
|---|---|---|---|
| `GET /` | `home` | `SiteController@home` | Vitrine: projetos em destaque |
| `GET /downloads` | `downloads` | `SiteController@downloads` | Lista de projetos + arquivos |
| `GET /p/{project}` | `project.show` | `SiteController@show` | Detalhe do projeto (binding por `slug`) |
| `GET /d/{file}` | `download.track` | `DownloadController@track` | Download com contagem + auditoria |
| `GET /login` | `login` | `Auth\LoginController@show` | Tela de login |
| `POST /login` | `login.attempt` | `Auth\LoginController@login` | Autentica (throttle 5/min) |
| `POST /logout` | `logout` | `Auth\LoginController@logout` | Sai |

### Admin (`routes/admin.php` — prefixo `/admin`, middleware `auth, admin, password.changed`)
`dashboard` · `projects.{index,create,store,edit,update,destroy}` · `projects.files.{index,store,available,destroy}` · `audit.index` · `access-audit.index` · `profile` / `profile.password`.

Registrado em `bootstrap/app.php` via `withRouting(then: …)` dentro do grupo `web`.

---

## 3. Modelo de dados (migrations `2026_06_24_0000xx`)

- **projects**: `title, slug(unique), description, category, icon, is_published, sort_order` + softDeletes.
- **project_files**: `project_id(FK), label, filename, original_name, version, size, sha256, is_available, downloads_count` + softDeletes.
- **download_logs**: `project_file_id(FK,nullOnDelete), user_id, ip, user_agent, referer, method, is_bot, locale`. Uma linha por download concluído. Sem pruning.
- **page_views**: visitas públicas (`path, ip, user_agent, is_bot, device, browser, os, referer, locale`) — alimentado pelo middleware `TrackPageView` (roda em `terminate()`).
- **activity_logs**: ações do admin (`event, subject_type, subject_id, description, ip_address, user_agent`) — via `AuditLogger`.
- **auth_events**: login/falha/logout do painel — via listeners no `AppServiceProvider`.
- **users** (+colunas): `is_admin, must_change_password, last_login_at, last_login_ip`.

Relações: `Project hasMany ProjectFile`; `ProjectFile hasMany DownloadLog`.

---

## 4. Componentes

**Services** (`app/Services/`)
- `UserAgentParser` — bot/dispositivo/navegador/SO por heurística (UA + faixas de IP de crawler). Sem dependência externa.
- `AnalyticsService` — métricas (fuso `America/Sao_Paulo`, bots à parte): `cards, visitsByDay, downloadsByDay, topPages, topIps, byDevice, byBrowser, downloadsByProject, topFiles, bots`.
- `FileIngestService` — grava upload/arquivo no disco `downloads`, calcula `size`+`sha256`, faz upsert de `ProjectFile`. Reupload do mesmo nome atualiza (preserva o contador).
- `AuditLogger` — grava `activity_logs` (nunca segredos).

**Middlewares** (`app/Http/Middleware/`)
- `EnsureIsAdmin` (alias `admin`) — exige `users.is_admin`.
- `EnsurePasswordChanged` (alias `password.changed`) — força troca da senha inicial.
- `TrackPageView` — registra visitas públicas.

**Auth** — `Auth\LoginController` com `Auth::attempt` (dispara eventos Login/Failed/Logout nativamente; **sem `laravel/ui`**). Admin único via `is_admin` (sem Spatie). Sem cadastro público.

---

## 5. Armazenamento de arquivos

Disco **`downloads`** (`config/filesystems.php`) → `storage/app/private/downloads` (**privado**). O único acesso é via `/d/{file}` (`DownloadController`), que conta e audita — **não há bypass do contador**. Não precisa de `storage:link`.

Contagem em `DownloadController@track`: conta só `GET` completo (ignora `Range` parcial e bots); incrementa `downloads_count` e grava `download_logs`; serve via `Storage::disk('downloads')->download(...)`.

Limite de upload via navegador: **500 MB**. Maiores: `php artisan files:add <caminho> --project=<slug> [--version=x] [--label="..."]`.

---

## 6. Admin / Auditoria (telas)

- `/admin` — dashboard (KPIs, top arquivos, downloads recentes).
- `/admin/projetos` — CRUD; `/admin/projetos/{p}/arquivos` — upload (XHR com barra de progresso + tratamento 413) e gestão.
- `/admin/auditoria` — downloads + analytics: 4 KPIs do dia, gráficos por dia, top páginas/IPs, dispositivos/navegadores, downloads por projeto, bots à parte, top arquivos e tabela filtrável (IP/projeto/arquivo/período). Colunas: Data/Hora · Projeto · Arquivo · Versão · IP · Origem · Cliente · Idioma.
- `/admin/auditoria-acesso` — abas **Ações do admin** (`activity_logs`) e **Logins do painel** (`auth_events`).
- `/admin/perfil` — troca de senha.

Gráficos são **barras em CSS puro** (sem lib JS), no layout `admin/layouts/app.blade.php` (design system embutido).

---

## 7. Pendências / observações

- **SECURITY_GUIDELINES.md** diz "sem área de login no front-end" — agora **há** painel admin. Atualizar quando der.
- Arquivo **SQLite legado** em `samirhv/samirhv` (96 KB) — cruft, não usado (regra: nunca SQLite). Pode remover.
- `composer audit` e revisão de headers (Nginx) seguem válidos.
- `README.md` do app ainda é o boilerplate do Laravel (não crítico).
