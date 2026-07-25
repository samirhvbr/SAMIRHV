@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Dashboard')

@push('styles')
<style>
/* ════════ AI-MEMORY · Dashboard ════════
   Escopo em .aim para não vazar para as outras telas do admin.
   Duas séries (observações/sessões) com hues checados para daltonismo
   sobre o fundo escuro; o resto é o mesmo vocabulário do admin.        */
.aim{
    --obs:#6366f1;   --obs-hi:#818cf8;   /* série 1 — observações */
    --ses:#0d9488;   --ses-hi:#2dd4bf;   /* série 2 — sessões     */
    --mono:ui-monospace,SFMono-Regular,'JetBrains Mono',Menlo,monospace;
    --hair:rgba(148,163,184,.12);
    --grid:rgba(148,163,184,.10);
}
.aim .sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0 0 0 0);white-space:nowrap;border:0}

/* ── painel de instrumentos (totais) ──
   Um bloco só, dividido por fios de 1px (o "vão" do grid mostrando o fundo),
   em vez de oito cartões iguais. */
.aim-panel{display:grid;gap:1px;background:var(--hair);border:1px solid var(--line);border-radius:var(--radius-lg);overflow:hidden;margin-bottom:20px}
.aim-panel__row{display:grid;gap:1px;background:var(--hair)}
.aim-panel__row--lede{grid-template-columns:1.5fr 1fr 1fr}
.aim-panel__row--facts{grid-template-columns:repeat(5,1fr)}
.aim-cell{background:var(--panel);padding:20px 22px;min-width:0}
.aim-cell--lede{padding:22px}
.aim-cell__top{display:flex;align-items:flex-start;justify-content:space-between;gap:16px}
.aim-cell__link{display:block;text-decoration:none;border-radius:var(--radius-sm)}
.aim-cell__link:hover .aim-value{color:#c7d2fe}
.aim-cell__link:focus-visible{outline:2px solid var(--accent);outline-offset:4px}
.aim-label{font-size:.7rem;text-transform:uppercase;letter-spacing:.09em;color:var(--muted);font-weight:600;margin:0}
.aim-value{color:#f8fafc;font-weight:650;letter-spacing:-.02em;line-height:1;margin:10px 0 0;transition:color .15s ease}
.aim-value--hero{font-size:3.1rem}
.aim-value--md{font-size:1.85rem}
.aim-value--sm{font-size:1.15rem;font-weight:600;margin-top:7px}
.aim-delta{margin:12px 0 0;font-size:.78rem;color:var(--muted);line-height:1.5}
.aim-delta b{color:#cbd5e1;font-weight:600;font-variant-numeric:tabular-nums}
.aim-delta i{font-style:normal;font-size:.68rem;margin-right:2px}
.aim-delta--up i{color:#4ade80}
.aim-delta--down i{color:#f87171}
.aim-delta--flat i{color:var(--muted)}
.aim-hint{margin:8px 0 0;font-size:.72rem;color:var(--muted);line-height:1.45}
.aim-fact{display:inline-flex;align-items:center;gap:8px;text-decoration:none}
.aim-fact:hover .aim-value{color:#c7d2fe}
.aim-fact:focus-visible{outline:2px solid var(--accent);outline-offset:4px;border-radius:var(--radius-sm)}
.aim-dot{width:7px;height:7px;border-radius:50%;flex-shrink:0}
.aim-dot--warn{background:var(--warn);box-shadow:0 0 0 3px rgba(245,158,11,.14)}
.aim-dot--idle{background:#334155}

/* sparkline dos retratos: linha discreta + ponto no valor atual */
.aim-spark{position:relative;width:106px;height:34px;flex-shrink:0}
.aim-spark--lede{width:148px;height:46px}
.aim-spark svg{display:block;width:100%;height:100%;overflow:visible}
.aim-spark .l{fill:none;stroke:rgba(129,140,248,.55);stroke-width:1.5;stroke-linejoin:round;stroke-linecap:round;vector-effect:non-scaling-stroke}
.aim-spark__dot{position:absolute;left:100%;width:5px;height:5px;border-radius:50%;background:var(--obs-hi);transform:translate(-50%,-50%);box-shadow:0 0 0 2px var(--panel)}
.aim-spark__cap{position:absolute;left:0;bottom:-15px;font-size:.62rem;color:var(--muted);font-family:var(--mono);white-space:nowrap}

.aim-source{display:flex;flex-wrap:wrap;align-items:center;gap:8px 16px;margin:0 0 22px;padding:0 2px;font-size:.72rem;color:var(--muted);font-family:var(--mono)}
.aim-source b{color:var(--muted);font-weight:500}

/* ── cards ── */
.aim-card{padding:22px 24px}
.aim-card__head{display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:12px 16px;margin-bottom:18px}
.aim-card__head > * + *{margin-left:auto}
.aim-card__head h2{margin:0;font-size:1.02rem;font-weight:700;color:#f1f5f9}
.aim-sub{margin:5px 0 0;font-size:.75rem;color:var(--muted);font-family:var(--mono)}
.aim-ghost{background:transparent;border:1px solid var(--line);color:var(--muted);border-radius:var(--radius-sm);padding:6px 12px;font:inherit;font-size:.76rem;cursor:pointer;transition:border-color .15s ease,color .15s ease}
.aim-ghost:hover{border-color:var(--line-hover);color:var(--txt)}
.aim-ghost[aria-pressed=true]{border-color:var(--accent);background:var(--accent-soft);color:#c7d2fe}
.aim-ghost:focus-visible{outline:2px solid var(--accent);outline-offset:2px}

/* ── legenda de série (é também o rótulo direto: total, média e pico) ── */
.aim-serie{display:flex;align-items:baseline;flex-wrap:wrap;gap:6px 10px;font-size:.78rem;color:var(--muted);margin:0 0 8px}
.aim-serie--stack{position:relative;z-index:2;background:var(--panel);margin:22px 0 2px;padding-top:2px}
.aim-serie__name{display:inline-flex;align-items:center;gap:8px;color:#e2e8f0;font-weight:600}
.aim-key{width:14px;height:3px;border-radius:2px;flex-shrink:0}
.aim-key--obs{background:var(--obs)}
.aim-key--ses{background:var(--ses)}
.aim-serie b{color:#cbd5e1;font-weight:600;font-family:var(--mono);font-size:.76rem}

/* ── atividade: duas faixas de barras com o MESMO eixo de dias ── */
.aim-plot{position:relative;outline:none}
.aim-plot:focus-visible{box-shadow:0 0 0 2px var(--accent);border-radius:6px}
.aim-row{position:relative;display:flex;align-items:flex-end;gap:4px;margin-top:20px}
.aim-row--obs{height:150px}
.aim-row--ses{height:74px;margin-top:16px}
.aim-gridline{position:absolute;left:0;right:0;height:1px;background:var(--grid);pointer-events:none}
.aim-ymax{position:absolute;left:0;bottom:100%;padding-bottom:5px;font-size:.65rem;color:var(--muted);font-family:var(--mono);line-height:1;pointer-events:none}
.aim-col{flex:1 1 0;min-width:0;height:100%;display:flex;align-items:flex-end;justify-content:center;cursor:crosshair}
.aim-bar{display:block;width:100%;max-width:18px;border-radius:3px 3px 0 0;transition:opacity .15s ease}
.aim-row--obs .aim-bar{background:linear-gradient(180deg,var(--obs),rgba(99,102,241,.42))}
.aim-row--ses .aim-bar{background:linear-gradient(180deg,var(--ses),rgba(13,148,136,.42))}
.aim-row--obs .aim-bar--peak{background:linear-gradient(180deg,var(--obs-hi),rgba(129,140,248,.5))}
.aim-row--ses .aim-bar--peak{background:linear-gradient(180deg,var(--ses-hi),rgba(45,212,191,.5))}
.aim-bar--zero{background:rgba(148,163,184,.18) !important;height:2px}
.aim-plot.is-hovering .aim-bar{opacity:.45}
.aim-col.is-active .aim-bar{opacity:1}
.aim-cross{position:absolute;top:0;width:1px;background:rgba(226,232,240,.22);pointer-events:none;opacity:0;transition:opacity .12s ease}
.aim-plot.is-hovering .aim-cross{opacity:1}
.aim-axis{display:flex;gap:4px;margin-top:9px}
.aim-axis span{flex:1 1 0;min-width:0;text-align:center;font-size:.62rem;color:var(--muted);font-family:var(--mono);white-space:nowrap}
.aim-axis span.is-today{color:#a5b4fc}
.aim-tip{position:absolute;z-index:20;min-width:150px;background:#161622;border:1px solid rgba(148,163,184,.22);border-radius:var(--radius-sm);padding:9px 11px;pointer-events:none;opacity:0;transition:opacity .12s ease;box-shadow:0 10px 28px rgba(0,0,0,.45)}
.aim-tip.is-on{opacity:1}
.aim-tip__day{font-size:.7rem;color:var(--muted);font-family:var(--mono);margin:0 0 6px}
.aim-tip__row{display:flex;align-items:center;gap:8px;font-size:.78rem;color:var(--muted);margin:3px 0 0}
.aim-tip__row b{color:#f1f5f9;font-weight:650;font-family:var(--mono);margin-left:auto;padding-left:12px}
.aim-tablewrap{margin-top:18px;max-height:280px;overflow:auto;border:1px solid var(--line);border-radius:var(--radius-sm)}
.aim-tablewrap[hidden]{display:none}
.aim-tablewrap th{position:sticky;top:0;background:#14141f;z-index:1}
.aim-tablewrap td{font-family:var(--mono);font-size:.78rem}

/* ── histórico durável (área) ── */
.aim-grid2{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(0,1fr);gap:20px;align-items:stretch}
.aim-grid2 > .admin-card{margin-bottom:0}
.aim-card--tall{display:flex;flex-direction:column}
.aim-card--tall .aim-area{flex:1;min-height:200px}
.aim-seg{display:inline-flex;gap:2px;background:var(--panel-2);border:1px solid var(--line);border-radius:var(--radius-sm);padding:3px}
.aim-seg button{background:transparent;border:none;color:var(--muted);font:inherit;font-size:.75rem;padding:5px 11px;border-radius:5px;cursor:pointer;transition:background-color .15s ease,color .15s ease}
.aim-seg button:hover{color:var(--txt)}
.aim-seg button[aria-pressed=true]{background:var(--accent-soft-2);color:#c7d2fe;font-weight:600}
.aim-seg button:focus-visible{outline:2px solid var(--accent);outline-offset:1px}
.aim-area{position:relative;height:220px;margin-top:22px}
.aim-area svg{display:block;width:100%;height:100%}
.aim-area .a{fill:url(#aimFade)}
.aim-area .l{fill:none;stroke:var(--obs-hi);stroke-width:2;stroke-linejoin:round;stroke-linecap:round;vector-effect:non-scaling-stroke}
.aim-area__grid{position:absolute;inset:0;pointer-events:none}
.aim-area__grid span{position:absolute;left:0;right:0;height:1px;background:var(--grid)}
.aim-area__grid i{position:absolute;left:0;font-style:normal;font-size:.65rem;color:var(--muted);font-family:var(--mono);line-height:1;padding-top:5px}
.aim-area__grid i.is-zero{padding:0 0 3px}
.aim-area__dot{position:absolute;width:9px;height:9px;border-radius:50%;background:var(--obs-hi);box-shadow:0 0 0 2px var(--panel);transform:translate(-50%,-50%);pointer-events:none}
.aim-area__end{position:absolute;transform:translate(-50%,-160%);font-size:.7rem;font-family:var(--mono);color:#c7d2fe;white-space:nowrap;pointer-events:none}
.aim-area__hit{position:absolute;inset:0;cursor:crosshair;outline:none}
.aim-area__hit:focus-visible{box-shadow:0 0 0 2px var(--accent);border-radius:6px}
.aim-area__foot{margin:14px 0 0}

/* ── ranking de projetos ── */
.aim-rank{list-style:none;margin:0;padding:0}
.aim-rank li + li{margin-top:2px}
.aim-rank a{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:4px 12px;padding:9px 10px;border-radius:var(--radius-sm);text-decoration:none;transition:background-color .15s ease}
.aim-rank a:hover{background:var(--accent-soft)}
.aim-rank a:focus-visible{outline:2px solid var(--accent);outline-offset:-2px}
.aim-rank__name{color:#e2e8f0;font-weight:600;font-size:.85rem;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.aim-rank__n{font-family:var(--mono);font-size:.78rem;color:#c7d2fe;font-variant-numeric:tabular-nums}
.aim-rank__track{grid-column:1/-1;height:4px;border-radius:2px;background:rgba(148,163,184,.10);overflow:hidden}
.aim-rank__fill{display:block;height:100%;border-radius:2px;background:var(--obs)}
.aim-rank__meta{grid-column:1/-1;font-size:.7rem;color:var(--muted);font-family:var(--mono);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.aim-more{display:inline-block;margin-top:14px;font-size:.78rem;color:var(--muted);text-decoration:none}
.aim-more:hover{color:#c7d2fe}
.aim-empty{color:var(--muted);font-size:.85rem;line-height:1.65;margin:0}
.aim-empty code{font-family:var(--mono);color:#a5b4fc;font-size:.8rem}

@media(max-width:1000px){
    .aim-grid2{grid-template-columns:1fr}
    .aim-panel__row--lede{grid-template-columns:1fr 1fr}
    .aim-panel__row--lede > :first-child{grid-column:1/-1}
    .aim-panel__row--facts{grid-template-columns:repeat(3,1fr)}
    .aim-panel__row--facts > :last-child{grid-column:span 2}
}
@media(max-width:640px){
    .aim-panel__row--lede,.aim-panel__row--facts{grid-template-columns:1fr 1fr}
    .aim-panel__row--lede > :first-child,
    .aim-panel__row--facts > :last-child{grid-column:1/-1}
    .aim-cell,.aim-cell--lede{padding:16px}
    .aim-value--hero{font-size:2.4rem}
    .aim-value--md{font-size:1.6rem}
    .aim-row--obs{height:120px}
    .aim-row--ses{height:60px}
    .aim-row,.aim-axis{gap:2px}
    .aim-axis span.is-dense{display:none}
    .aim-spark{display:none}
    .aim-card{padding:18px 16px}
    .aim-tip{min-width:0}
}
@media(max-width:400px){
    .aim-panel__row--lede,.aim-panel__row--facts{grid-template-columns:1fr}
}
@media(prefers-reduced-motion:reduce){
    .aim-bar,.aim-cross,.aim-tip,.aim-seg button,.aim-ghost,.aim-rank a,.aim-value{transition:none}
}
</style>
@endpush

@section('content')
<div class="aim">
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $n = fn ($v) => number_format((int) $v, 0, ',', '.');
            $T = \App\Services\AiMemory\AiMemoryTime::class;

            $obs = $summary->series($observationsByDay);
            $ses = $summary->series($sessionsByDay);
            $days = array_keys($observationsByDay);
            $labelEvery = 5;
            $fmtDay = fn ($ymd) => \Illuminate\Support\Carbon::parse($ymd)->format('d/m');

            $deltas = [
                'observations' => $summary->delta($history, 'observations', (int) $counts['observations']),
                'pages' => $summary->delta($history, 'pages', (int) $counts['pages']),
                'sessions' => $summary->delta($history, 'sessions', (int) $counts['sessions']),
            ];

            $histMetrics = ['observations' => 'Observações', 'pages' => 'Páginas', 'sessions' => 'Sessões'];
            $histData = $summary->historySeries($history, array_keys($histMetrics));
            $histDates = $history->map(fn ($s) => $s->captured_on->format('d/m/Y'))->values()->all();
            $histTop = $summary->niceMax(max($histData['observations'] ?: [0]));
            $histPath = $summary->areaPath($histData['observations'], $histTop);

            $sparks = [
                'observations' => $summary->sparkline($histData['observations']),
                'pages' => $summary->sparkline($histData['pages']),
                'sessions' => $summary->sparkline($histData['sessions']),
            ];
            $hasHistory = $history->count() > 1;
        @endphp

        {{-- ── Totais ao vivo: um painel de instrumentos, não oito cartões iguais ── --}}
        <section class="aim-panel" aria-label="Totais na memória">
            <div class="aim-panel__row aim-panel__row--lede">
                @php
                    $lede = [
                        ['key' => 'observations', 'label' => 'Observações', 'count' => $counts['observations'], 'route' => route('admin.ai-memory.observations'), 'hero' => true],
                        ['key' => 'pages', 'label' => 'Páginas', 'count' => $counts['pages'], 'route' => route('admin.ai-memory.pages'), 'hero' => false],
                        ['key' => 'sessions', 'label' => 'Sessões', 'count' => $counts['sessions'], 'route' => route('admin.ai-memory.sessions'), 'hero' => false],
                    ];
                @endphp
                @foreach($lede as $cell)
                    @php $delta = $deltas[$cell['key']]; @endphp
                    <div class="aim-cell {{ $cell['hero'] ? 'aim-cell--lede' : '' }}">
                        <div class="aim-cell__top">
                            <a class="aim-cell__link" href="{{ $cell['route'] }}">
                                <p class="aim-label">{{ $cell['label'] }}</p>
                                <p class="aim-value {{ $cell['hero'] ? 'aim-value--hero' : 'aim-value--md' }}">{{ $n($cell['count']) }}</p>
                            </a>
                            @if($hasHistory)
                                <span class="aim-spark {{ $cell['hero'] ? 'aim-spark--lede' : '' }}" aria-hidden="true">
                                    <svg viewBox="0 0 100 30" preserveAspectRatio="none"><path class="l" d="{{ $sparks[$cell['key']]['line'] }}"></path></svg>
                                    <span class="aim-spark__dot" style="top:{{ round($sparks[$cell['key']]['last_y'] / 30 * 100, 2) }}%"></span>
                                    @if($cell['hero'])
                                        <span class="aim-spark__cap">{{ $history->count() }} retratos</span>
                                    @endif
                                </span>
                            @endif
                        </div>
                        @if($delta !== null)
                            <p class="aim-delta {{ $delta['value'] > 0 ? 'aim-delta--up' : ($delta['value'] < 0 ? 'aim-delta--down' : 'aim-delta--flat') }}">
                                <i>{{ $delta['value'] > 0 ? '▲' : ($delta['value'] < 0 ? '▼' : '•') }}</i>
                                <b>{{ $delta['value'] > 0 ? '+' : '' }}{{ $n($delta['value']) }}</b>
                                em {{ $delta['days'] }} {{ $delta['days'] === 1 ? 'dia' : 'dias' }}@if($cell['hero']) · <b>{{ $n($obs['today']) }}</b> hoje @endif
                            </p>
                        @elseif($cell['hero'])
                            <p class="aim-delta aim-delta--flat"><b>{{ $n($obs['today']) }}</b> hoje · sem retrato anterior para comparar</p>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="aim-panel__row aim-panel__row--facts">
                <div class="aim-cell">
                    <p class="aim-label">Workspaces</p>
                    <p class="aim-value aim-value--sm">{{ $n($counts['workspaces']) }}</p>
                </div>
                <div class="aim-cell">
                    <p class="aim-label">Projetos</p>
                    <a class="aim-fact" href="{{ route('admin.ai-memory.projects') }}">
                        <span class="aim-value aim-value--sm">{{ $n($counts['projects']) }}</span>
                    </a>
                </div>
                <div class="aim-cell">
                    <p class="aim-label">Embeddings</p>
                    <p class="aim-value aim-value--sm">{{ $n($counts['embeddings']) }}</p>
                    @if((int) $counts['embeddings'] === 0)
                        <p class="aim-hint">índice semântico ainda não gerado</p>
                    @endif
                </div>
                <div class="aim-cell">
                    <p class="aim-label">Handoffs abertos</p>
                    <a class="aim-fact" href="{{ route('admin.ai-memory.handoffs', ['state' => 'open']) }}">
                        <span class="aim-dot {{ (int) $counts['handoffs_open'] > 0 ? 'aim-dot--warn' : 'aim-dot--idle' }}"></span>
                        <span class="aim-value aim-value--sm">{{ $n($counts['handoffs_open']) }}</span>
                    </a>
                    <p class="aim-hint">{{ (int) $counts['handoffs_open'] > 0 ? 'esperando serem aceitos' : 'nenhum pendente' }}</p>
                </div>
                <div class="aim-cell">
                    <p class="aim-label">Propostas pendentes</p>
                    <span class="aim-fact">
                        <span class="aim-dot {{ (int) $counts['proposals_pending'] > 0 ? 'aim-dot--warn' : 'aim-dot--idle' }}"></span>
                        <span class="aim-value aim-value--sm">{{ $n($counts['proposals_pending']) }}</span>
                    </span>
                    <p class="aim-hint">{{ (int) $counts['proposals_pending'] > 0 ? 'na fila do auto-improve' : 'nada na fila do auto-improve' }}</p>
                </div>
            </div>
        </section>

        {{-- Procedência: de onde saiu cada número desta tela. --}}
        <p class="aim-source">
            <span>fonte: <b>{{ basename($aimemoryPath ?: 'memory.sqlite') }}</b> (somente leitura)</span>
            <span>volume <b>{{ $dockerVolume }}</b></span>
            <span>dias agrupados em <b>UTC</b></span>
        </p>

        {{-- ── Atividade: observações e sessões compartilhando o MESMO eixo de dias ── --}}
        <section class="admin-card aim-card">
            <header class="aim-card__head">
                <div>
                    <h2>Atividade</h2>
                    <p class="aim-sub">últimos {{ count($days) }} dias · {{ $fmtDay($days[0]) }} → {{ $fmtDay(end($days)) }}</p>
                </div>
                <button type="button" class="aim-ghost" data-aim-table aria-pressed="false" aria-expanded="false" aria-controls="aim-activity-table">tabela</button>
            </header>

            <p class="aim-serie">
                <span class="aim-serie__name"><span class="aim-key aim-key--obs"></span>Observações</span>
                <span>
                    <b>{{ $n($obs['total']) }}</b> no período · média <b>{{ $n($obs['avg']) }}</b>/dia
                    @if($obs['peak_day']) · pico <b>{{ $n($obs['max']) }}</b> em {{ $fmtDay($obs['peak_day']) }} @endif
                </span>
            </p>

            <div class="aim-plot" data-aim-plot tabindex="0" role="img"
                 data-days='@json(array_map($fmtDay, $days), JSON_HEX_APOS | JSON_HEX_QUOT)'
                 data-obs='@json(array_values($observationsByDay), JSON_HEX_APOS | JSON_HEX_QUOT)'
                 data-ses='@json(array_values($sessionsByDay), JSON_HEX_APOS | JSON_HEX_QUOT)'
                 aria-label="Observações e sessões por dia nos últimos {{ count($days) }} dias. Observações: {{ $n($obs['total']) }} no período, média de {{ $n($obs['avg']) }} por dia. Sessões: {{ $n($ses['total']) }} no período, média de {{ $n($ses['avg']) }} por dia. Use as setas do teclado para percorrer os dias, ou o botão tabela para ver os números.">
                <div class="aim-row aim-row--obs">
                    <span class="aim-gridline" style="bottom:100%"></span>
                    <span class="aim-gridline" style="bottom:50%"></span>
                    <span class="aim-ymax">{{ $n($obs['top']) }}</span>
                    @foreach($observationsByDay as $day => $value)
                        <div class="aim-col">
                            <span class="aim-bar{{ $value === 0 ? ' aim-bar--zero' : ($value === $obs['max'] ? ' aim-bar--peak' : '') }}"
                                  @if($value > 0) style="height:{{ round($value / $obs['top'] * 100, 2) }}%" @endif></span>
                        </div>
                    @endforeach
                </div>

                <p class="aim-serie aim-serie--stack">
                    <span class="aim-serie__name"><span class="aim-key aim-key--ses"></span>Sessões</span>
                    <span>
                        <b>{{ $n($ses['total']) }}</b> no período · média <b>{{ $n($ses['avg']) }}</b>/dia
                        @if($ses['peak_day']) · pico <b>{{ $n($ses['max']) }}</b> em {{ $fmtDay($ses['peak_day']) }} @endif
                    </span>
                </p>

                <div class="aim-row aim-row--ses">
                    <span class="aim-gridline" style="bottom:100%"></span>
                    <span class="aim-ymax">{{ $n($ses['top']) }}</span>
                    @foreach($sessionsByDay as $day => $value)
                        <div class="aim-col">
                            <span class="aim-bar{{ $value === 0 ? ' aim-bar--zero' : ($value === $ses['max'] ? ' aim-bar--peak' : '') }}"
                                  @if($value > 0) style="height:{{ round($value / $ses['top'] * 100, 2) }}%" @endif></span>
                        </div>
                    @endforeach
                </div>

                <div class="aim-axis">
                    @foreach($days as $idx => $day)
                        @php
                            $isLast = $idx === count($days) - 1;
                            $show = $idx % $labelEvery === 0 || $isLast;
                            // em tela estreita só sobram os rótulos "esparsos" (um a cada dois)
                            $dense = $show && ! $isLast && $idx % ($labelEvery * 2) !== 0;
                        @endphp
                        <span class="{{ $isLast ? 'is-today' : '' }}{{ $dense ? ' is-dense' : '' }}">{{ $show ? $fmtDay($day) : '' }}</span>
                    @endforeach
                </div>

                <div class="aim-cross" data-aim-cross></div>
                <div class="aim-tip" data-aim-tip role="status" aria-live="polite"></div>
            </div>

            @if($obs['total'] === 0 && $ses['total'] === 0)
                <p class="aim-empty" style="margin-top:16px">
                    Nenhuma atividade nestes {{ count($days) }} dias — os agentes não abriram sessão
                    nem gravaram observação. Os totais acima continuam valendo: são o acumulado da memória.
                </p>
            @endif

            {{-- Toda leitura do gráfico existe também em texto: o tooltip nunca é o único caminho. --}}
            <div class="aim-tablewrap" id="aim-activity-table" data-aim-tablewrap hidden>
                <table class="admin-table">
                    <caption class="sr-only">Observações e sessões por dia, do mais recente para o mais antigo</caption>
                    <thead><tr><th scope="col">Dia</th><th scope="col">Observações</th><th scope="col">Sessões</th></tr></thead>
                    <tbody>
                        @foreach(array_reverse($days) as $day)
                            <tr>
                                <td>{{ $fmtDay($day) }}</td>
                                <td>{{ $n($observationsByDay[$day]) }}</td>
                                <td>{{ $n($sessionsByDay[$day] ?? 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <div class="aim-grid2">
            {{-- ── Evolução histórica DURÁVEL (MySQL — sobrevive a reset do ai-memory) ── --}}
            <section class="admin-card aim-card aim-card--tall">
                <header class="aim-card__head">
                    <div>
                        <h2>Evolução histórica</h2>
                        <p class="aim-sub">{{ $history->count() }} {{ $history->count() === 1 ? 'retrato diário' : 'retratos diários' }} · MySQL</p>
                    </div>
                    @if($hasHistory)
                        <div class="aim-seg" role="group" aria-label="Métrica do histórico">
                            @foreach($histMetrics as $key => $label)
                                <button type="button" data-aim-metric="{{ $key }}" aria-pressed="{{ $key === 'observations' ? 'true' : 'false' }}">{{ $label }}</button>
                            @endforeach
                        </div>
                    @endif
                </header>

                @if(! $hasHistory)
                    <p class="aim-empty">
                        @if($history->isEmpty())
                            Ainda sem retratos. O job diário <code>aimemory:snapshot</code> não rodou —
                            execute <code>php artisan aimemory:snapshot</code> para gravar o primeiro
                            (e confira o cron do Laravel no servidor).
                        @else
                            Só um retrato até agora ({{ $histDates[0] }}). A curva aparece a partir do segundo —
                            o job <code>aimemory:snapshot</code> grava um por dia.
                        @endif
                    </p>
                @else
                    <div class="aim-area" data-aim-area
                         data-dates='@json($histDates, JSON_HEX_APOS | JSON_HEX_QUOT)'
                         data-series='@json($histData, JSON_HEX_APOS | JSON_HEX_QUOT)'
                         data-labels='@json($histMetrics, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE)'>
                        <div class="aim-area__grid" data-aim-areagrid>
                            <span style="top:0"></span><i style="top:0" data-aim-ytop>{{ $n($histTop) }}</i>
                            <span style="top:50%"></span><i style="top:50%" data-aim-ymid>{{ $n((int) ($histTop / 2)) }}</i>
                            <span style="bottom:0"></span><i style="bottom:0" class="is-zero">0</i>
                        </div>
                        <svg viewBox="0 0 1000 220" preserveAspectRatio="none" aria-hidden="true">
                            <defs>
                                <linearGradient id="aimFade" x1="0" y1="0" x2="0" y2="1">
                                    <stop offset="0%" stop-color="#6366f1" stop-opacity=".26"></stop>
                                    <stop offset="100%" stop-color="#6366f1" stop-opacity="0"></stop>
                                </linearGradient>
                            </defs>
                            <path class="a" data-aim-areapath d="{{ $histPath['area'] }}"></path>
                            <path class="l" data-aim-linepath d="{{ $histPath['line'] }}"></path>
                        </svg>
                        <div class="aim-area__dot" data-aim-areadot style="left:100%;top:{{ round($histPath['last_y'] / 220 * 100, 2) }}%"></div>
                        <div class="aim-area__end" data-aim-areaend style="left:100%;top:{{ round($histPath['last_y'] / 220 * 100, 2) }}%">{{ $n(end($histData['observations'])) }}</div>
                        <div class="aim-area__hit" data-aim-areahit tabindex="0" role="img"
                             aria-label="Evolução histórica das observações, de {{ $histDates[0] }} a {{ end($histDates) }}: de {{ $n($histData['observations'][0]) }} a {{ $n(end($histData['observations'])) }}."></div>
                        <div class="aim-tip" data-aim-areatip role="status" aria-live="polite"></div>
                    </div>
                    <p class="aim-sub aim-area__foot">{{ $histDates[0] }} → {{ end($histDates) }} · os retratos sobrevivem a um reset do ai-memory</p>
                @endif
            </section>

            {{-- ── Ranking: por onde a memória cresceu ── --}}
            <section class="admin-card aim-card">
                <header class="aim-card__head">
                    <div>
                        <h2>Projetos mais ativos</h2>
                        <p class="aim-sub">por observações acumuladas</p>
                    </div>
                </header>

                @if($topProjects->isEmpty())
                    <p class="aim-empty">Nenhum projeto na memória ainda.</p>
                @else
                    @php $rankMax = max(1, (int) $topProjects->max('observations')); @endphp
                    <ul class="aim-rank">
                        @foreach($topProjects as $project)
                            <li>
                                <a href="{{ route('admin.ai-memory.projects.show', $project->id_hex) }}">
                                    <span class="aim-rank__name">{{ $project->name }}</span>
                                    <span class="aim-rank__n">{{ $n($project->observations) }}</span>
                                    <span class="aim-rank__track"><span class="aim-rank__fill" style="width:{{ round($project->observations / $rankMax * 100, 1) }}%"></span></span>
                                    <span class="aim-rank__meta">{{ $T::human($project->last_session_at) }} · {{ $n($project->sessions) }} sessões · {{ $n($project->pages) }} páginas</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <a class="aim-more" href="{{ route('admin.ai-memory.projects') }}">ver todos os {{ $n($counts['projects']) }} projetos →</a>
                @endif
            </section>
        </div>
    @endunless
</div>
@endsection

@push('scripts')
<script defer src="{{ asset('js/admin/ai-memory/dashboard.js') }}"></script>
@endpush
