@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Sessões')

@section('content')
<div class="aim">
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $T = \App\Services\AiMemory\AiMemoryTime::class;
            $sort = $filters['sort'] ?? 'recent';
            // Links de ordenação preservam os filtros atuais e voltam pra página 1.
            $sortUrl = fn ($s) => route('admin.ai-memory.sessions', array_merge(\Illuminate\Support\Arr::except(request()->query(), 'page'), ['sort' => $s]));
            $startNext = $sort === 'oldest' ? 'recent' : 'oldest';
            $startArrow = $sort === 'recent' ? '▼' : ($sort === 'oldest' ? '▲' : '');
            $durNext = $sort === 'longest' ? 'shortest' : 'longest';
            $durArrow = $sort === 'longest' ? '▼' : ($sort === 'shortest' ? '▲' : '');
            $hasFilters = ! empty(array_filter($filters, fn ($v, $k) => $k !== 'sort' && $v !== null && $v !== '', ARRAY_FILTER_USE_BOTH));
            $maxObs = max(1, (int) collect($sessions->items())->max('obs_count'));
        @endphp

        <section class="admin-card aim-card">
            <header class="aim-card__head">
                <div>
                    <h2>Sessões</h2>
                    <p class="aim-sub">{{ number_format($sessions->total(), 0, ',', '.') }} no total · cada sessão é um agente trabalhando</p>
                </div>
            </header>

            <form method="GET" action="{{ route('admin.ai-memory.sessions') }}" class="aim-filters">
                <div class="form-row">
                    <label for="f-agent">Agente</label>
                    <select name="agent" id="f-agent">
                        <option value="">Todos</option>
                        @foreach($agentKinds as $ak)
                            <option value="{{ $ak }}" @selected(($filters['agent'] ?? null) === $ak)>{{ $ak }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label for="f-project">Projeto</label>
                    <select name="project" id="f-project">
                        <option value="">Todos</option>
                        @foreach($projectOptions as $opt)
                            <option value="{{ $opt->id_hex }}" @selected(($filters['project'] ?? null) === $opt->id_hex)>{{ $opt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label for="f-days">Início</label>
                    <select name="days" id="f-days">
                        <option value="">Tudo</option>
                        @foreach([1 => 'Hoje', 7 => '7 dias', 30 => '30 dias', 90 => '90 dias'] as $v => $lbl)
                            <option value="{{ $v }}" @selected((int) ($filters['days'] ?? 0) === $v)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- preserva a ordenação atual ao aplicar filtros --}}
                <input type="hidden" name="sort" value="{{ $sort }}">
                <div class="aim-filters__go">
                    <button type="submit" class="admin-btn admin-btn-primary">Filtrar</button>
                    @if($hasFilters)<a href="{{ route('admin.ai-memory.sessions') }}" class="admin-btn">Limpar</a>@endif
                </div>
            </form>

            @if($sessions->isEmpty())
                <div class="aim-blank">
                    <i class="fa-solid fa-comments"></i>
                    <p>{{ $hasFilters ? 'Nenhuma sessão para este filtro.' : 'Nenhuma sessão registrada ainda.' }}</p>
                    <p>{{ $hasFilters ? 'Tente ampliar o período ou limpar os filtros.' : 'Uma sessão é aberta pelos hooks do agente (Claude Code, Codex…) ao começar a trabalhar num projeto.' }}</p>
                </div>
            @else
                <div class="aim-scroll">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th scope="col">Agente</th>
                                <th scope="col">Projeto</th>
                                <th scope="col">Diretório</th>
                                <th scope="col" aria-sort="{{ $sort === 'recent' ? 'descending' : ($sort === 'oldest' ? 'ascending' : 'none') }}">
                                    <a href="{{ $sortUrl($startNext) }}" class="aim-th-sort" title="Ordenar por início">Início <i>{{ $startArrow }}</i></a>
                                </th>
                                <th scope="col" aria-sort="{{ $sort === 'longest' ? 'descending' : ($sort === 'shortest' ? 'ascending' : 'none') }}">
                                    <a href="{{ $sortUrl($durNext) }}" class="aim-th-sort" title="Ordenar por duração">Duração <i>{{ $durArrow }}</i></a>
                                </th>
                                <th scope="col">Observações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $s)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.ai-memory.sessions.show', $s->id_hex) }}">
                                            <span class="aim-chip aim-chip--accent">{{ $s->agent_kind }}</span>
                                        </a>
                                    </td>
                                    <td class="aim-mono">{{ $s->project }}</td>
                                    <td><span class="aim-path" style="--w:240px" title="{{ $s->cwd }}">{{ $s->cwd ?? '—' }}</span></td>
                                    <td class="aim-when" title="{{ $T::format($s->started_at) }}">{{ $T::format($s->started_at, 'd/m/Y H:i') }}</td>
                                    <td>
                                        @if($s->ended_at)
                                            <span class="aim-when">{{ $T::duration($s->started_at, $s->ended_at) }}</span>
                                        @else
                                            <span class="aim-chip aim-chip--live">em aberto</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="aim-bar">
                                            <span class="aim-bar__track"><span class="aim-bar__fill" style="width:{{ round(($s->obs_count ?: 0) / $maxObs * 100, 1) }}%"></span></span>
                                            <span class="aim-bar__n">{{ number_format((int) $s->obs_count, 0, ',', '.') }}</span>
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $sessions->links() }}
            @endif
        </section>
    @endunless
</div>
@endsection
