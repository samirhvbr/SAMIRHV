{{-- Sub-navegação do módulo AI-MEMORY. Usada por TODAS as abas — o estilo vem
     junto (uma vez só) para o módulo ter o mesmo vocabulário em toda tela. --}}
@include('admin.ai-memory._styles')

@once
@push('styles')
<style>
    .aimnav{display:flex;gap:2px;flex-wrap:wrap;border-bottom:1px solid var(--line);margin-bottom:24px}
    .aimnav a{
        position:relative;padding:9px 14px 12px;font-size:.85rem;font-weight:500;color:var(--muted);
        text-decoration:none;border-radius:var(--radius-sm) var(--radius-sm) 0 0;
        transition:color .15s ease,background-color .15s ease;
    }
    .aimnav a:hover{color:var(--txt);background:var(--accent-soft)}
    .aimnav a[aria-current]{color:#c7d2fe;font-weight:600}
    .aimnav a[aria-current]::after{
        content:'';position:absolute;left:10px;right:10px;bottom:-1px;height:2px;
        background:var(--accent);border-radius:2px 2px 0 0;
    }
    .aimnav a:focus-visible{outline:2px solid var(--accent);outline-offset:-2px}
    @media(max-width:640px){
        /* faixa rolável em vez de quebrar em três linhas */
        .aimnav{flex-wrap:nowrap;overflow-x:auto;scrollbar-width:none;-ms-overflow-style:none}
        .aimnav::-webkit-scrollbar{display:none}
        .aimnav a{white-space:nowrap}
    }
</style>
@endpush
@endonce

@php
    $r = (string) request()->route()?->getName();
    $tabs = [
        'admin.ai-memory.dashboard' => 'Dashboard',
        'admin.ai-memory.projects' => 'Projetos',
        'admin.ai-memory.workspaces' => 'Workspaces',
        'admin.ai-memory.pages' => 'Páginas',
        'admin.ai-memory.sessions' => 'Sessões',
        'admin.ai-memory.observations' => 'Observações',
        'admin.ai-memory.handoffs' => 'Handoffs',
        'admin.ai-memory.search' => 'Busca',
    ];
@endphp

<nav class="aimnav" aria-label="Seções do AI-MEMORY">
    @foreach($tabs as $name => $label)
        {{-- a tela de detalhe (…/{hexId}) mantém acesa a aba da sua listagem --}}
        <a href="{{ route($name) }}" @if($r === $name || str_starts_with($r, $name.'.')) aria-current="page" @endif>{{ $label }}</a>
    @endforeach
</nav>
