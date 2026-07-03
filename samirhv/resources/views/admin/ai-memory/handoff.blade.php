@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Handoff')

@section('topbar-actions')
    <a href="{{ route('admin.ai-memory.handoffs') }}" class="admin-btn admin-btn-sm"><i class="fa-solid fa-arrow-left"></i> Handoffs</a>
@endsection

@section('content')
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $T = \App\Services\AiMemory\AiMemoryTime::class;
            $stateBadge = ['open' => 'badge-warn', 'accepted' => 'badge-ok', 'expired' => 'badge-muted'];
            $decode = fn ($json) => is_array($d = json_decode((string) $json, true)) ? $d : [];
            $openQuestions = $decode($handoff->open_questions);
            $nextSteps = $decode($handoff->next_steps);
            $filesTouched = $decode($handoff->files_touched);
        @endphp

        <div class="admin-card">
            <h2>
                <span class="badge badge-accent">{{ $handoff->from_agent }}</span>
                <i class="fa-solid fa-arrow-right" style="font-size:.8rem;color:var(--dim)"></i>
                {{ $handoff->to_agent ? $handoff->to_agent : 'qualquer agente' }}
                <span class="badge {{ $stateBadge[$handoff->state] ?? 'badge-muted' }}">{{ $handoff->state }}</span>
            </h2>
            <ul class="an-list" style="max-width:640px">
                <li><span class="card-sub">Projeto</span><span>{{ $handoff->project }}</span></li>
                <li><span class="card-sub">Diretório</span><span><code>{{ $handoff->cwd ?? '—' }}</code></span></li>
                <li><span class="card-sub">Criado</span><span>{{ $T::format($handoff->created_at) }}</span></li>
                @if($handoff->accepted_by)
                    <li><span class="card-sub">Aceito por</span><span>{{ $handoff->accepted_by }} · {{ $T::format($handoff->accepted_at) }}</span></li>
                @endif
            </ul>
        </div>

        <div class="admin-card">
            <h2>Resumo</h2>
            <div style="white-space:pre-wrap;line-height:1.6;color:#dbe2ea">{{ $handoff->summary }}</div>
        </div>

        <div class="an-grid-3">
            <div class="admin-card">
                <h2>Perguntas em aberto <span class="card-sub">({{ count($openQuestions) }})</span></h2>
                @forelse($openQuestions as $q)
                    <div class="an-hbar-row"><span>{{ $q }}</span></div>
                @empty
                    <p class="card-sub">Nenhuma.</p>
                @endforelse
            </div>
            <div class="admin-card">
                <h2>Próximos passos <span class="card-sub">({{ count($nextSteps) }})</span></h2>
                @forelse($nextSteps as $s)
                    <div class="an-hbar-row"><span>{{ $s }}</span></div>
                @empty
                    <p class="card-sub">Nenhum.</p>
                @endforelse
            </div>
            <div class="admin-card">
                <h2>Arquivos tocados <span class="card-sub">({{ count($filesTouched) }})</span></h2>
                @forelse($filesTouched as $f)
                    <div class="an-hbar-row"><span><code>{{ $f }}</code></span></div>
                @empty
                    <p class="card-sub">Nenhum.</p>
                @endforelse
            </div>
        </div>
    @endunless
@endsection
