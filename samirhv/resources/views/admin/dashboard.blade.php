@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('topbar-actions')
    <a href="{{ route('admin.projects.create') }}" class="admin-btn admin-btn-primary"><i class="fa-solid fa-plus"></i> Novo projeto</a>
@endsection

@section('content')

    <div class="admin-stats-grid">
        <div class="admin-stat"><div class="label">Projetos</div><div class="value">{{ $stats['projects'] }}</div></div>
        <div class="admin-stat"><div class="label">Arquivos</div><div class="value">{{ $stats['files'] }}</div></div>
        <div class="admin-stat"><div class="label">Downloads (total)</div><div class="value">{{ number_format($stats['downloads_total'], 0, ',', '.') }}</div></div>
        <div class="admin-stat"><div class="label">Downloads hoje</div><div class="value">{{ $stats['downloads_today'] }}</div></div>
    </div>

    <div class="an-grid-2">
        <div class="admin-card">
            <h2>Arquivos mais baixados</h2>
            @if($topFiles->isEmpty())
                <p class="card-sub">Nenhum download ainda.</p>
            @else
                <ul class="an-list">
                    @foreach($topFiles as $file)
                        <li>
                            <span>{{ $file->label }} <span class="muted" style="color:var(--dim)">— {{ $file->project?->title ?? '—' }}</span></span>
                            <span class="total">{{ number_format($file->downloads_count, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="admin-card">
            <h2>Downloads recentes</h2>
            @if($recentDownloads->isEmpty())
                <p class="card-sub">Sem registros recentes.</p>
            @else
                <ul class="an-list">
                    @foreach($recentDownloads as $log)
                        <li>
                            <span>{{ $log->file?->label ?? '(arquivo removido)' }}</span>
                            <span class="muted" style="color:var(--dim);font-family:'JetBrains Mono',monospace;font-size:.74rem">{{ $log->created_at->timezone('America/Sao_Paulo')->format('d/m H:i') }}</span>
                        </li>
                    @endforeach
                </ul>
                <div style="margin-top:14px"><a href="{{ route('admin.audit.index') }}" class="admin-btn admin-btn-sm">Ver auditoria completa →</a></div>
            @endif
        </div>
    </div>

@endsection
