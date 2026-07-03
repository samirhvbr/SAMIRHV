# SAMIRHV — REPOSITORIO

Blog pessoal de Samir Hanna Verza, construído com Laravel e tema Canvas.

## Tecnologias

- **Backend:** Laravel (PHP 8.4+)
- **Frontend:** Blade + Canvas 7 (tema HTML5 — assets compilados em `public/vendor/canvas/`)
- **Banco de Dados:** MySQL / MariaDB (nenhum outro, e nunca usar sqlite)
- **Servidor:** Debian (Linux)
- **GitHub:** Sempre faça commits em blocos e com uma boa descrição, padrão é a versão de version.md - (hífen) comentário

## Objetivo

Espaço pessoal para publicar posts sobre tecnologia, desenvolvimento de software, Linux e outros temas de interesse.

## Estrutura de Pastas

```
samirhv/                     ← raiz do repositório
├── samirhv/                 ← aplicação Laravel
│   ├── app/
│   │   ├── Http/Controllers/BlogController.php
│   │   └── ...
│   ├── public/
│   │   ├── vendor/canvas/   ← assets do tema Canvas (CSS, JS)
│   │   └── favicon.ico
│   ├── resources/views/
│   │   ├── layouts/app.blade.php
│   │   ├── home.blade.php
│   │   └── blog/            ← index.blade.php, show.blade.php
│   ├── routes/web.php
│   └── ...
├── img/                     ← favicons e imagens do projeto
├── tmp/                     ← arquivos de referência (ignorado pelo git, será excluído)
├── CLAUDE.md                ← guia para agentes de IA
├── SECURITY_GUIDELINES.md
└── version.md
```

## Posts

Por ora os posts estão definidos como array estático no `BlogController`. Para adicionar um novo post, edite o método `allPosts()` em `app/Http/Controllers/BlogController.php`.

No futuro, migrar para banco de dados com tabela `posts`.

## Versão

`version.md` na raiz registra a versão pública no formato `X.Y.Z`:

- **X** — versão estável (mudança manual)
- **Y** — mudança estrutural significativa
- **Z** — incremento a cada nova tela, tabela, ou mudança de layout
