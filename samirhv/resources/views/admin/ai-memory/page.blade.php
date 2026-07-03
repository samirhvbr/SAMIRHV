@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Página')

@section('topbar-actions')
    <a href="{{ route('admin.ai-memory.pages') }}" class="admin-btn admin-btn-sm"><i class="fa-solid fa-arrow-left"></i> Páginas</a>
@endsection

@push('styles')
<style>
    .md-body{line-height:1.65;color:#dbe2ea;font-size:.92rem;max-width:820px}
    .md-body h1,.md-body h2,.md-body h3,.md-body h4{color:#f1f5f9;margin:1.4em 0 .5em;line-height:1.25}
    .md-body h1{font-size:1.5rem}.md-body h2{font-size:1.25rem}.md-body h3{font-size:1.08rem}
    .md-body p{margin:.6em 0}
    .md-body a{color:#a5b4fc;text-decoration:underline}
    .md-body ul,.md-body ol{margin:.6em 0;padding-left:1.5em}
    .md-body li{margin:.25em 0}
    .md-body code{font-family:'JetBrains Mono',monospace;font-size:.82rem;background:var(--panel-2);border:1px solid var(--line);border-radius:5px;padding:1px 5px;color:#a5b4fc}
    .md-body pre{background:var(--panel-2);border:1px solid var(--line);border-radius:9px;padding:14px 16px;overflow-x:auto;margin:.8em 0}
    .md-body pre code{background:none;border:none;padding:0;color:#cbd5e1}
    .md-body blockquote{border-left:3px solid var(--accent);margin:.8em 0;padding:.2em 0 .2em 14px;color:var(--muted)}
    .md-body table{border-collapse:collapse;margin:.8em 0;font-size:.85rem}
    .md-body th,.md-body td{border:1px solid var(--line);padding:6px 10px;text-align:left}
    .md-body img{max-width:100%}
</style>
@endpush

@section('content')
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $T = \App\Services\AiMemory\AiMemoryTime::class;
            $tierBadge = ['working' => 'badge-warn', 'episodic' => 'badge-accent', 'semantic' => 'badge-ok', 'procedural' => 'badge-muted'];
            $frontmatter = trim((string) $page->frontmatter_json);
        @endphp

        <div class="an-grid-2" style="grid-template-columns:1fr 300px;align-items:start">
            {{-- Conteúdo --}}
            <div class="admin-card">
                <h2 style="margin-bottom:6px">{{ $page->title }}</h2>
                <div style="overflow-x:auto;margin-bottom:8px"><code style="color:#a5b4fc">{{ $page->path }}</code></div>
                <div style="margin-bottom:18px">
                    <span class="badge {{ $tierBadge[$page->tier] ?? 'badge-muted' }}">{{ $page->tier }}</span>
                    @if($page->is_latest)
                        <span class="badge badge-ok">versão atual</span>
                    @else
                        <span class="badge badge-warn">versão antiga</span>
                    @endif
                    @if($page->pinned)<span class="badge badge-accent">fixada</span>@endif
                </div>

                <div class="md-body">{!! \Illuminate\Support\Str::markdown($page->body, ['html_input' => 'escape', 'allow_unsafe_links' => false]) !!}</div>
            </div>

            {{-- Metadados + histórico --}}
            <div>
                <div class="admin-card">
                    <h2>Metadados</h2>
                    <ul class="an-list">
                        <li><span class="card-sub">Projeto</span><span>{{ $page->project }}</span></li>
                        <li><span class="card-sub">Workspace</span><span>{{ $page->workspace }}</span></li>
                        <li><span class="card-sub">Autor</span><span>{{ $page->author ?? '—' }}</span></li>
                        <li><span class="card-sub">Criada</span><span>{{ $T::format($page->created_at) }}</span></li>
                        <li><span class="card-sub">Atualizada</span><span>{{ $T::format($page->updated_at) }}</span></li>
                    </ul>
                    @if($frontmatter !== '' && $frontmatter !== '{}')
                        <p class="card-sub" style="margin:14px 0 6px">Frontmatter</p>
                        <pre style="background:var(--panel-2);border:1px solid var(--line);border-radius:8px;padding:10px;overflow-x:auto;font-size:.75rem;color:#cbd5e1">{{ $frontmatter }}</pre>
                    @endif
                </div>

                <div class="admin-card">
                    <h2>Histórico <span class="card-sub">({{ count($history) }})</span></h2>
                    <ul class="an-list">
                        @foreach($history as $v)
                            <li>
                                <span>
                                    @if($v->id_hex === $page->id_hex)
                                        <b>{{ $T::format($v->created_at, 'd/m/Y H:i') }}</b>
                                    @else
                                        <a href="{{ route('admin.ai-memory.pages.show', $v->id_hex) }}">{{ $T::format($v->created_at, 'd/m/Y H:i') }}</a>
                                    @endif
                                </span>
                                <span>@if($v->is_latest)<span class="badge badge-ok">atual</span>@endif</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endunless
@endsection
