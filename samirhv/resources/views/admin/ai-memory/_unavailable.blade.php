{{-- Aviso de degradação: renderizado por TODA aba quando o SQLite do ai-memory
     não está acessível. É a explicação, na própria UI, de "por que parou". --}}
<div class="admin-card" style="border-color:rgba(245,158,11,.35);background:rgba(245,158,11,.05)">
    <h2 style="color:#fcd34d;display:flex;align-items:center;gap:10px">
        <i class="fa-solid fa-triangle-exclamation"></i> AI-MEMORY indisponível neste servidor
    </h2>

    <p style="color:#e2e8f0;line-height:1.6">
        Esta tela consulta, <b>somente leitura</b>, o banco <b>SQLite do ai-memory</b> — a memória
        de longo prazo dos agentes de código. Esse arquivo pertence ao container Docker do
        <code>ai-memory</code> (volume <code>{{ $dockerVolume }}</code>) e é lido
        <b>diretamente do sistema de arquivos deste servidor</b>. No momento ele
        <b>não está acessível</b>, então não há o que mostrar.
    </p>

    <p class="card-sub" style="margin:14px 0 6px">Caminho configurado (<code>AI_MEMORY_SQLITE_PATH</code>):</p>
    <div style="overflow-x:auto"><code style="color:#a5b4fc">{{ $aimemoryPath ?: '(vazio)' }}</code></div>

    <p class="card-sub" style="margin:18px 0 6px">Causas prováveis:</p>
    <ul class="an-list" style="max-width:760px">
        <li><span>O app <b>saiu do servidor</b> onde o ai-memory roda — o arquivo só existe naquele host.</span></li>
        <li><span>O <b>volume/container</b> do ai-memory mudou de nome ou caminho.</span></li>
        <li><span>O usuário do PHP-FPM (<code>www-data</code>) <b>não tem permissão</b> de leitura sobre <code>memory.sqlite</code> + <code>-wal</code> + <code>-shm</code>.</span></li>
        <li><span>A extensão <code>pdo_sqlite</code> do PHP não está instalada neste ambiente.</span></li>
    </ul>

    <p class="card-sub" style="margin-top:16px">
        Isto <b>não é um bug</b> do app — é o acoplamento esperado ao host. Detalhes de diagnóstico e
        permissões em <code>docs/AI-MEMORY.md</code>.
    </p>
</div>
