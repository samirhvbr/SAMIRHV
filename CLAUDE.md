# Samirhv — Central de Projetos/Downloads — Guia para Agentes de IA

Este documento orienta agentes de IA (Claude Code, etc.) que trabalham no projeto **Samirhv** — uma central pessoal para organizar e disponibilizar projetos para download (não é mais um blog; pivotado na v0.2.0).

---

## Comunicação

- **Idioma:** Português (pt-BR) para mensagens ao operador, comentários e textos de UI.
- **Commits:** Formato `versão - comentário` (ex: `0.1.0 - adiciona página de contato`). Versão extraída de `version.md`. Mensagem em português.
- **Identificadores de código:** Inglês (classes, métodos, variáveis, rotas).
- **Strings de UI:** Português.

---

## Stack

- **Framework:** Laravel (PHP 8.4+), pasta `samirhv/`
- **Template engine:** Blade
- **Frontend:** Canvas 7 (tema HTML5) — assets em `public/vendor/canvas/`
- **Banco de Dados:** MySQL / MariaDB — nunca usar SQLite em nenhum contexto
- **CSS theme:** `public/vendor/canvas/style.css` + `css/blog-theme.css`

---

## Pastas Temporárias

`tmp/` na raiz é para referência visual apenas — não referenciar no código de produção. Se precisar de um asset de lá, copiar para `public/vendor/canvas/`.

---

## Convenções (Laravel)

- **Controllers finos:** request handling + response. Lógica vai em Services.
- **Nomes de views:** `snake_case` em sub-pastas (ex: `projects/show.blade.php`).
- **Rotas nomeadas:** sempre com `->name()`, ex: `route('project.show', $project)`.
- **Assets:** sempre via `asset('vendor/canvas/...')`, nunca caminho relativo.
- **Str::limit:** usar para truncar textos no blade.

## Estrutura (v0.2.0+)

**Público** (`routes/web.php`): `/` (home, vitrine), `/downloads` (lista), `/p/{slug}` (projeto), `/d/{file}` (download com contagem + auditoria), `/login`, `/logout`.

**Admin** (`routes/admin.php`, prefixo `/admin`, middleware `auth,admin,password.changed`): dashboard, `projetos` (CRUD), `projetos/{p}/arquivos` (upload), `auditoria` (downloads + analytics), `auditoria-acesso` (ações/logins), `perfil`.

**Modelo de dados:** `Project` → `hasMany ProjectFile`. Cada arquivo tem `downloads_count`; cada download gera uma linha em `download_logs`. Auditoria de visitas em `page_views` (via middleware `TrackPageView`), ações do admin em `activity_logs` (`AuditLogger`), autenticação em `auth_events` (listeners no `AppServiceProvider`).

**Arquivos para download:** disco `downloads` **privado** (`storage/app/private/downloads`). O único acesso é via `/d/{file}` (`DownloadController`), que conta e audita. Upload no admin tem limite de 500 MB; arquivos maiores: `php artisan files:add <path> --project=<slug>`.

**Admin único:** flag `users.is_admin` (sem Spatie). Seeder `AdminUserSeeder` cria o admin (`ADMIN_EMAIL`/`ADMIN_PASSWORD` no `.env`) com troca de senha obrigatória no 1º acesso. Sem cadastro público.

## Comandos Rápidos

| Comando                          | Uso                                   |
|----------------------------------|---------------------------------------|
| `php artisan serve`              | Servidor local (http://localhost:8000)|
| `php artisan route:list`         | Lista rotas registradas               |
| `php artisan view:clear`         | Limpa cache de views                  |
| `php artisan optimize:clear`     | Limpa todo cache                      |
| `php -l arquivo.php`             | Valida sintaxe PHP                    |

## Checklist de PR

- [ ] `php -l` em arquivos PHP alterados
- [ ] `php artisan route:list` sem erros
- [ ] `php artisan view:cache` valida Blade (depois `view:clear`)
- [ ] `README.md` atualizado se mudou estrutura
- [ ] `version.md` incrementado (Z+1 para mudança de layout/feature)
