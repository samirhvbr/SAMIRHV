@extends('admin.layouts.app')

@section('title', $repository->fullName().' · GitHub View')

@push('styles')
<style>
    .gh-head{ display:flex; align-items:flex-end; justify-content:space-between; gap:16px; flex-wrap:wrap; }
    .gh-back{ color:var(--dim); text-decoration:none; font-size:.72rem; text-transform:uppercase; letter-spacing:.12em; }
    .gh-back:hover{ color:var(--muted); }
    .gh-title{ font-size:1.7rem; font-weight:700; color:var(--txt); margin:5px 0 0; }
    .gh-desc{ color:var(--muted); font-size:.9rem; margin:5px 0 0; }
    .gh-actions{ display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
    .gh-actions form{ margin:0; }
    .gh-btn{ display:inline-flex; align-items:center; gap:7px; border-radius:var(--radius-sm); padding:8px 13px; font:inherit;
        font-weight:600; font-size:.8rem; cursor:pointer; text-decoration:none; border:1px solid var(--line); background:transparent; color:var(--txt); }
    .gh-btn:hover{ background:var(--accent-soft); border-color:var(--line-hover); }
    .gh-btn--danger{ color:var(--danger); border-color:rgba(239,68,68,.3); }
    .gh-btn--danger:hover{ background:rgba(239,68,68,.08); }
    .gh-window{ display:flex; align-items:center; justify-content:flex-end; gap:6px; margin-top:22px; font-size:.75rem; }
    .gh-window a{ border:1px solid var(--line); border-radius:var(--radius-sm); padding:5px 11px; color:var(--muted); text-decoration:none; }
    .gh-window a:hover{ border-color:var(--line-hover); }
    .gh-window a.is-active{ border-color:var(--accent); background:var(--accent-soft); color:#c7d2fe; }
    .gh-hm__head{ display:flex; align-items:flex-end; justify-content:space-between; gap:16px; flex-wrap:wrap; }
    .gh-hm__count{ font-size:1.9rem; font-weight:700; color:var(--txt); margin:2px 0 0; }
    .gh-hm__legend{ display:flex; align-items:center; gap:9px; color:var(--muted); font-size:.72rem; }
    .gh-hm__ramp{ display:inline-block; width:96px; height:9px; border-radius:999px;
        background:linear-gradient(to right,#2d1b4e,#7e22ce,#c026d3,#ec4899,#f97316,#facc15); }
    .gh-hm__canvas{ width:100%; margin-top:16px; display:block; }
    .gh-empty{ text-align:center; color:var(--muted); padding:40px 20px; }
</style>
@endpush

@section('content')
    <div class="gh-head">
        <div>
            <a href="{{ route('admin.github-view.index') }}" class="gh-back">← GitHub View</a>
            <h1 class="gh-title">{{ $repository->fullName() }}</h1>
            @if($repository->description)<p class="gh-desc">{{ $repository->description }}</p>@endif
        </div>
        <div class="gh-actions">
            <form method="POST" action="{{ route('admin.github-view.repos.sync', ['owner' => $repository->owner, 'name' => $repository->name]) }}">
                @csrf
                <button type="submit" class="gh-btn"><i class="fa-solid fa-rotate"></i> sincronizar</button>
            </form>
            <a href="{{ $repository->githubUrl() }}" target="_blank" rel="noopener" class="gh-btn"><i class="fa-brands fa-github"></i> github ↗</a>
            <form method="POST" action="{{ route('admin.github-view.repos.destroy', ['owner' => $repository->owner, 'name' => $repository->name]) }}"
                  onsubmit="return confirm('Remover {{ $repository->fullName() }} e todos os seus dados?')">
                @csrf @method('DELETE')
                <button type="submit" class="gh-btn gh-btn--danger"><i class="fa-solid fa-trash"></i> remover</button>
            </form>
        </div>
    </div>

    @if($repository->sync_error)
        <div class="admin-alert admin-alert-error" style="margin-top:16px">
            <i class="fa-solid fa-circle-exclamation"></i> Última sincronização falhou: {{ $repository->sync_error }}
        </div>
    @endif

    <div class="gh-window">
        <span style="color:var(--dim);text-transform:uppercase;letter-spacing:.12em;margin-right:4px">janela</span>
        @foreach($windows as $days)
            <a href="{{ route('admin.github-view.repos.show', ['owner' => $repository->owner, 'name' => $repository->name, 'days' => $days]) }}"
               class="{{ $days === $window ? 'is-active' : '' }}">{{ $days }}d</a>
        @endforeach
    </div>

    @if($heatmap['total'] === 0)
        <div class="admin-card gh-empty" style="margin-top:16px">
            Sem commits nos últimos {{ $window }} dias{{ $repository->isSyncing() ? ' (ainda) — sync em andamento.' : ' — tente uma janela maior.' }}
        </div>
    @else
        {{-- Heatmap dia × hora (canvas). O ES module lê o JSON abaixo e anima. --}}
        <div class="admin-card" style="margin-top:16px" data-gh-heatmap>
            <div class="gh-hm__head">
                <div>
                    <p class="card-sub" style="text-transform:uppercase;letter-spacing:.12em">últimos {{ $window }} dias · 24 horas</p>
                    <p class="gh-hm__count"><span data-gh-heatmap-counter>{{ number_format($heatmap['total'], 0, ',', '.') }}</span> commits</p>
                </div>
                <div class="gh-hm__legend">
                    <span>1</span>
                    <span class="gh-hm__ramp"></span>
                    <span>{{ $heatmap['max'] }} commits/hora</span>
                    <button type="button" class="gh-btn" data-gh-heatmap-replay><i class="fa-solid fa-rotate-right"></i> replay</button>
                </div>
            </div>
            <canvas class="gh-hm__canvas" data-gh-heatmap-canvas></canvas>
            <script type="application/json" data-gh-heatmap-data>@json($heatmap)</script>
        </div>
    @endif
@endsection

@push('scripts')
<script type="module" src="{{ asset('js/admin/github-view/heatmap.js') }}"></script>
@endpush
