@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Dashboard')

@section('content')
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php $n = fn ($v) => number_format((int) $v, 0, ',', '.'); @endphp

        {{-- Totais ao vivo (direto do ai-memory) --}}
        <div class="admin-stats-grid">
            <div class="admin-stat"><div class="label">Workspaces</div><div class="value">{{ $n($counts['workspaces']) }}</div></div>
            <div class="admin-stat"><div class="label">Projetos</div><div class="value">{{ $n($counts['projects']) }}</div></div>
            <div class="admin-stat"><div class="label">Páginas</div><div class="value">{{ $n($counts['pages']) }}</div></div>
            <div class="admin-stat"><div class="label">Sessões</div><div class="value">{{ $n($counts['sessions']) }}</div></div>
            <div class="admin-stat"><div class="label">Observações</div><div class="value">{{ $n($counts['observations']) }}</div></div>
            <div class="admin-stat"><div class="label">Embeddings</div><div class="value">{{ $n($counts['embeddings']) }}</div></div>
            <div class="admin-stat"><div class="label">Handoffs abertos</div><div class="value">{{ $n($counts['handoffs_open']) }}</div></div>
            <div class="admin-stat"><div class="label">Propostas pendentes</div><div class="value">{{ $n($counts['proposals_pending']) }}</div></div>
        </div>

        {{-- Evolução ao vivo (bucket UTC) --}}
        <div class="an-grid-2">
            <div class="admin-card">
                <h2>Observações por dia <span class="card-sub">({{ count($observationsByDay) }}d)</span></h2>
                @php $vals = array_values($observationsByDay); $max = $vals ? max(1, max($vals)) : 1; @endphp
                <div class="an-chart">
                    @foreach($observationsByDay as $day => $total)
                        <div class="an-bar" style="height:{{ max(2, round($total / $max * 100)) }}%" title="{{ $day }}: {{ $total }}"></div>
                    @endforeach
                </div>
                <div class="an-xlabels">
                    @foreach($observationsByDay as $day => $total)
                        <span>{{ \Illuminate\Support\Carbon::parse($day)->format('d/m') }}</span>
                    @endforeach
                </div>
            </div>

            <div class="admin-card">
                <h2>Sessões por dia <span class="card-sub">({{ count($sessionsByDay) }}d)</span></h2>
                @php $vals = array_values($sessionsByDay); $max = $vals ? max(1, max($vals)) : 1; @endphp
                <div class="an-chart">
                    @foreach($sessionsByDay as $day => $total)
                        <div class="an-bar" style="height:{{ max(2, round($total / $max * 100)) }}%" title="{{ $day }}: {{ $total }}"></div>
                    @endforeach
                </div>
                <div class="an-xlabels">
                    @foreach($sessionsByDay as $day => $total)
                        <span>{{ \Illuminate\Support\Carbon::parse($day)->format('d/m') }}</span>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Evolução histórica DURÁVEL (tabela própria — sobrevive a reset do ai-memory) --}}
        <div class="admin-card">
            <h2>Evolução histórica <span class="card-sub">— retratos diários (MySQL, sobrevivem a reset do ai-memory)</span></h2>
            @if($history->isEmpty())
                <p class="card-sub">
                    Ainda sem retratos. O job diário <code>aimemory:snapshot</code> ainda não rodou —
                    execute <code>php artisan aimemory:snapshot</code> para gravar o primeiro (e confira
                    o cron do Laravel no servidor).
                </p>
            @else
                @php $vals = $history->pluck('observations')->all(); $max = $vals ? max(1, max($vals)) : 1; @endphp
                <div class="an-chart">
                    @foreach($history as $snap)
                        <div class="an-bar" style="height:{{ max(2, round($snap->observations / $max * 100)) }}%" title="{{ $snap->captured_on->format('d/m/Y') }}: {{ $snap->observations }} observações · {{ $snap->pages }} páginas"></div>
                    @endforeach
                </div>
                <div class="an-xlabels">
                    @foreach($history as $snap)
                        <span>{{ $snap->captured_on->format('d/m') }}</span>
                    @endforeach
                </div>
                <p class="card-sub" style="margin-top:12px">{{ $history->count() }} retrato(s) · barras = nº de observações no dia do retrato.</p>
            @endif
        </div>
    @endunless
@endsection
