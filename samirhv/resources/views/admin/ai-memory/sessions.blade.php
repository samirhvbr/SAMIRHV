@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Sessões')

@section('content')
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php $T = \App\Services\AiMemory\AiMemoryTime::class; @endphp

        <div class="admin-card">
            <h2>Sessões <span class="card-sub">— {{ $sessions->total() }}</span></h2>

            <form method="GET" action="{{ route('admin.ai-memory.sessions') }}" class="filters">
                <div class="form-row">
                    <label>Projeto</label>
                    <select name="project" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        @foreach($projectOptions as $opt)
                            <option value="{{ $opt->id_hex }}" @selected($project === $opt->id_hex)>{{ $opt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="admin-btn admin-btn-primary">Filtrar</button>
                @if($project)<a href="{{ route('admin.ai-memory.sessions') }}" class="admin-btn">Limpar</a>@endif
            </form>

            <div style="overflow-x:auto">
                <table class="admin-table">
                    <thead>
                        <tr><th>Agente</th><th>Projeto</th><th>Diretório</th><th>Início</th><th>Fim</th><th>Duração</th><th>Obs</th></tr>
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
                            <tr><td colspan="7" class="muted" style="text-align:center;padding:36px">Nenhuma sessão.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination">{{ $sessions->links() }}</div>
        </div>
    @endunless
@endsection
