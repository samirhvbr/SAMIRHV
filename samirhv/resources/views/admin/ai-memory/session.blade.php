@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Sessão')

@section('topbar-actions')
    <a href="{{ route('admin.ai-memory.sessions') }}" class="admin-btn admin-btn-sm"><i class="fa-solid fa-arrow-left"></i> Sessões</a>
@endsection

@section('content')
<div class="aim">
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $T = \App\Services\AiMemory\AiMemoryTime::class;
            $impClass = fn ($i) => $i >= 8 ? 'aim-imp--high' : ($i >= 5 ? 'aim-imp--mid' : '');
            $live = ! $session->ended_at;
        @endphp

        <header class="aim-hero">
            <div>
                <h1>{{ $session->project }}</h1>
                <div class="aim-hero__chips">
                    <span class="aim-chip aim-chip--accent">{{ $session->agent_kind }}</span>
                    @if($live)
                        <span class="aim-chip aim-chip--live">em aberto</span>
                    @else
                        <span class="aim-chip">{{ $T::duration($session->started_at, $session->ended_at) }}</span>
                    @endif
                    <span class="aim-chip">{{ number_format((int) $session->obs_count, 0, ',', '.') }} observações</span>
                </div>
                <code class="aim-hero__path">{{ $session->cwd ?? 'sem diretório registrado' }}</code>
            </div>
        </header>

        <dl class="aim-facts aim-facts--cols" style="margin-bottom:22px">
            <div><dt>Início</dt><dd class="aim-mono">{{ $T::format($session->started_at) }}</dd></div>
            <div><dt>Fim</dt><dd class="aim-mono">{{ $session->ended_at ? $T::format($session->ended_at) : 'em aberto' }}</dd></div>
            <div><dt>Duração</dt><dd class="aim-mono">{{ $T::duration($session->started_at, $session->ended_at) }}</dd></div>
            <div>
                <dt>Resumo</dt>
                <dd>
                    @if($session->summary_page_hex)
                        <a href="{{ route('admin.ai-memory.pages.show', $session->summary_page_hex) }}">{{ $session->summary_title ?? 'ver página' }} ↗</a>
                    @else
                        <span class="aim-mono">ainda não consolidado</span>
                    @endif
                </dd>
            </div>
        </dl>

        <section class="admin-card aim-card">
            <header class="aim-card__head">
                <div>
                    <h2>O que a sessão aprendeu</h2>
                    <p class="aim-sub">{{ count($observations) }} {{ count($observations) === 1 ? 'observação' : 'observações' }} · em ordem cronológica</p>
                </div>
            </header>

            @if(empty($observations))
                <div class="aim-blank">
                    <i class="fa-solid fa-lightbulb"></i>
                    <p>Nenhuma observação nesta sessão.</p>
                    <p>{{ $live ? 'A sessão ainda está aberta — pode ser que os fatos venham depois.' : 'A sessão terminou sem registrar fatos: trabalho curto, ou hooks desligados.' }}</p>
                </div>
            @else
                <ul class="aim-tl">
                    @foreach($observations as $o)
                        <li>
                            <div class="aim-tl__time">{{ $T::format($o->created_at, 'd/m H:i:s') }}</div>
                            <div class="aim-tl__row">
                                <span class="aim-chip">{{ $o->kind }}</span>
                                <span class="aim-imp {{ $impClass($o->importance) }}" title="Importância {{ $o->importance }} de 10">
                                    <span class="aim-imp__track"><span class="aim-imp__fill" style="width:{{ min(100, (int) $o->importance * 10) }}%"></span></span>
                                    <span class="aim-imp__n">{{ $o->importance }}</span>
                                </span>
                                <a href="{{ route('admin.ai-memory.observations.show', $o->id_hex) }}">{{ $o->title }}</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </section>
    @endunless
</div>
@endsection
