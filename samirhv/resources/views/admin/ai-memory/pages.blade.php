@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Páginas')

@section('content')
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $T = \App\Services\AiMemory\AiMemoryTime::class;
            $tierBadge = ['working' => 'badge-warn', 'episodic' => 'badge-accent', 'semantic' => 'badge-ok', 'procedural' => 'badge-muted'];
        @endphp

        <div class="admin-card">
            <h2>Páginas <span class="card-sub">— {{ $pages->total() }} (versão atual)</span></h2>

            <form method="GET" action="{{ route('admin.ai-memory.pages') }}" class="filters">
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
                @if($project)
                    <a href="{{ route('admin.ai-memory.pages') }}" class="admin-btn">Limpar</a>
                @endif
            </form>

            <div style="overflow-x:auto">
                <table class="admin-table">
                    <thead>
                        <tr><th>Título</th><th>Caminho</th><th>Tier</th><th>Projeto</th><th>Atualizada</th></tr>
                    </thead>
                    <tbody>
                        @forelse($pages as $p)
                            <tr>
                                <td>
                                    @if($p->pinned)<i class="fa-solid fa-thumbtack" style="color:var(--warn);font-size:.7rem" title="Fixada"></i> @endif
                                    <a href="{{ route('admin.ai-memory.pages.show', $p->id_hex) }}"><b>{{ $p->title }}</b></a>
                                </td>
                                <td style="max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $p->path }}"><code>{{ $p->path }}</code></td>
                                <td><span class="badge {{ $tierBadge[$p->tier] ?? 'badge-muted' }}">{{ $p->tier }}</span></td>
                                <td class="muted">{{ $p->project }}</td>
                                <td class="muted" style="white-space:nowrap">{{ $T::format($p->updated_at, 'd/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="muted" style="text-align:center;padding:36px">Nenhuma página.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination">{{ $pages->links() }}</div>
        </div>
    @endunless
@endsection
