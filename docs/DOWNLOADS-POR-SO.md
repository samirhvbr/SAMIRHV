# Downloads agrupados por Sistema Operacional

> Página de projeto `/p/{slug}`: os arquivos são agrupados por SO (Linux/Windows/
> macOS), com detecção automática do SO do visitante, arquivo "recomendado para
> você" e comando de instalação por pacote. Entregue na v0.2.2.

## Modelo de dados

`project_files` ganhou (migration `2026_06_29_130000_add_os_columns_to_project_files_table`,
com `Schema::hasColumn` guards):

| Coluna | Tipo | Uso |
|---|---|---|
| `os` | `string(16)` nullable, indexada | `linux` \| `windows` \| `macos` |
| `arch` | `string(16)` nullable | `x64` \| `arm64` \| `universal` (null → assume x64) |
| `file_type` | `string(16)` nullable | `deb`, `appimage`, `exe`, `msi`, `rpm`, `dmg`, `pkg`, `zip`… |
| `released_at` | `timestamp` nullable | data de lançamento (fallback p/ `created_at`) |

## Como os campos são preenchidos

1. **Upload no Admin** (`/admin/projetos/{p}/arquivos`): o form tem selects de
   **SO** (obrigatório) e **Arquitetura**, e o campo **Tipo**. Ao escolher o
   arquivo, um JS pré-preenche os três a partir do nome — sempre com override
   manual. Validação: `os` `required|in:linux,windows,macos`; `arch`
   `nullable|in:x64,arm64,universal`.
2. **CLI** (`php artisan files:add`): infere de `original_name` automaticamente.
3. **Inferência** (`App\Support\FilenameInspector::inspect($nome)`): extensão →
   tipo; `amd64`/`x86_64`/`x64`→`x64`, `arm64`/`aarch64`→`arm64`; tipo/tokens →
   SO. É a fonte única usada pelo Admin (JS espelha), pelo CLI e pelo backfill.

## Backfill dos arquivos existentes

```bash
php artisan downloads:backfill-os
```

Idempotente — só toca nas linhas com `os` null. Classifica por `original_name` e
preenche `released_at = created_at` quando ausente. **Rode uma vez após a
migration** (já incluído no fluxo de deploy).

## Página pública (`DownloadPresenter`)

`App\Services\DownloadPresenter::for($project, $request)` monta:

- **Detecção de SO** por User-Agent (`App\Support\OsDetector`): define a aba
  default e o arquivo recomendado. UA desconhecido → `linux`. Arch é best-effort
  (default `x64`; `arm64` só quando o UA expõe).
- **Abas por SO** com contadores; SO sem arquivo → "em breve" (aba desabilitada).
- **Grupos por versão**: mais recente aberto (marca `is_latest`); demais em
  "Versões anteriores (N)" recolhível.
- **Recomendado** (null-safe): latest de `(os detectado + arch)` → `os + x64` →
  `os` qualquer → primeiro SO com build (com aviso "ainda não há build para X").
- **Comando de instalação** (`App\Support\InstallCommand::for($file)`): por
  `(os, file_type)` — `apt`/`dpkg` (`.deb`), `chmod +x` (`.AppImage`), `dnf`
  (`.rpm`); `.exe`/`.msi` instalam pelo assistente. Verificação sempre presente:
  `sha256sum` (Linux), `shasum -a 256` (macOS), `Get-FileHash` (Windows).

A aba default e o recomendado vêm renderizados do servidor → **funciona sem JS**
(o JS só troca de aba, recolhe versões antigas e copia sha/comando).

## Como adicionar um SO novo (ex.: estender macOS ou incluir `.rpm`)

1. **macOS já está pronto**: basta subir arquivos com `os=macos` — a aba deixa de
   mostrar "em breve" sozinha.
2. **Novo tipo de pacote** (ex.: `.rpm` para Linux): já é inferido pelo
   `FilenameInspector`. Para um comando de instalação dedicado, ajuste o `match`
   em `App\Support\InstallCommand`.
3. **Novo SO** fora de linux/windows/macos: acrescente em `OsDetector::OSES` +
   `OsDetector::LABELS`, na regra de `OsDetector::detect()` e no `match` de
   `FilenameInspector`. As abas e o agrupamento passam a considerá-lo
   automaticamente.

## Contador de downloads

Inalterado: o clique em **Baixar** vai para `/d/{file}`
(`DownloadController@track`), que incrementa `downloads_count` e audita.

## Limitações conhecidas

- Arch por User-Agent é fraca (Safari/Apple Silicon reporta Intel) → default `x64`.
- "Versão Beta" é tratada como string da versão (sem coluna de canal separada).
- O comando de instalação assume nomes de pacote padrão (sem repositório apt/winget próprio).
