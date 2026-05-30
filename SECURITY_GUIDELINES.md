# Samirhv Blog — Diretrizes de Segurança

**Versão:** 1.0
**Data:** 30/05/2026
**Escopo:** Blog pessoal em Laravel — aplicação web pública sem área de login no front-end.

---

## Regras Gerais (obrigatórias)

1. **Nunca commitar** `.env`, chaves de API, senhas ou credenciais.
2. **XSS:** usar `{{ }}` no Blade (escapa automaticamente). Usar `{!! !!}` **apenas** para HTML confiável gerado internamente (conteúdo de posts).
3. **SQL Injection:** usar sempre o Query Builder ou Eloquent. Nunca interpolação direta de string em queries.
4. **CSRF:** manter `@csrf` em todos os formulários POST.
5. **Rate Limiting:** configurar throttle nas rotas de contato/formulários.
6. **Dependências:** manter `composer.json` atualizado. Verificar advisories com `composer audit`.

## Headers de Segurança

Configurar no Nginx/Apache:

```
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
Referrer-Policy: strict-origin-when-cross-origin
Content-Security-Policy: (ajustar conforme assets carregados)
```

## Conteúdo de Posts

O campo `content` dos posts é renderizado com `{!! !!}`. Enquanto o conteúdo vier de um array estático interno (não de input de usuário), isso é seguro. Ao migrar para banco de dados com editor externo, sanitizar com biblioteca como `HTMLPurifier` antes de salvar.

## Logs

- Logs em `storage/logs/` — nunca expor via URL pública.
- Não logar dados pessoais de visitantes desnecessariamente.
- `APP_DEBUG=false` em produção.

## Deploy

- `APP_ENV=production` e `APP_DEBUG=false` no `.env` de produção.
- Executar `php artisan optimize` após deploy.
- Permissões: `storage/` e `bootstrap/cache/` com `775`, owner do processo web.
