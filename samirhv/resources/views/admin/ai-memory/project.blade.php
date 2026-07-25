@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Projeto')

@section('topbar-actions')
    <a href="{{ route('admin.ai-memory.projects') }}" class="admin-btn admin-btn-sm"><i class="fa-solid fa-arrow-left"></i> Projetos</a>
@endsection

@section('content')
<div class="aim">
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $T = \App\Services\AiMemory\AiMemoryTime::class;
            $n = fn ($v) => number_format((int) $v, 0, ',', '.');
            $tierChip = ['working' => 'aim-chip--warn', 'episodic' => 'aim-chip--accent', 'semantic' => 'aim-chip--ok'];
        @endphp

        <header class="aim-hero">
            <div>
                <h1>{{ $project->name }}</h1>
                <div class="aim-hero__chips">
                    <span class="aim-chip">workspace {{ $project->workspace }}</span>
                    <span class="aim-chip">criado em {{ $T::format($project->created_at, 'd/m/Y') }}</span>
                </div>
                <code class="aim-hero__path">{{ $project->repo_path ?? 'sem caminho de repositório' }}</code>
            </div>
        </header>

        <section class="aim-panel" aria-label="Totais do projeto">
            <div class="aim-panel__row aim-panel__row--3">
                <div class="aim-cell">
                    <p class="aim-label">Páginas</p>
                    <a class="aim-cell__link" href="{{ route('admin.ai-memory.pages', ['project' => $project->id_hex]) }}">
                        <p class="aim-value aim-value--md">{{ $n($project->pages) }}</p>
                    </a>
                </div>
                <div class="aim-cell">
                    <p class="aim-label">Sessões</p>
                    <a class="aim-cell__link" href="{{ route('admin.ai-memory.sessions', ['project' => $project->id_hex]) }}">
                        <p class="aim-value aim-value--md">{{ $n($project->sessions) }}</p>
                    </a>
                </div>
                <div class="aim-cell">
                    <p class="aim-label">Observações</p>
                    <a class="aim-cell__link" href="{{ route('admin.ai-memory.observations', ['project' => $project->id_hex]) }}">
                        <p class="aim-value aim-value--md">{{ $n($project->observations) }}</p>
                    </a>
                </div>
            </div>
        </section>

        <div class="aim-grid2 aim-grid2--even">
            <section class="admin-card aim-card">
                <header class="aim-card__head">
                    <div>
                        <h2>Páginas recentes</h2>
                        <p class="aim-sub">conhecimento consolidado deste projeto</p>
                    </div>
                    <a href="{{ route('admin.ai-memory.pages', ['project' => $project->id_hex]) }}" class="admin-btn admin-btn-sm">ver todas</a>
                </header>

                @forelse($recentPages as $pg)
                    <div class="aim-tl__row" style="justify-content:space-between;margin:0;padding:9px 0;border-bottom:1px solid var(--hair)">
                        <a href="{{ route('admin.ai-memory.pages.show', $pg->id_hex) }}" title="{{ $pg->path }}" style="min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $pg->title }}</a>
                        <span class="aim-chip {{ $tierChip[$pg->tier] ?? '' }}">{{ $pg->tier }}</span>
                    </div>
                @empty
                    <p class="aim-empty">Nenhuma página consolidada neste projeto ainda.</p>
                @endforelse
            </section>

            <section class="admin-card aim-card">
                <header class="aim-card__head">
                    <div>
                        <h2>Sessões recentes</h2>
                        <p class="aim-sub">quem trabalhou aqui, e quanto rendeu</p>
                    </div>
                    <a href="{{ route('admin.ai-memory.sessions', ['project' => $project->id_hex]) }}" class="admin-btn admin-btn-sm">ver todas</a>
                </header>

                @forelse($recentSessions as $s)
                    <div class="aim-tl__row" style="justify-content:space-between;margin:0;padding:9px 0;border-bottom:1px solid var(--hair)">
                        <a href="{{ route('admin.ai-memory.sessions.show', $s->id_hex) }}">
                            <span class="aim-chip aim-chip--accent">{{ $s->agent_kind }}</span>
                            <span class="aim-when" style="margin-left:8px">{{ $T::format($s->started_at, 'd/m H:i') }}</span>
                        </a>
                        <span class="aim-mono">{{ $n($s->obs_count) }} obs</span>
                    </div>
                @empty
                    <p class="aim-empty">Nenhuma sessão registrada neste projeto ainda.</p>
                @endforelse
            </section>
        </div>
    @endunless
</div>
@endsection
