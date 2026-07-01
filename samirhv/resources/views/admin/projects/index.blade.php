@extends('admin.layouts.app')

@section('title', 'Projetos')

@section('topbar-actions')
    <a href="{{ route('admin.projects.create') }}" class="admin-btn admin-btn-primary"><i class="fa-solid fa-plus"></i> Novo projeto</a>
@endsection

@section('content')

    <div class="admin-card" style="padding:0;overflow:hidden">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Projeto</th>
                    <th>Categoria</th>
                    <th>Arquivos</th>
                    <th>Downloads</th>
                    <th>Status</th>
                    <th style="text-align:right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                    <tr>
                        <td>
                            <strong style="color:#f1f5f9">{{ $project->title }}</strong>
                            @if($project->external_url)
                                <span class="badge badge-muted" style="margin-left:6px">{{ $project->redirect_to_site ? 'link' : 'híbrido' }}</span>
                                <div class="muted" style="font-family:'JetBrains Mono',monospace;font-size:.72rem"><a href="{{ $project->external_url }}" target="_blank" rel="noopener" style="color:inherit">{{ $project->external_url }} ↗</a></div>
                            @else
                                <div class="muted" style="font-family:'JetBrains Mono',monospace;font-size:.72rem">/p/{{ $project->slug }}</div>
                            @endif
                        </td>
                        <td>{{ $project->category ?? '—' }}</td>
                        <td>{{ $project->files_count }}</td>
                        <td><span class="total" style="font-family:'JetBrains Mono',monospace;color:#c7d2fe">{{ number_format($project->downloads_total ?? 0, 0, ',', '.') }}</span></td>
                        <td>
                            @if($project->is_published)
                                <span class="badge badge-ok">publicado</span>
                            @else
                                <span class="badge badge-muted">rascunho</span>
                            @endif
                        </td>
                        <td style="text-align:right;white-space:nowrap">
                            <a href="{{ route('admin.projects.files.index', $project) }}" class="admin-btn admin-btn-sm"><i class="fa-solid fa-file-arrow-up"></i> Arquivos</a>
                            <a href="{{ route('admin.projects.edit', $project) }}" class="admin-btn admin-btn-sm"><i class="fa-solid fa-pen"></i></a>
                            <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" style="display:inline" onsubmit="return confirm('Remover o projeto &quot;{{ $project->title }}&quot; e seus arquivos?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-btn admin-btn-sm admin-btn-danger"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="muted" style="text-align:center;padding:40px">Nenhum projeto ainda. <a href="{{ route('admin.projects.create') }}" style="color:var(--accent)">Criar o primeiro</a>.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">{{ $projects->links() }}</div>

@endsection
