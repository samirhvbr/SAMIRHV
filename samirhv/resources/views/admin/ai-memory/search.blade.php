@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Busca')

@push('styles')
<style>
    .am-search mark{background:rgba(99,102,241,.28);color:#e0e7ff;border-radius:3px;padding:0 2px}
    .am-hit{padding:14px 0;border-bottom:1px solid rgba(99,102,241,.07)}
    .am-hit:last-child{border-bottom:none}
    .am-hit .snip{color:var(--muted);font-size:.85rem;line-height:1.55;margin-top:5px}
</style>
@endpush

@section('content')
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php $tierBadge = ['working' => 'badge-warn', 'episodic' => 'badge-accent', 'semantic' => 'badge-ok', 'procedural' => 'badge-muted']; @endphp

        <div class="admin-card am-search">
            <h2>Busca no conhecimento <span class="card-sub">— índice FTS5 (título + corpo das páginas)</span></h2>

            <form method="GET" action="{{ route('admin.ai-memory.search') }}" class="filters">
                <div class="form-row" style="flex:1;min-width:280px">
                    <label>Pesquisar</label>
                    <input type="text" name="q" value="{{ $q }}" placeholder="ex: autenticação oauth" autofocus>
                </div>
                <button type="submit" class="admin-btn admin-btn-primary">Buscar</button>
            </form>

            @if($q === '')
                <p class="card-sub">Digite um termo para pesquisar nas páginas consolidadas.</p>
            @elseif(empty($results))
                <p class="card-sub">Nenhum resultado para <b>{{ $q }}</b>. (A busca é literal sobre título/corpo — termos que não aparecem no texto não retornam.)</p>
            @else
                <p class="card-sub" style="margin-bottom:6px">{{ count($results) }} resultado(s) para <b>{{ $q }}</b></p>
                @foreach($results as $r)
                    <div class="am-hit">
                        <div>
                            <a href="{{ route('admin.ai-memory.pages.show', $r->id_hex) }}"><b>{{ $r->title }}</b></a>
                            <span class="badge {{ $tierBadge[$r->tier] ?? 'badge-muted' }}">{{ $r->tier }}</span>
                            <span class="card-sub">· {{ $r->project }}</span>
                        </div>
                        <div><code style="font-size:.76rem;color:#818cf8">{{ $r->path }}</code></div>
                        <div class="snip">{!! str_replace(['&lt;&lt;&lt;', '&gt;&gt;&gt;'], ['<mark>', '</mark>'], e($r->snippet)) !!}</div>
                    </div>
                @endforeach
            @endif
        </div>
    @endunless
@endsection
