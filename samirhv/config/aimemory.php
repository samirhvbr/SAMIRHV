<?php

/*
|--------------------------------------------------------------------------
| Módulo AI-MEMORY (admin) — configuração e acoplamento de produção
|--------------------------------------------------------------------------
|
| O admin do Samirhv tem uma tela "AI-MEMORY" que CONSULTA (somente leitura)
| o banco do produto `ai-memory` (github.com/akitaonrails/ai-memory) — a
| memória de longo prazo dos agentes de código (Claude Code, Codex, etc.).
|
| >>> LEIA ISTO ANTES DE MOVER O APP DE SERVIDOR <<<
|
| O ai-memory roda como CONTAINER DOCKER no MESMO servidor de produção do
| Samirhv. Ele guarda seu índice num arquivo SQLite (modo WAL) dentro do
| volume Docker `ai-memory-data`, no caminho interno `/data/db/memory.sqlite`.
| No host, esse arquivo normalmente aparece em:
|
|     /var/lib/docker/volumes/ai-memory-data/_data/db/memory.sqlite
|
| A tela do admin abre esse arquivo DIRETO do filesystem, como um segundo
| leitor read-only. Por isso o módulo é intrinsecamente ACOPLADO AO HOST:
| se o app Samirhv for movido para outra máquina (ou o volume/container
| mudar de nome/caminho, ou o usuário do PHP-FPM perder permissão de leitura
| sobre o arquivo e seus `-wal`/`-shm`), a tela deixa de retornar dados e
| passa a exibir o aviso de indisponibilidade. NÃO é bug: é o acoplamento.
| A explicação completa (permissões, WAL, roteiro Fase 2) está em
| docs/AI-MEMORY.md.
|
*/

return [

    // Caminho absoluto do memory.sqlite NO HOST. Configure em produção via
    // AI_MEMORY_SQLITE_PATH no .env. Vazio/inexistente => módulo degrada.
    'path' => env('AI_MEMORY_SQLITE_PATH', '/var/lib/docker/volumes/ai-memory-data/_data/db/memory.sqlite'),

    // Nome da conexão declarada em config/database.php (read-only).
    'connection' => 'aimemory',

    // Fuso para exibir os timestamps (o ai-memory grava em UTC, microssegundos).
    'timezone' => env('AI_MEMORY_TIMEZONE', 'America/Sao_Paulo'),

    // Volume Docker de origem — usado só para o texto explicativo da UI/doc.
    'docker_volume' => env('AI_MEMORY_DOCKER_VOLUME', 'ai-memory-data'),

    // Quantos dias de evolução mostrar nos gráficos do Dashboard.
    'chart_days' => 30,

    // Teto de linhas por página nas listagens (sessões/observações/páginas).
    'per_page' => 50,
];
