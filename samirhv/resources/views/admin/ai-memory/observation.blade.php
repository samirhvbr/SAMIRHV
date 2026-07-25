@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Observação')

@section('topbar-actions')
    <a href="{{ route('admin.ai-memory.observations') }}" class="admin-btn admin-btn-sm"><i class="fa-solid fa-arrow-left"></i> Observações</a>
@endsection

@section('content')
<div class="aim">
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $T = \App\Services\AiMemory\AiMemoryTime::class;
            $imp = (int) $observation->importance;
            $impClass = $imp >= 8 ? 'aim-imp--high' : ($imp >= 5 ? 'aim-imp--mid' : '');
        @endphp

        <header class="aim-hero">
            <div>
                <h1>{{ $observation->title }}</h1>
                <div class="aim-hero__chips">
                    <span class="aim-chip">{{ $observation->kind }}</span>
                    @if($observation->agent_kind)<span class="aim-chip aim-chip--accent">{{ $observation->agent_kind }}</span>@endif
                    <span class="aim-imp {{ $impClass }}" title="Importância {{ $imp }} de 10">
                        <span class="aim-imp__track"><span class="aim-imp__fill" style="width:{{ min(100, $imp * 10) }}%"></span></span>
                        <span class="aim-imp__n">importância {{ $imp }}/10</span>
                    </span>
                </div>
            </div>
        </header>

        <dl class="aim-facts aim-facts--cols" style="margin-bottom:22px">
            <div><dt>Projeto</dt><dd>{{ $observation->project }}</dd></div>
            <div><dt>Registrada</dt><dd class="aim-mono">{{ $T::format($observation->created_at) }}</dd></div>
            <div>
                <dt>Sessão</dt>
                <dd>
                    @if($observation->session_hex)
                        <a href="{{ route('admin.ai-memory.sessions.show', $observation->session_hex) }}">abrir a sessão ↗</a>
                    @else
                        <span class="aim-mono">—</span>
                    @endif
                </dd>
            </div>
            <div><dt>Tipo</dt><dd class="aim-mono">{{ $observation->kind }}</dd></div>
        </dl>

        <section class="admin-card aim-card">
            <header class="aim-card__head">
                <div>
                    <h2>Fato registrado</h2>
                    <p class="aim-sub">texto como o agente gravou</p>
                </div>
            </header>

            @if(trim((string) $observation->body) === '')
                <p class="aim-empty">Esta observação não tem corpo — só o título acima.</p>
            @else
                <p class="aim-raw">{{ $observation->body }}</p>
            @endif
        </section>
    @endunless
</div>
@endsection
