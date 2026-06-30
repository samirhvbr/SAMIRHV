# ROTEIRO — samirhv.com.br: downloads separados por sistema operacional

> Deliverable para **Claude Code**. Site de distribuição de builds — página de projeto em `/p/{slug}`.
> PT-BR · Conventional Commits · **uma fase = um commit** · aspas simples no zsh.
> **Stack presumida:** Laravel + Blade + Tailwind (a **Fase 0 confirma**). Se não for Laravel, os conceitos (colunas `os`/`arch`/`file_type`, agrupamento, detecção por User-Agent, inferência por nome) valem igual — adaptar à stack real.
> **Alvo visual:** o redesenho aprovado — header com badges de SO, card "recomendado para você" com comando de instalação, abas Linux/Windows/macOS e lista por versão com metadados completos.
> **Objetivo central:** agrupar os arquivos por SO e **habilitar a subida dos builds de Windows** (o Admin precisa marcar o SO no upload).

---

## 0. Decisões travadas

| #  | Decisão | Valor (✅ travado / 🔁 trocável) |
|----|---------|----------------------------------|
| D1 | Escopo | Redesenhar a página `/p/{slug}`: **agrupar downloads por SO** + card recomendado + metadados por arquivo. **Inclui o lado Admin** (upload precisa gravar o SO). ✅ |
| D2 | SOs / arquiteturas | **Linux** (`.deb`, `.AppImage`) e **Windows** (`.exe`, `.msi`) agora; **macOS** como slot pronto (no enum; UI mostra "em breve" se vazio). Arch: `x64`, `arm64` (+ `universal`/null). 🔁 (fácil estender p/ `.rpm`, `.dmg`…) |
| D3 | Auto-detecção de SO | **Server-side por User-Agent** (Laravel) → define a **aba default** + o **arquivo recomendado**; o seletor de SO fica sempre disponível; **UA desconhecido → Linux**. 🔁 |
| D4 | Comando de instalação | **Por pacote**, derivado de `(os, file_type)`: `apt`/`dpkg` (`.deb`), `chmod +x` (`.AppImage`), instalador + `Get-FileHash` (Windows). Manter o bloco "terminal". 🔁 |
| D5 | Auto-inferência por nome | Helper deduz `os`/`arch`/`file_type` do **filename** (`amd64`/`x86_64`→x64; `arm64`/`aarch64`→arm64; extensão→tipo). Usado no **backfill** e no **Admin** (com override manual). ✅ |
| D6 | Migration | Adicionar `os`/`arch`/`file_type` (+ `released_at` se faltar) **com `Schema::hasColumn` guards**; **nunca** editar migration antiga. ✅ |
| D7 | Identidade | Manter **índigo/escuro/mono** (tokens Tailwind existentes; estender `theme` se preciso). **Sem estilo inline, sem hex hardcoded** fora do config. ✅ |
| D8 | Arquitetura de código | **Controllers finos** — detecção/agrupamento/recomendado/comando em **Service/Presenter**. ✅ |

---

## 1. O que muda (página `/p/{slug}`, de cima p/ baixo)

```
← Downloads
┌ header ───────────────────────────────────────────────────────┐
│ [icon] // APLICATIVO DESKTOP                                    │
│        GitHub Desktop                                           │
│        ●Linux ●Windows · v3.5.15 Beta 3 · 02 jun · x64·arm64    │  ← badges derivadas dos SOs presentes
│ descrição…                                                     │
├ RECOMENDADO PARA VOCÊ · Linux (x64)        [trocar de sistema] ─┤  ← auto-detect (server-side)
│ github-desktop_3.5.15_amd64.deb   .deb x64 195.9MB  [⬇ Baixar] │
│ ┌ terminal ──────────────────────────────────────  copiar ⧉ ┐ │
│ │ $ sudo apt install ./github-desktop_3.5.15_amd64.deb       │ │  ← comando por (os,tipo)  ★ assinatura
│ └───────────────────────────────────────────────────────────┘ │
├ Arquivos ──────────────────────────────────────────────────────┤
│ [Linux 5] [Windows 3] [macOS · em breve]                       │  ← abas + contadores por SO
│ ── v3.5.15 Beta 3   ● mais recente              02 jun 2026 ─── │
│   github-desktop_3.5.15_amd64.deb  [.deb][x64] 195.9MB         │
│     sha256 af4a83b3…⧉  12 downloads              [Baixar]      │
│   …_x86_64.AppImage                [AppImage][x64] …  [Baixar] │
│ ▸ Versões anteriores (1) — v3.5.13 Beta 3                      │  ← recolhível
├ Confira a integridade: sha256sum … / Get-FileHash …  ──────────┤
└────────────────────────────────────────────────────────────────┘
```

---

## 2. Arquitetura

```
GET /p/{slug}
  → DownloadPresenter
       • OsDetector: User-Agent → os (+ arch best-effort)  [unknown → linux]
       • agrupa files: os → version (mais recente 1º, marca latest, older recolhe)
       • availability: SOs distintos com arquivos; counts por SO
       • recommended: latest do (os detectado + arch) → fallbacks (null-safe)
       • InstallCommand: (os, file_type, filename) → comando instalar/verificar
  → Blade (Tailwind + Alpine só p/ trocar aba e copiar)

Admin upload/edit
  → FilenameInspector: filename → {os, arch, file_type}  (pré-preenche, editável)
  → salva os/arch/file_type
```

---

## 3. FASE 0 — Descoberta (NÃO escreve código)

```bash
cd <SAMIRHV_PATH>

# stack + rota/controller/view do projeto
cat composer.json 2>/dev/null | grep -i laravel
php artisan route:list 2>/dev/null | grep -iE '/p/|project|download|admin'
ls -la resources/views 2>/dev/null

# tabelas e colunas REAIS (o que enche os cards de download)
php artisan db:table 2>/dev/null | grep -iE 'project|file|release|download|asset'
php artisan tinker --execute="echo implode(',', \Schema::getColumnListing('files'));"      # ajustar nome
php artisan tinker --execute="echo implode(',', \Schema::getColumnListing('projects'));"   # ajustar nome
# procurar: os / arch / file_type / version / filename / size / sha256 / download_count / released_at

# como o Admin sobe arquivo (form + store) — onde adicionar os campos
grep -rniE 'upload|store.*file|FormRequest|sha256|hash_file' app/Http 2>/dev/null | head
grep -rniE 'enctype=.multipart|input.*file|@livewire|wire:model' resources/views 2>/dev/null | head

# Tailwind: tokens de cor/fontes existentes + build
ls -la tailwind.config.* vite.config.* 2>/dev/null
grep -niE '6366F1|indigo|violet|fontFamily|mono' tailwind.config.* 2>/dev/null | head

# já existe detecção de SO ou componente de download?
grep -rniE 'userAgent|user_agent|detect.*os|x-os|file-row' app resources 2>/dev/null | head
```

**Resolver / STOP-GATE — não codar até confirmar:**
1. `<SAMIRHV_PATH>`, a **stack**, a **rota `/p/{slug}`** + controller + view.
2. Nome real das tabelas **projects** e **files/releases** e suas **colunas** (confirmar via `tinker` — quais já existem: `version`, `filename`/`path`, `size`, `sha256`, `download_count`, `released_at`? e confirmar que **faltam** `os`/`arch`/`file_type`).
3. **Fluxo do Admin** para upload/edição de arquivo (form + controller `store`/`update`) — ponto exato onde os novos campos entram.
4. Tokens Tailwind existentes (índigo? mono?) e o build (vite?).

> Regra da casa: 100% de falha numa query = **artefato de query, não dado real** → confirmar via `tinker` antes de assumir.

---

## 4. FASE 1 — Migration `os`/`arch`/`file_type` + backfill
`commit: feat(downloads): colunas os/arch/file_type + backfill dos arquivos existentes`

**Migration** (ajustar nome da tabela; `hasColumn` guards):
```php
public function up(): void
{
    Schema::table('files', function (Blueprint $t) {          // <- nome real da Fase 0
        if (! Schema::hasColumn('files', 'os'))         $t->string('os', 16)->nullable()->index()->after('version');
        if (! Schema::hasColumn('files', 'arch'))       $t->string('arch', 16)->nullable()->after('os');
        if (! Schema::hasColumn('files', 'file_type'))  $t->string('file_type', 16)->nullable()->after('arch');
        if (! Schema::hasColumn('files', 'released_at')) $t->timestamp('released_at')->nullable()->after('file_type');
    });
}
```

**Helper de inferência** (`app/Support/FilenameInspector.php`) — usado aqui e no Admin:
```php
final class FilenameInspector
{
    public static function inspect(string $name): array
    {
        $n = strtolower($name);
        $ext = pathinfo($n, PATHINFO_EXTENSION);

        $type = match (true) {
            $ext === 'deb'                      => 'deb',
            $ext === 'rpm'                      => 'rpm',
            str_contains($n, '.appimage')       => 'appimage',
            $ext === 'exe'                      => 'exe',
            $ext === 'msi'                      => 'msi',
            $ext === 'dmg'                      => 'dmg',
            $ext === 'pkg'                      => 'pkg',
            $ext === 'zip'                      => 'zip',
            default                             => $ext ?: null,
        };

        $os = match (true) {
            in_array($type, ['deb','rpm','appimage'], true) || str_contains($n, 'linux') => 'linux',
            in_array($type, ['exe','msi'], true) || str_contains($n, 'win')              => 'windows',
            in_array($type, ['dmg','pkg'], true) || str_contains($n, 'mac') || str_contains($n, 'darwin') => 'macos',
            default => null,
        };

        $arch = match (true) {
            str_contains($n, 'arm64') || str_contains($n, 'aarch64') => 'arm64',
            str_contains($n, 'amd64') || str_contains($n, 'x86_64') || str_contains($n, 'x64') => 'x64',
            str_contains($n, 'universal') => 'universal',
            default => null,                                  // null → o Admin/recommended assume x64
        };

        return compact('os', 'arch', 'type') + ['file_type' => $type];
    }
}
```

**Backfill** (command idempotente, só onde `os` é null):
```php
// php artisan downloads:backfill-os
File::whereNull('os')->get()->each(function ($f) {
    $i = FilenameInspector::inspect($f->filename);          // <- coluna real do nome
    $f->update(array_filter([
        'os' => $i['os'], 'arch' => $i['arch'], 'file_type' => $i['file_type'],
    ]));
});
```

**Smoke:**
```bash
php artisan migrate
php artisan downloads:backfill-os
php artisan tinker --execute="echo \App\Models\File::select('os')->get()->groupBy('os')->map->count();"
# .deb amd64 → os=linux/arch=x64/type=deb · _arm64 → arm64 · .AppImage → appimage
```

---

## 5. FASE 2 — Admin: marcar o SO no upload
`commit: feat(admin): campos de SO/arquitetura/tipo no upload de arquivos`

> **É isto que faz o Windows agrupar** quando você subir os `.exe`.

- No **form de upload/edição**: campos **SO** (select: Linux/Windows/macOS), **Arquitetura** (select: x64/arm64/universal) e **Tipo** (auto da extensão, editável).
- **Pré-preencher** via `FilenameInspector` ao escolher o arquivo (JS no Admin) — sempre com **override manual**.
- `store`/`update` salvam `os`/`arch`/`file_type`. **Validação:** `os` obrigatório (`in:linux,windows,macos`); `arch` `nullable|in:x64,arm64,universal`.
- Calcular `sha256`/`size` no upload se ainda não fizer (confirmar na Fase 0).

**Smoke:** subir um `.exe` de teste → grava `os=windows`, `arch=x64`, `type=exe`; aparece na aba Windows da página pública (após Fase 4).

---

## 6. FASE 3 — Domínio: detecção + agrupamento + recomendado + comando
`commit: feat(downloads): detecção de SO, agrupamento e comando de instalação`

- **`OsDetector`**: `User-Agent` → `os` (`Windows NT`→windows; `Mac OS X`/`Macintosh`→macos; `Linux`/`X11`→linux; **senão linux**) + `arch` best-effort (`aarch64`/`arm64`→arm64; senão x64).
- **`InstallCommand::for($file)`** → comando por `(os, file_type)`:
  - `deb` → `sudo apt install ./{filename}` (alternativa: `sudo dpkg -i {filename}`)
  - `appimage` → `chmod +x {filename} && ./{filename}`
  - `rpm` → `sudo dnf install ./{filename}`
  - `exe`/`msi` → instalar pelo assistente; **verificar:** `Get-FileHash .\{filename} -Algorithm SHA256`
  - sempre disponibilizar a linha de verificação (`sha256sum {filename}` / `Get-FileHash`).
- **`DownloadPresenter::for($project, $request)`**:
  - agrupa `files` → `os` → `version` (mais recente 1º; marca **latest**; demais em "anteriores"); `counts` por SO; `availability` = SOs com ≥1 arquivo;
  - `recommended` = latest do `(os detectado + arch)` → fallback `os+x64` → `os qualquer` → (se nada no SO detectado) **primeiro SO com arquivos** + flag "não há build pra {os} ainda"; **null-safe**;
  - anexa `install_command` a cada arquivo.
- **Controller fino** instancia o presenter e passa pronto pra view.

**Smoke:**
```bash
curl -s -A 'Mozilla/5.0 (X11; Linux x86_64)'   <base>/p/github-desktop | grep -i 'recomendado'
curl -s -A 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)' <base>/p/github-desktop | grep -i 'recomendado'
# o recomendado/aba default deve mudar conforme o UA
```

---

## 7. FASE 4 — View pública (redesenho)
`commit: feat(downloads): página de projeto agrupada por SO + card recomendado`

Reconstruir a view de `/p/{slug}` conforme o mock:
- **Header:** ícone, eyebrow `// {tipo}`, título, **badges** derivadas (`availability` de SOs + versão + data + arquiteturas), descrição.
- **Card recomendado:** rótulo "Recomendado para você · {SO} ({arch})" + "trocar de sistema"; arquivo (nome, badges tipo/arch, tamanho, data) + **botão Baixar**; **bloco terminal** com o `install_command` + **copiar**. Sem build pro SO detectado → mensagem null-safe.
- **Abas de SO:** Linux / Windows / macOS, com **contadores**; SO sem arquivo → **"em breve"** (aba desabilitada, sem painel vazio).
- **Grupos por versão:** mais recente **aberto** (badge "● mais recente") + **"Versões anteriores (N)"** recolhível. Cada arquivo: nome (mono), badges (**tipo**, **arch**), **tamanho**, **data**, **sha256 copiável**, **downloads**, **Baixar**.
- **Dica de verificação:** `sha256sum {arquivo}` (Linux) / `Get-FileHash .\{arquivo} -Algorithm SHA256` (Windows).
- **Tailwind** com os tokens da identidade (índigo/escuro/mono); **Alpine** só p/ trocar aba e copiar. **A aba default e o recomendado vêm do servidor** → funciona sem JS (JS só melhora).
- Componentes sugeridos: `<x-download.file-row :file>`, `<x-os-icon :os>`, `<x-install-command :file>`.

**Smoke:** as 5 coisas do header→verify renderizam; trocar aba; copiar sha e comando; SO sem arquivo mostra "em breve"; nenhum card "fantasma".

---

## 8. FASE 5 — Polish, docs e versão
`commit: feat(downloads): polish + estados vazios + docs`
`commit: chore: bump version`

- **Responsivo:** mobile — abas com scroll horizontal, linhas de arquivo empilham, botões full-width.
- **Acessibilidade:** foco de teclado visível, `aria-label`/`alt` nos ícones de SO, abas como `role=tab` (ou `<button>` semântico).
- **No-JS:** aba default + recomendado já vêm renderizados do servidor.
- **Contador de downloads:** confirmar que o clique em **Baixar** incrementa `download_count` (rota de download existente — Fase 0).
- **docs/** (bloqueador de deploy): documentar as colunas `os`/`arch`/`file_type`, o backfill, a detecção por UA, a auto-inferência por nome e **como subir um SO novo** (ex.: macOS). `version.md`: bump.
- **Deploy** pelo processo já existente do repo.

---

## 9. Smoke tests (consolidado)

```bash
# Detecção/recomendado por UA
curl -s -A 'Mozilla/5.0 (X11; Linux x86_64)'           <base>/p/github-desktop | grep -iE 'recomendado|Linux'
curl -s -A 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)' <base>/p/github-desktop | grep -iE 'recomendado|Windows'

# Dados
php artisan tinker --execute="echo \App\Models\File::select('os')->get()->groupBy('os')->map->count();"
php artisan tinker --execute="echo \App\Models\File::whereNull('os')->count();"   # deve ser 0 após backfill

# Manual: trocar abas · copiar sha/comando · 'Versões anteriores' · aba 'em breve' · subir um .exe no Admin e ver na aba Windows
```

---

## 10. Checklist

- [ ] Fase 0 resolvida (stack, tabelas/colunas e fluxo do Admin confirmados via `tinker`)
- [ ] Migration `os`/`arch`/`file_type` (+`released_at`) com `hasColumn` guards
- [ ] `FilenameInspector` + backfill idempotente (Linux existente classificado)
- [ ] **Admin grava `os`/`arch`/`tipo`** (auto-preenche por nome, com override) — Windows pronto pra subir
- [ ] `OsDetector` + `DownloadPresenter` + `InstallCommand` (null-safe)
- [ ] View redesenhada: header/badges, card recomendado + terminal, abas com contadores, grupos por versão, sha copiável, verify
- [ ] Aba default + recomendado **server-side** (funciona sem JS)
- [ ] Estados vazios ("em breve"), responsivo, acessível
- [ ] `docs/` + `version.md` bump
- [ ] Nenhum arquivo sem `os`

---

## 11. Limitações conhecidas

- **Arch por User-Agent é fraca** → default `x64`; `arm64` só quando o UA expõe (`aarch64`/`arm64`).
- **macOS** sem build → "em breve" (enum já pronto; é só subir arquivos `os=macos`).
- **Auto-inferência** cobre nomes padrão (`amd64`, `arm64`, extensões conhecidas); nome fora do padrão exige ajuste manual no Admin.
- **Versão "Beta"** tratada como string da versão (sem coluna de canal `stable/beta` separada nesta entrega).
- **Comando de instalação** assume nomes de pacote padrão; ajustar se o seu fluxo usar repositório `apt`/`winget` próprio.
