@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('topbar-actions')
    <a href="{{ route('admin.projects.create') }}" class="admin-btn admin-btn-primary"><i class="fa-solid fa-plus"></i> Novo projeto</a>
@endsection

@section('content'

    <div class="admin-stats-grid">
        <div class="admin-stat">
            <i class="fa-solid fa-folder-open" style="position: absolute; top: 18px; right: 18px; font-size: 1rem; color: var(--accent); opacity: 0.3;"></i>
            <div class="label">Projetos</div>
            <div class="value">{{ $stats['projects'] }}</div>
        </div>
        <div class="admin-stat">
            <i class="fa-solid fa-file-zipper" style="position: absolute; top: 18px; right: 18px; font-size: 1rem; color: var(--ok); opacity: 0.3;"></i>
            <div class="label">Arquivos</div>
            <div class="value">{{ $stats['files'] }}</div>
        </div>
        <div class="admin-stat">
            <i class="fa-solid fa-download" style="position: absolute; top: 18px; right: 18px; font-size: 1rem; color: var(--warn); opacity: 0.3;"></i>
            <div class="label">Downloads (total)</div>
            <div class="value">{{ number_format($stats['downloads_total'], 0, ',', '.') }}</div>
        </div>
        <div class="admin-stat">
            <i class="fa-solid fa-chart-line" style="position: absolute; top: 18px; right: 18px; font-size: 1rem; color: #818cf8; opacity: 0.3;"></i>
            <div class="label">Downloads hoje</div>
            <div class="value" style="color: var(--ok);">{{ $stats['downloads_today'] }}</div>
        </div>
    </div>

    <div class="an-grid-2">
        <div class="admin-card">
            <h2><i class="fa-solid fa-trophy" style="color: var(--warn); margin-right: 8px; font-size: 0.9rem;"></i>Arquivos mais baixados</h2>
            @if($topFiles->isEmpty())
                <p class="card-sub" style="padding: 20px 0; text-align: center;">Nenhum download ainda.</p>
            @else
                <ul class="an-list">
                    @foreach($topFiles as $file)
                        <li>
                            <span style="display: flex; align-items: center; gap: 6px;">
                                <i class="fa-solid fa-file-arrow-down" style="font-size: 0.7rem; color: var(--accent-soft-2);"></i>
                                {{ $file->label }}
                                <span class="badge badge-muted" style="font-size: .62rem;">{{ $file->project?->title ?? '—' }}</span>
                            </span>
                            <span class="total"><i class="fa-solid fa-arrow-trend-up" style="margin-right: 4px; font-size: .65rem;"></i>{{ number_format($file->downloads_count, 0, ',', '.') }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="admin-card">
            <h2><i class="fa-solid fa-clock-rotate-left" style="color: var(--accent); margin-right: 8px; font-size: 0.9rem;"></i>Downloads recentes</h2>
            @if($recentDownloads->isEmpty())
                <p class="card-sub" style="padding: 20px 0; text-align: center;">Sem registros recentes.</p>
            @else
                <ul class="an-list">
                    @foreach($recentDownloads as $log)
                        <li>
                            <span style="display: flex; align-items: center; gap: 7px;">
                                <i class="fa-solid fa-file" style="font-size: 0.68rem; color: var(--dim);"></i>
                                {{ $log->file?->label ?? '(arquivo removido)' }}
                            </span>
                            <span class="muted" style="color:var(--dim);font-family:'JetBrains Mono',monospace;font-size:.74rem">
                                {{ $log->created_at->timezone('America/Sao_Paulo')->format('d/m H:i') }}
                            </span>
                        </li>
                    @endforeach
                </ul>
                <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--line);">
                    <a href="{{ route('admin.audit.index') }}" class="admin-btn admin-btn-sm">Ver auditoria completa <i class="fa-solid fa-arrow-right" style="font-size: .65rem;"></i></a>
                </div>
            @endif
        </div>
    </div>

@endsection
