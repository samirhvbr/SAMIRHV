@extends('admin.layouts.app')

@section('title', 'GitHub View')

@push('styles')
<style>
    /* topo: contagem + adicionar/importar */
    .gh-top{ display:flex; align-items:flex-end; justify-content:space-between; gap:18px; flex-wrap:wrap; margin-bottom:4px; }
    .gh-kicker{ font-size:.7rem; text-transform:uppercase; letter-spacing:.14em; color:var(--dim); margin:0 0 4px; }
    .gh-count{ font-size:2.1rem; font-weight:700; color:var(--txt); margin:0; line-height:1; }
    .gh-count span{ font-size:1.1rem; font-weight:500; color:var(--muted); margin-left:6px; }
    .gh-top__actions{ display:flex; flex-direction:column; gap:8px; align-items:flex-end; }
    .gh-add{ display:flex; gap:8px; }
    .gh-input{ width:260px; max-width:52vw; background:var(--panel-2); border:1px solid var(--line); color:var(--txt);
        border-radius:var(--radius-sm); padding:9px 12px; font:inherit; }
    .gh-input:focus{ outline:none; border-color:var(--line-hover); }
    .gh-btn{ display:inline-flex; align-items:center; gap:7px; background:var(--accent); color:#fff; border:1px solid transparent;
        border-radius:var(--radius-sm); padding:9px 15px; font:inherit; font-weight:600; font-size:.85rem; cursor:pointer; text-decoration:none; white-space:nowrap; }
    .gh-btn:hover{ filter:brightness(1.08); }
    .gh-btn--ghost{ background:transparent; color:var(--muted); border:1px solid var(--line); font-weight:500; }
    .gh-btn--ghost:hover{ background:var(--accent-soft); border-color:var(--line-hover); color:var(--txt); filter:none; }
    .gh-err{ color:var(--danger); font-size:.85rem; margin:10px 0 0; }
    .gh-empty{ text-align:center; color:var(--muted); padding:52px 20px; }

    /* barras: commits por repo (log scale) */
    .gh-bars{ margin-top:34px; }
    .gh-bars__label{ font-size:.7rem; text-transform:uppercase; letter-spacing:.14em; color:var(--dim); margin:0 0 12px; }
    .gh-scale{ text-transform:none; letter-spacing:0; color:var(--dim); background:none; border:none; cursor:pointer; font:inherit; font-size:.72rem; }
    .gh-scale:hover{ color:var(--muted); }
    .gh-bars__list{ display:flex; flex-direction:column; gap:6px; }
    .gh-bars__more{ margin-top:6px; }
    .gh-bar{ display:flex; align-items:center; gap:12px; font-size:.85rem; }
    .gh-bar__name{ width:min(260px,34vw); flex-shrink:0; color:var(--muted); text-decoration:none; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .gh-bar__name:hover{ color:var(--txt); }
    .gh-bar__track{ flex:1; height:12px; background:var(--panel-2); border-radius:4px; overflow:hidden; }
    .gh-bar__fill{ height:100%; border-radius:4px; transition:width .35s ease; }
    .gh-bar__count{ width:70px; text-align:right; color:var(--muted); font-variant-numeric:tabular-nums; }
    .gh-showall{ margin-top:10px; font-size:.75rem; color:var(--muted); background:none; border:none; cursor:pointer; padding:0; }
    .gh-showall:hover{ color:var(--txt); }

    /* sort */
    .gh-sort{ display:flex; align-items:center; justify-content:flex-end; gap:6px; margin-top:34px; font-size:.75rem; }
    .gh-sort__label{ text-transform:uppercase; letter-spacing:.14em; color:var(--dim); margin-right:4px; }
    .gh-sort__opt{ border:1px solid var(--line); border-radius:var(--radius-sm); padding:4px 11px; color:var(--muted); text-decoration:none; }
    .gh-sort__opt:hover{ border-color:var(--line-hover); }
    .gh-sort__opt.is-active{ border-color:var(--accent); background:var(--accent-soft); color:#c7d2fe; }

    /* cards */
    .gh-grid{ display:grid; grid-template-columns:repeat(auto-fill,minmax(320px,1fr)); gap:14px; margin-top:12px; }
    .gh-card{ background:var(--panel); border:1px solid var(--line); border-radius:var(--radius-md); padding:16px; transition:border-color .15s; }
    .gh-card:hover{ border-color:var(--line-hover); }
    .gh-card__head{ display:flex; align-items:flex-start; justify-content:space-between; gap:10px; }
    .gh-card__name{ display:block; color:var(--txt); font-weight:600; text-decoration:none; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .gh-card__name:hover{ color:#c7d2fe; }
    .gh-card__desc{ color:var(--dim); font-size:.78rem; margin:2px 0 0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .gh-dot{ flex-shrink:0; width:10px; height:10px; border-radius:50%; margin-top:5px; background:var(--dim); }
    .gh-dot--ok{ background:var(--ok); } .gh-dot--bad{ background:var(--danger); } .gh-dot--warn{ background:var(--warn); }
    .gh-card__chips{ display:flex; gap:3px; margin-top:16px; }
    .gh-chip{ flex:1; height:16px; border-radius:2px; }
    .gh-card__ago{ font-size:.7rem; color:var(--dim); margin:9px 0 0; }
    .gh-card__foot{ display:flex; align-items:center; justify-content:space-between; gap:10px; margin-top:12px; font-size:.78rem; }
    .gh-card__stats{ color:var(--muted); }
    .gh-add-n{ color:#f472b6; } .gh-del-n{ color:#22d3ee; }
    .gh-badge{ display:inline-flex; align-items:center; gap:5px; font-size:.68rem; font-weight:600; text-transform:uppercase; letter-spacing:.03em;
        padding:3px 8px; border-radius:999px; border:1px solid var(--line); color:var(--dim); }
    .gh-badge--synced{ color:var(--ok); border-color:rgba(34,197,94,.3); background:rgba(34,197,94,.08); }
    .gh-badge--syncing{ color:var(--accent); border-color:var(--line-hover); background:var(--accent-soft); }
    .gh-badge--failed{ color:var(--danger); border-color:rgba(239,68,68,.3); background:rgba(239,68,68,.08); }

    .is-hidden{ display:none; }
    @media (max-width:640px){ .gh-top__actions{ align-items:stretch; } .gh-input{ max-width:none; flex:1; } }
    @media (prefers-reduced-motion:reduce){ .gh-bar__fill{ transition:none; } }
</style>
@endpush

@section('content')
    @php
        $max = $overview->maxCommits();
        $ranked = $repositories->sortByDesc(fn ($r) => $overview->for($r)['total_commits'])->values();
        $barColors = ['#d946ef', '#fb923c', '#22d3ee', '#34d399', '#ec4899', '#8b5cf6', '#fcd34d', '#38bdf8'];
        $sortKey = explode('_', $sort)[0];
        $sortDir = explode('_', $sort)[1] ?? 'desc';
        $chipDays = \App\Services\GitHub\Visualizations\RepositoryOverview::CHIP_DAYS;
    @endphp

    <div class="gh-top">
        <div>
            <p class="gh-kicker">Repositórios monitorados</p>
            <p class="gh-count">{{ $repositories->count() }} <span>repos</span></p>
        </div>
        <div class="gh-top__actions">
            <form method="POST" action="{{ route('admin.github-view.repos.store') }}" class="gh-add">
                @csrf
                <input type="text" name="repository" class="gh-input" autocomplete="off" spellcheck="false" required
                    value="{{ old('repository') }}"
                    placeholder="{{ $defaultOwner ? $defaultOwner.'/repo · ou só repo' : 'owner/repo' }}">
                <button type="submit" class="gh-btn"><i class="fa-solid fa-plus"></i> add + sync</button>
            </form>
            <form method="POST" action="{{ route('admin.github-view.import') }}"
                  onsubmit="return confirm('Importar TODOS os seus repositórios do GitHub como pendentes? (você sincroniza depois, ou deixa o cron)')">
                @csrf
                <button type="submit" class="gh-btn gh-btn--ghost"><i class="fa-solid fa-cloud-arrow-down"></i> Importar todos os meus repos</button>
            </form>
        </div>
    </div>
    @error('repository')<p class="gh-err">{{ $message }}</p>@enderror

    @if($repositories->isEmpty())
        <div class="gh-empty">
            <p><i class="fa-solid fa-code-branch" style="font-size:1.7rem;opacity:.4"></i></p>
            <p>Nenhum repositório ainda. Adicione um <code>owner/name</code> acima (ou importe todos) — o 1º sync começa na hora.</p>
        </div>
    @else
        {{-- Barras: commits por repo (log scale por padrão; toggle p/ linear). --}}
        <section class="gh-bars" data-gh-bars>
            <p class="gh-bars__label">+ commits por repo
                <button type="button" class="gh-scale" data-gh-barscale-label>(log scale)</button>
            </p>
            <div class="gh-bars__list">
                @foreach($ranked->take(3) as $i => $repo)
                    @include('admin.github-view._bar', ['repo' => $repo, 'stats' => $overview->for($repo), 'max' => $max, 'color' => $barColors[$i % count($barColors)]])
                @endforeach
            </div>
            @if($ranked->count() > 3)
                <div class="gh-bars__list gh-bars__more is-hidden" data-gh-reveal-content>
                    {{-- slice(3) preserva as chaves originais (3,4,5…), então $i já é o índice global. --}}
                    @foreach($ranked->slice(3) as $i => $repo)
                        @include('admin.github-view._bar', ['repo' => $repo, 'stats' => $overview->for($repo), 'max' => $max, 'color' => $barColors[$i % count($barColors)]])
                    @endforeach
                </div>
                <button type="button" class="gh-showall" data-gh-reveal-toggle
                        data-more="… mostrar todos os {{ $ranked->count() }} repos" data-less="mostrar menos">… mostrar todos os {{ $ranked->count() }} repos</button>
            @endif
        </section>

        <div class="gh-sort">
            <span class="gh-sort__label">ordenar</span>
            @foreach(['updated' => 'modificado', 'name' => 'nome', 'created' => 'criado'] as $key => $label)
                @php
                    $isActive = $sortKey === $key;
                    $nextDir = $isActive ? ($sortDir === 'desc' ? 'asc' : 'desc') : ($key === 'name' ? 'asc' : 'desc');
                    $arrow = $isActive ? ($sortDir === 'desc' ? ' ↓' : ' ↑') : '';
                @endphp
                <a href="{{ route('admin.github-view.index', ['sort' => $key.'_'.$nextDir]) }}"
                   class="gh-sort__opt {{ $isActive ? 'is-active' : '' }}">{{ $label }}{{ $arrow }}</a>
            @endforeach
        </div>

        <section class="gh-grid">
            @foreach($repositories as $repo)
                @include('admin.github-view._card', ['repo' => $repo, 'stats' => $overview->for($repo), 'chipDays' => $chipDays])
            @endforeach
        </section>
    @endif
@endsection

@push('scripts')
<script defer src="{{ asset('js/admin/github-view/dashboard.js') }}"></script>
@endpush
