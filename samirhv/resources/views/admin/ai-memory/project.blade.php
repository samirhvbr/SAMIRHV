@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Projeto')

@section('topbar-actions')
    <a href="{{ route('admin.ai-memory.projects') }}" class="admin-btn admin-btn-sm"><i class="fa-solid fa-arrow-left"></i> Projetos</a>
@endsection

@section('content')
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php $T = \App\Services\AiMemory\AiMemoryTime::class; @endphp

        <div class="admin-card">
            <h2>{{ $project->name }} <span class="card-sub">— workspace {{ $project->workspace }}</span></h2>
            <p class="card-sub" style="margin:0 0 4px">Repositório</p>
            <div style="overflow-x:auto"><code style="color:#a5b4fc">{{ $project->repo_path ?? '—' }}</code></div>
            <p class="card-sub" style="margin-top:12px">Criado em {{ $T::format($project->created_at) }}</p>
        </div>

        <div class="admin-stats-grid" style="grid-template-columns:repeat(3,1fr)">
            <div class="admin-stat"><div class="label">Páginas</div><div class="value">{{ $project->pages }}</div></div>
            <div class="admin-stat"><div class="label">Sessões</div><div class="value">{{ $project->sessions }}</div></div>
            <div class="admin-stat"><div class="label">Observações</div><div class="value">{{ $project->observations }}</div></div>
        </div>

        <div class="tabs">
            <a href="{{ route('admin.ai-memory.pages', ['project' => $project->id_hex]) }}" class="admin-btn admin-btn-sm">Ver todas as páginas</a>
            <a href="{{ route('admin.ai-memory.sessions', ['project' => $project->id_hex]) }}" class="admin-btn admin-btn-sm">Ver todas as sessões</a>
            <a href="{{ route('admin.ai-memory.observations', ['project' => $project->id_hex]) }}" class="admin-btn admin-btn-sm">Ver observações</a>
        </div>

        <div class="an-grid-2">
            <div class="admin-card">
                <h2>Páginas recentes</h2>
                @forelse($recentPages as $pg)
                    <div class="an-hbar-row" style="justify-content:space-between">
                        <a href="{{ route('admin.ai-memory.pages.show', $pg->id_hex) }}" style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $pg->path }}">{{ $pg->title }}</a>
                        <span class="badge badge-muted">{{ $pg->tier }}</span>
                    </div>
                @empty
                    <p class="card-sub">Sem páginas.</p>
                @endforelse
            </div>

            <div class="admin-card">
                <h2>Sessões recentes</h2>
                @forelse($recentSessions as $s)
                    <div class="an-hbar-row" style="justify-content:space-between">
                        <a href="{{ route('admin.ai-memory.sessions.show', $s->id_hex) }}" style="flex:1"><span class="badge badge-accent">{{ $s->agent_kind }}</span> <span class="card-sub">{{ $T::format($s->started_at, 'd/m H:i') }}</span></a>
                        <span class="card-sub">{{ $s->obs_count }} obs</span>
                    </div>
                @empty
                    <p class="card-sub">Sem sessões.</p>
                @endforelse
            </div>
        </div>
    @endunless
@endsection
