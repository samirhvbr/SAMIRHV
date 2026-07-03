@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Observação')

@section('topbar-actions')
    <a href="{{ route('admin.ai-memory.observations') }}" class="admin-btn admin-btn-sm"><i class="fa-solid fa-arrow-left"></i> Observações</a>
@endsection

@section('content')
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $T = \App\Services\AiMemory\AiMemoryTime::class;
            $impBadge = $observation->importance >= 8 ? 'badge-danger' : ($observation->importance >= 5 ? 'badge-warn' : 'badge-muted');
        @endphp

        <div class="admin-card">
            <h2 style="margin-bottom:10px">{{ $observation->title }}</h2>
            <div style="margin-bottom:18px">
                <span class="badge badge-muted">{{ $observation->kind }}</span>
                <span class="badge {{ $impBadge }}">importância {{ $observation->importance }}</span>
                @if($observation->agent_kind)<span class="badge badge-accent">{{ $observation->agent_kind }}</span>@endif
            </div>

            <ul class="an-list" style="max-width:640px;margin-bottom:18px">
                <li><span class="card-sub">Projeto</span><span>{{ $observation->project }}</span></li>
                <li><span class="card-sub">Data</span><span>{{ $T::format($observation->created_at) }}</span></li>
                @if($observation->session_hex)
                    <li><span class="card-sub">Sessão</span><span><a href="{{ route('admin.ai-memory.sessions.show', $observation->session_hex) }}">abrir sessão</a></span></li>
                @endif
            </ul>

            <p class="card-sub" style="margin-bottom:6px">Corpo</p>
            <div style="white-space:pre-wrap;line-height:1.6;color:#dbe2ea;background:var(--panel-2);border:1px solid var(--line);border-radius:9px;padding:14px 16px">{{ $observation->body }}</div>
        </div>
    @endunless
@endsection
