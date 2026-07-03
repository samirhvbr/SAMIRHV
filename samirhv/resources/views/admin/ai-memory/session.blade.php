@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Sessão')

@section('topbar-actions')
    <a href="{{ route('admin.ai-memory.sessions') }}" class="admin-btn admin-btn-sm"><i class="fa-solid fa-arrow-left"></i> Sessões</a>
@endsection

@push('styles')
<style>
    .tl{list-style:none;margin:0;padding:0}
    .tl li{position:relative;padding:0 0 16px 22px;border-left:2px solid var(--line)}
    .tl li:last-child{border-left-color:transparent}
    .tl li::before{content:'';position:absolute;left:-6px;top:3px;width:10px;height:10px;border-radius:50%;background:var(--accent);box-shadow:0 0 0 3px var(--accent-soft)}
    .tl .tl-time{font-family:'JetBrains Mono',monospace;font-size:.72rem;color:var(--dim)}
    .tl .tl-title{color:#e2e8f0;margin-top:2px}
</style>
@endpush

@section('content')
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $T = \App\Services\AiMemory\AiMemoryTime::class;
            $impBadge = fn ($i) => $i >= 8 ? 'badge-danger' : ($i >= 5 ? 'badge-warn' : 'badge-muted');
        @endphp

        <div class="admin-card">
            <h2><span class="badge badge-accent">{{ $session->agent_kind }}</span> {{ $session->project }}</h2>
            <ul class="an-list" style="max-width:640px">
                <li><span class="card-sub">Diretório</span><span><code>{{ $session->cwd ?? '—' }}</code></span></li>
                <li><span class="card-sub">Início</span><span>{{ $T::format($session->started_at) }}</span></li>
                <li><span class="card-sub">Fim</span><span>{{ $session->ended_at ? $T::format($session->ended_at) : 'em aberto' }}</span></li>
                <li><span class="card-sub">Duração</span><span>{{ $T::duration($session->started_at, $session->ended_at) }}</span></li>
                <li><span class="card-sub">Observações</span><span>{{ $session->obs_count }}</span></li>
                @if($session->summary_page_hex)
                    <li><span class="card-sub">Resumo</span><span><a href="{{ route('admin.ai-memory.pages.show', $session->summary_page_hex) }}">{{ $session->summary_title ?? 'ver página' }}</a></span></li>
                @endif
            </ul>
        </div>

        <div class="admin-card">
            <h2>Timeline de observações <span class="card-sub">({{ count($observations) }})</span></h2>
            @if(empty($observations))
                <p class="card-sub">Nenhuma observação nesta sessão.</p>
            @else
                <ul class="tl">
                    @foreach($observations as $o)
                        <li>
                            <div class="tl-time">{{ $T::format($o->created_at, 'd/m H:i:s') }}</div>
                            <div class="tl-title">
                                <span class="badge badge-muted">{{ $o->kind }}</span>
                                <span class="badge {{ $impBadge($o->importance) }}">i{{ $o->importance }}</span>
                                <a href="{{ route('admin.ai-memory.observations.show', $o->id_hex) }}">{{ $o->title }}</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endunless
@endsection
