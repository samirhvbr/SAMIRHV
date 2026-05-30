# Configuração Claude Code — Blue3

## Modelo
Usando `opusplan` (híbrido). Opus pra raciocínio, Sonnet pra execução.

## Effort
`max` + adaptive thinking desabilitado.

## Permissões críticas
- `.env`, chaves OAuth Passport, `auth.json` bloqueados
- `migrate:fresh`, `db:wipe` bloqueados — proteção contra perda de dados
- `mysql/mariadb` direto bloqueados — Claude deve gerar migrations, não rodar SQL


