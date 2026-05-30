# Samirhv Blog — Guia para Agentes de IA

Este documento orienta agentes de IA (Claude Code, etc.) que trabalham no projeto **Samirhv Blog**.

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
- **Nomes de views:** `snake_case` em sub-pastas (ex: `blog/show.blade.php`).
- **Rotas nomeadas:** sempre com `->name()`, ex: `route('blog.show', $slug)`.
- **Assets:** sempre via `asset('vendor/canvas/...')`, nunca caminho relativo.
- **Str::limit:** usar para truncar excerpts no blade.

## Adicionando Posts

Posts são definidos no array `allPosts()` do `BlogController`. Cada post tem:

```php
[
    'slug'         => 'meu-post',        // URL: /blog/meu-post
    'title'        => 'Título do post',
    'excerpt'      => 'Resumo curto...',
    'content'      => '<p>HTML do conteúdo</p>',
    'category'     => 'tecnologia',      // deve estar em categories()
    'tags'         => ['tag1', 'tag2'],
    'date'         => '30 mai. 2026',
    'reading_time' => 5,                 // minutos
    'featured'     => false,             // destaque na home
]
```

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
