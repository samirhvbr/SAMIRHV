@extends('admin.layouts.app')

@section('title', 'GitHub View')

@push('styles')
<style>
    .gh-add{ display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:6px; }
    .gh-input{ flex:1; min-width:260px; background:var(--panel-2); border:1px solid var(--line); color:var(--txt);
        border-radius:var(--radius-sm); padding:10px 13px; font:inherit; }
    .gh-input:focus{ outline:none; border-color:var(--line-hover); }
    .gh-btn{ display:inline-flex; align-items:center; gap:7px; background:var(--accent); color:#fff; border:1px solid transparent;
        border-radius:var(--radius-sm); padding:10px 16px; font:inherit; font-weight:600; cursor:pointer; text-decoration:none; }
    .gh-btn:hover{ filter:brightness(1.08); }
    .gh-err{ color:var(--danger); font-size:.85rem; margin:8px 0 18px; }
    .gh-list{ display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:14px; margin-top:22px; }
    .gh-repo{ display:block; background:var(--panel); border:1px solid var(--line); border-radius:var(--radius-md);
        padding:16px; text-decoration:none; color:inherit; transition:border-color .15s, transform .15s; }
    .gh-repo:hover{ border-color:var(--line-hover); transform:translateY(-1px); }
    .gh-repo__owner{ color:var(--dim); }
    .gh-repo__name{ font-weight:600; color:var(--txt); }
    .gh-repo__desc{ color:var(--muted); font-size:.85rem; margin-top:6px; line-height:1.45;
        display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .gh-repo__foot{ display:flex; align-items:center; justify-content:space-between; margin-top:12px; }
    .gh-badge{ display:inline-flex; align-items:center; gap:6px; font-size:.72rem; font-weight:600; text-transform:uppercase;
        letter-spacing:.03em; padding:3px 9px; border-radius:999px; border:1px solid var(--line); color:var(--dim); }
    .gh-badge--synced{ color:var(--ok); border-color:rgba(34,197,94,.3); background:rgba(34,197,94,.08); }
    .gh-badge--syncing{ color:var(--accent); border-color:var(--line-hover); background:var(--accent-soft); }
    .gh-badge--failed{ color:var(--danger); border-color:rgba(239,68,68,.3); background:rgba(239,68,68,.08); }
    .gh-empty{ text-align:center; color:var(--muted); padding:48px 20px; }
</style>
@endpush

@section('content')
    <form method="POST" action="{{ route('admin.github-view.repos.store') }}" class="gh-add">
        @csrf
        <input type="text" name="repository" class="gh-input" autocomplete="off" spellcheck="false" required
            value="{{ old('repository') }}"
            placeholder="{{ $defaultOwner ? $defaultOwner.'/repo   ·   ou só   repo' : 'owner/repo' }}">
        <button type="submit" class="gh-btn"><i class="fa-solid fa-plus"></i> Adicionar &amp; sincronizar</button>
    </form>
    @error('repository')<p class="gh-err">{{ $message }}</p>@enderror

    @if($repositories->isEmpty())
        <div class="gh-empty">
            <p><i class="fa-solid fa-code-branch" style="font-size:1.7rem;opacity:.4"></i></p>
            <p>Nenhum repositório ainda. Adicione um <code>owner/name</code> acima pra ver as visualizações.</p>
        </div>
    @else
        <div class="gh-list">
            @foreach($repositories as $repo)
                <a href="{{ route('admin.github-view.repos.show', ['owner' => $repo->owner, 'name' => $repo->name]) }}" class="gh-repo">
                    <div><span class="gh-repo__owner">{{ $repo->owner }}/</span><span class="gh-repo__name">{{ $repo->name }}</span></div>
                    @if($repo->description)<div class="gh-repo__desc">{{ $repo->description }}</div>@endif
                    <div class="gh-repo__foot">
                        <span class="gh-badge gh-badge--{{ $repo->sync_status }}">
                            @switch($repo->sync_status)
                                @case('synced')<i class="fa-solid fa-circle-check"></i> sincronizado @break
                                @case('syncing')<i class="fa-solid fa-rotate"></i> sincronizando @break
                                @case('failed')<i class="fa-solid fa-circle-exclamation"></i> falhou @break
                                @default<i class="fa-solid fa-clock"></i> pendente
                            @endswitch
                        </span>
                        @if($repo->last_synced_at)
                            <span style="color:var(--dim);font-size:.72rem">{{ $repo->last_synced_at->diffForHumans() }}</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection
