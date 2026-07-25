@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Handoff')

@section('topbar-actions')
    <a href="{{ route('admin.ai-memory.handoffs') }}" class="admin-btn admin-btn-sm"><i class="fa-solid fa-arrow-left"></i> Handoffs</a>
@endsection

@section('content')
<div class="aim">
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $T = \App\Services\AiMemory\AiMemoryTime::class;
            $stateChip = ['open' => 'aim-chip--warn', 'accepted' => 'aim-chip--ok', 'expired' => ''];
            $stateLabel = ['open' => 'aberto', 'accepted' => 'aceito', 'expired' => 'expirado'];
            $decode = fn ($json) => is_array($d = json_decode((string) $json, true)) ? $d : [];
            $openQuestions = $decode($handoff->open_questions);
            $nextSteps = $decode($handoff->next_steps);
            $filesTouched = $decode($handoff->files_touched);
        @endphp

        <header class="aim-hero">
            <div>
                <h1>{{ $handoff->from_agent }} → {{ $handoff->to_agent ?: 'qualquer agente' }}</h1>
                <div class="aim-hero__chips">
                    <span class="aim-chip {{ $stateChip[$handoff->state] ?? '' }}">{{ $stateLabel[$handoff->state] ?? $handoff->state }}</span>
                    <span class="aim-chip">{{ $handoff->project }}</span>
                    <span class="aim-chip">{{ $T::human($handoff->created_at) }}</span>
                </div>
                <code class="aim-hero__path">{{ $handoff->cwd ?? 'sem diretório registrado' }}</code>
            </div>
        </header>

        <dl class="aim-facts aim-facts--cols" style="margin-bottom:22px">
            <div><dt>Criado</dt><dd class="aim-mono">{{ $T::format($handoff->created_at) }}</dd></div>
            <div>
                <dt>Aceito</dt>
                <dd class="aim-mono">
                    @if($handoff->accepted_by)
                        {{ $handoff->accepted_by }} · {{ $T::format($handoff->accepted_at) }}
                    @else
                        ainda não
                    @endif
                </dd>
            </div>
        </dl>

        <section class="admin-card aim-card">
            <header class="aim-card__head">
                <div>
                    <h2>Resumo deixado para a próxima sessão</h2>
                    <p class="aim-sub">o bilhete que o agente escreveu ao sair</p>
                </div>
            </header>

            @if(trim((string) $handoff->summary) === '')
                <p class="aim-empty">Sem resumo — este handoff só trouxe as listas abaixo.</p>
            @else
                <p class="aim-raw">{{ $handoff->summary }}</p>
            @endif
        </section>

        <div class="aim-grid2 aim-grid2--even" style="grid-template-columns:repeat(auto-fit,minmax(280px,1fr))">
            <section class="admin-card aim-card">
                <header class="aim-card__head">
                    <div>
                        <h2>Perguntas em aberto</h2>
                        <p class="aim-sub">{{ count($openQuestions) }} {{ count($openQuestions) === 1 ? 'pergunta' : 'perguntas' }}</p>
                    </div>
                </header>
                @if(empty($openQuestions))
                    <p class="aim-empty">Nada pendente de decisão.</p>
                @else
                    <ul class="aim-items">
                        @foreach($openQuestions as $question)
                            <li><span>{{ is_string($question) ? $question : json_encode($question, JSON_UNESCAPED_UNICODE) }}</span></li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section class="admin-card aim-card">
                <header class="aim-card__head">
                    <div>
                        <h2>Próximos passos</h2>
                        <p class="aim-sub">{{ count($nextSteps) }} {{ count($nextSteps) === 1 ? 'passo' : 'passos' }}</p>
                    </div>
                </header>
                @if(empty($nextSteps))
                    <p class="aim-empty">Nenhum passo combinado.</p>
                @else
                    <ul class="aim-items">
                        @foreach($nextSteps as $step)
                            <li><span>{{ is_string($step) ? $step : json_encode($step, JSON_UNESCAPED_UNICODE) }}</span></li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section class="admin-card aim-card">
                <header class="aim-card__head">
                    <div>
                        <h2>Arquivos tocados</h2>
                        <p class="aim-sub">{{ count($filesTouched) }} {{ count($filesTouched) === 1 ? 'arquivo' : 'arquivos' }}</p>
                    </div>
                </header>
                @if(empty($filesTouched))
                    <p class="aim-empty">Nenhum arquivo registrado.</p>
                @else
                    <ul class="aim-items aim-items--files">
                        @foreach($filesTouched as $file)
                            <li><code>{{ is_string($file) ? $file : json_encode($file, JSON_UNESCAPED_UNICODE) }}</code></li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    @endunless
</div>
@endsection
