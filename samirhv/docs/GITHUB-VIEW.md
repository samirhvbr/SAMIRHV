# GitHub View — configuração do token

Módulo do `/admin` que monitora seus repositórios do GitHub e mostra visualizações
animadas (heatmap dia×hora, barras de commits por repo, atividade por repo). Porte
do [`github-visualize`](https://github.com/akitaonrails/github-visualize) (Fabio Akita).

Pra funcionar só precisa de **UMA** coisa: um **token do GitHub — somente leitura**.
Este doc mostra como criar com o **mínimo** de permissão.

---

## 1. Criar o token (fine-grained, read-only)

1. Acesse **<https://github.com/settings/personal-access-tokens>** → **Generate new token** → **Fine-grained token**.
2. **Token name:** ex. `samirhv-github-view`. **Expiration:** à vontade — quando expirar, o sync falha com **401** e é só gerar outro.
3. **Resource owner:** sua conta (ex. `samirhvbr`).
4. **Repository access:**
   - **All repositories** (recomendado) — pra monitorar/importar todos os seus repos.
   - ou **Only select repositories** — se quiser limitar a alguns.
   - ⚠️ Repos **privados** só aparecem se estiverem no escopo do token. Repos **públicos** qualquer token lê.
5. **Permissions → Repository** — **todas Read-only**:

   | Permissão | Acesso | Pra quê |
   |---|---|---|
   | **Metadata** | Read-only | **Obrigatória** (o GitHub exige; habilita listar seus repos p/ "Importar todos") |
   | **Contents** | Read-only | **Obrigatória** — commits, heatmap, `+add/−del`, descrição e branch default |
   | **Actions** | Read-only | **Opcional** — runs de CI (o "race to green"). Sem ela, commits/heatmap continuam funcionando (o CI é *best-effort*) |

   **Não precisa de mais nada.** Nenhuma permissão de **Account**, nenhuma de **escrita** — o GitHub View **só lê**. Pode remover Commit statuses, Copilot, Pull requests, etc.
6. **Generate token** e **copie** (só aparece uma vez).

---

## 2. Colocar no `.env`

```env
GITHUB_TOKEN=github_pat_...        # o token gerado acima
GITHUB_OWNER=samirhvbr            # owner default (pra digitar só "repo" no add-form)
APP_TIME_ZONE=America/Sao_Paulo   # opcional: fuso dos gráficos (fallback: APP_TIMEZONE)
```

Depois: `php artisan config:clear` (ou o deploy padrão, que já limpa o cache).

---

## 3. Usar

- **/admin → GitHub View** → **add + sync** (`owner/name`, ou só `name` usando o `GITHUB_OWNER`) — ou **Importar todos os meus repos**.
- Um **cron horário** (`php artisan github-view:sync`, agendado em `routes/console.php`) re-sincroniza sozinho, **incremental**. Testar na mão: `php artisan github-view:sync`.

---

## Problemas comuns

| Sintoma | Causa | Correção |
|---|---|---|
| **401 Unauthorized** | token inválido ou **expirado** | gere outro token e atualize o `.env` (+ `config:clear`) |
| **403 Forbidden** buscando CI | falta **Actions: Read** ou o repo não está no escopo | adicione **Actions** read, ou inclua o repo no escopo. Sem Actions, o repo ainda sincroniza — só não traz o CI |
| repo **privado** não sincroniza | fora do escopo do token | use **All repositories** ou selecione o repo em **Only select** |
| **timeout** no CI (repo com muitos runs) | histórico grande de Actions | é *best-effort*: o repo fica **synced** mesmo assim; o CI entra no próximo ciclo |
| `GITHUB_TOKEN não configurado` | `.env` sem o token | preencha `GITHUB_TOKEN` e rode `config:clear` |

Link do token: **<https://github.com/settings/personal-access-tokens>**
