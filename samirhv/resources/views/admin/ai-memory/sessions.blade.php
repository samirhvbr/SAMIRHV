@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Sessões')

@section('content')
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $T = \App\Services\AiMemory\AiMemoryTime::class;
            $sort = $filters['sort'] ?? 'recent';
            // Links de ordenação preservam os filtros atuais e voltam pra página 1.
            $sortUrl = fn ($s) => route('admin.ai-memory.sessions', array_merge(\Illuminate\Support\Arr::except(request()->query(), 'page'), ['sort' => $s]));
            $inicioNext = $sort === 'oldest' ? 'recent' : 'oldest';
            $inicioArrow = $sort === 'recent' ? ' ▼' : ($sort === 'oldest' ? ' ▲' : '');
            $durNext = $sort === 'longest' ? 'shortest' : 'longest';
            $durArrow = $sort === 'longest' ? ' ▼' : ($sort === 'shortest' ? ' ▲' : '');
        @endphp

        <div class="admin-card">
            <h2>Sessões <span class="card-sub">— {{ $sessions->total() }}</span></h2>

            <form method="GET" action="{{ route('admin.ai-memory.sessions') }}" class="filters">
                <div class="form-row">
                    <label>Agente</label>
                    <select name="agent">
                        <option value="">Todos</option>
                        @foreach($agentKinds as $ak)
                            <option value="{{ $ak }}" @selected(($filters['agent'] ?? null) === $ak)>{{ $ak }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>Projeto</label>
                    <select name="project">
                        <option value="">Todos</option>
                        @foreach($projectOptions as $opt)
                            <option value="{{ $opt->id_hex }}" @selected(($filters['project'] ?? null) === $opt->id_hex)>{{ $opt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>Início (período)</label>
                    <select name="days">
                        <option value="">Tudo</option>
                        @foreach([1 => 'Hoje', 7 => '7 dias', 30 => '30 dias', 90 => '90 dias'] as $v => $lbl)
                            <option value="{{ $v }}" @selected((int) ($filters['days'] ?? 0) === $v)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- preserva a ordenação atual ao aplicar filtros --}}
                <input type="hidden" name="sort" value="{{ $sort }}">
                <button type="submit" class="admin-btn admin-btn-primary">Filtrar</button>
                <a href="{{ route('admin.ai-memory.sessions') }}" class="admin-btn">Limpar</a>
            </form>

            <div style="overflow-x:auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Agente</th>
                            <th>Projeto</th>
                            <th>Diretório</th>
                            <th><a href="{{ $sortUrl($inicioNext) }}" style="color:inherit;text-decoration:none" title="Ordenar por início">Início{{ $inicioArrow }}</a></th>
                            <th>Fim</th>
                            <th><a href="{{ $sortUrl($durNext) }}" style="color:inherit;text-decoration:none" title="Ordenar por duração">Duração{{ $durArrow }}</a></th>
                            <th>Obs</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $s)
                            <tr>
                                <td><a href="{{ route('admin.ai-memory.sessions.show', $s->id_hex) }}"><span class="badge badge-accent">{{ $s->agent_kind }}</span></a></td>
                                <td class="muted">{{ $s->project }}</td>
                                <td style="max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $s->cwd }}"><code>{{ $s->cwd ?? '—' }}</code></td>
                                <td class="muted" style="white-space:nowrap">{{ $T::format($s->started_at, 'd/m/Y H:i') }}</td>
                                <td class="muted" style="white-space:nowrap">{{ $s->ended_at ? $T::format($s->ended_at, 'd/m/Y H:i') : '—' }}</td>
                                <td class="muted" style="white-space:nowrap">{{ $T::duration($s->started_at, $s->ended_at) }}</td>
                                <td>{{ $s->obs_count }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="muted" style="text-align:center;padding:36px">Nenhuma sessão para o filtro.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination">{{ $sessions->links() }}</div>
        </div>
    @endunless
@endsection
