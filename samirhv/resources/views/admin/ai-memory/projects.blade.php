@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Projetos')

@section('content')
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php $T = \App\Services\AiMemory\AiMemoryTime::class; @endphp
        <div class="admin-card">
            <h2>Projetos <span class="card-sub">— {{ count($projects) }} na memória</span></h2>
            <div style="overflow-x:auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Projeto</th><th>Workspace</th><th>Repo</th>
                            <th>Páginas</th><th>Sessões</th><th>Observações</th><th>Última atividade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($projects as $p)
                            <tr>
                                <td><a href="{{ route('admin.ai-memory.projects.show', $p->id_hex) }}"><b>{{ $p->name }}</b></a></td>
                                <td class="muted">{{ $p->workspace }}</td>
                                <td style="max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $p->repo_path }}"><code>{{ $p->repo_path ?? '—' }}</code></td>
                                <td>{{ $p->pages }}</td>
                                <td>{{ $p->sessions }}</td>
                                <td>{{ $p->observations }}</td>
                                <td class="muted" style="white-space:nowrap">{{ $T::human($p->last_session_at) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="muted" style="text-align:center;padding:36px">Nenhum projeto na memória.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endunless
@endsection
