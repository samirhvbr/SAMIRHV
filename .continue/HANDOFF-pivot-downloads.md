# HANDOFF — Pivot blog → Downloads (v0.2.0)

**Data:** 2026-06-24 · **Status:** código pronto na working tree, **NÃO commitado**, **NÃO migrado** (não havia PHP nesta máquina). Doc completa: `docs/PIVOT-DOWNLOADS.md`.

---

## ⚙️ O QUE RODAR NO SERVIDOR (importante)

O `deploy.sh` faz pull de **master** → `php artisan down` → composer → npm build → `migrate --force` → optimize → up. **Mas ele NÃO roda seeders**, então o admin precisa de 1 passo extra (one-time).

### 1. Local (antes): commitar e enviar para master
```bash
cd ~/x/SAMIRHV
git add -A
git commit -m "0.2.0 - pivot: blog -> central de projetos/downloads (admin, upload, auditoria)"
git push origin master
```

### 2. No servidor: deploy normal
```bash
cd /srv/www/samirhv
./deploy.sh          # pull master, migrate --force (cria as 7 tabelas novas), optimize, up
```

### 3. No servidor: criar o admin (ONE-TIME, só na primeira vez)
```bash
cd /srv/www/samirhv/samirhv
# (opcional) definir a senha antes, senão o seeder gera uma aleatória e imprime:
#   edite .env e adicione:  ADMIN_PASSWORD=suaSenhaForte
php artisan db:seed --class=AdminUserSeeder --force
```
- Login: **samirhv@me.com** · senha = a do `.env` (ADMIN_PASSWORD) ou a aleatória impressa no output. **Troca obrigatória no 1º acesso.**
- Se não imprimiu senha (admin já existia), use a que já tinha ou rode o seeder com `ADMIN_PASSWORD` setado no `.env`.

### 4. Permissões de storage (se necessário)
Os arquivos vão para `storage/app/private/downloads` (disco privado). O usuário do PHP-FPM precisa de escrita em `storage/`. A pasta é criada no 1º upload. Se der erro de permissão:
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
```
Não precisa de `storage:link` (disco é privado de propósito — download só via `/d/{file}`, que conta).

---

## ✅ Verificação rápida (local, quando tiver PHP)
```bash
cd ~/x/SAMIRHV/samirhv
cp .env.example .env && php artisan key:generate
# ajuste DB_* (MySQL) no .env
php artisan migrate --seed
php artisan route:list   # confere home, downloads, project.show, download.track, admin.*
php artisan serve
```
Teste: `/` mostra projetos · menu **Downloads** + CTA **Admin** · footer GitHub/Instagram `samirhvbr`, LinkedIn `samirhv` · `/blog` = 404 · login → criar projeto → upload → baixar em `/d/{file}` → `downloads_count` +1 e linha em `download_logs` → ver em `/admin/auditoria`.

Arquivos grandes (>500 MB): `php artisan files:add <caminho> --project=<slug>`.

---

## 📩 Última mensagem (resumo do que foi entregue)

Pivot completo. O **Samirhv** deixou de ser blog e virou uma central de projetos/downloads, mantendo o visual Canvas escuro. Não rodei migrate/serve (sem PHP/Composer na máquina); fiz revisão estática (rotas, imports, balanceamento Blade) e corrigi os bugs encontrados.

**Site público:** menu Blog→Downloads; CTA "Ver todos os posts"→Admin; home repaginada com projetos; novas telas `/downloads` e `/p/{slug}`; footer com GitHub `samirhvbr`, Instagram `samirhvbr` (novo), LinkedIn `https://www.linkedin.com/in/samirhv/`; blog removido.

**Admin (`/admin`, sem cadastro):** login próprio com throttle, troca de senha no 1º acesso, admin único via `is_admin`; CRUD de Projetos→Arquivos com upload (barra de progresso + tratamento de limite); dashboard com KPIs.

**Downloads + Auditoria (padrão SShvTerm):** `/d/{file}` conta (ignora bots/parciais) e grava `download_logs`; arquivos em disco privado (sem furar o contador); `/admin/auditoria` com KPIs, gráficos, top páginas/IPs, dispositivos/navegadores, downloads por projeto, bots à parte, top arquivos e tabela filtrável; `/admin/auditoria-acesso` com abas Ações e Logins; visitas via middleware `TrackPageView`.

**Notas:** Laravel 13; auth sem `laravel/ui` (sem dependência nova); `version.md`→0.2.0; `CLAUDE.md` atualizado. Bugs corrigidos: `User::withTrashed()` (User não usa SoftDeletes), slug único auto-gerado, `max()` dos gráficos. Arquivo SQLite legado em `samirhv/samirhv` deixado intocado (cruft). Não commitei (aguardando você).

---

## ⏭️ Próximos passos sugeridos (quando voltar)
- Commitar/pushar para master e rodar o deploy + seeder (acima).
- Cadastrar os primeiros projetos e subir os arquivos.
- Atualizar `SECURITY_GUIDELINES.md` (agora há área de login no front).
- (Opcional) remover o SQLite legado `samirhv/samirhv`.
