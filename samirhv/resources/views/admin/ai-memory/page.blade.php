@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Página')

@section('topbar-actions')
    <a href="{{ route('admin.ai-memory.pages') }}" class="admin-btn admin-btn-sm"><i class="fa-solid fa-arrow-left"></i> Páginas</a>
@endsection

@section('content')
<div class="aim">
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $T = \App\Services\AiMemory\AiMemoryTime::class;
            $tierChip = ['working' => 'aim-chip--warn', 'episodic' => 'aim-chip--accent', 'semantic' => 'aim-chip--ok'];
            $frontmatter = trim((string) $page->frontmatter_json);
            $hasFrontmatter = $frontmatter !== '' && $frontmatter !== '{}';
        @endphp

        <header class="aim-hero">
            <div>
                <h1>{{ $page->title }}</h1>
                <div class="aim-hero__chips">
                    <span class="aim-chip {{ $tierChip[$page->tier] ?? '' }}">{{ $page->tier }}</span>
                    @if($page->is_latest)
                        <span class="aim-chip aim-chip--ok">versão atual</span>
                    @else
                        <span class="aim-chip aim-chip--warn">versão antiga</span>
                    @endif
                    @if($page->pinned)<span class="aim-chip aim-chip--accent">fixada</span>@endif
                </div>
                <code class="aim-hero__path">{{ $page->path }}</code>
            </div>
        </header>

        @unless($page->is_latest)
            <div class="admin-alert admin-alert-warn">
                <i class="fa-solid fa-clock-rotate-left"></i>
                Você está lendo uma versão antiga desta página. A versão atual está no fim do histórico, à direita.
            </div>
        @endunless

        <div class="aim-grid2" style="grid-template-columns:minmax(0,1fr) 320px">
            <section class="admin-card aim-card">
                @if(trim((string) $page->body) === '')
                    <p class="aim-empty">Página sem corpo.</p>
                @else
                    <div class="aim-prose">
                        {!! \Illuminate\Support\Str::markdown($page->body, ['html_input' => 'escape', 'allow_unsafe_links' => false]) !!}
                    </div>
                @endif
            </section>

            <div>
                <section class="admin-card aim-card">
                    <header class="aim-card__head"><div><h2>Metadados</h2></div></header>
                    <dl class="aim-facts">
                        <div><dt>Projeto</dt><dd>{{ $page->project }}</dd></div>
                        <div><dt>Workspace</dt><dd>{{ $page->workspace }}</dd></div>
                        <div><dt>Autor</dt><dd class="aim-mono">{{ $page->author ?? '—' }}</dd></div>
                        <div><dt>Criada</dt><dd class="aim-mono">{{ $T::format($page->created_at) }}</dd></div>
                        <div><dt>Atualizada</dt><dd class="aim-mono">{{ $T::format($page->updated_at) }}</dd></div>
                    </dl>

                    @if($hasFrontmatter)
                        <p class="aim-sub" style="margin:16px 0 6px">Frontmatter</p>
                        <pre class="aim-prose" style="margin:0"><code>{{ $frontmatter }}</code></pre>
                    @endif
                </section>

                <section class="admin-card aim-card">
                    <header class="aim-card__head">
                        <div>
                            <h2>Histórico</h2>
                            <p class="aim-sub">{{ count($history) }} {{ count($history) === 1 ? 'versão' : 'versões' }}</p>
                        </div>
                    </header>

                    @if(count($history) <= 1)
                        <p class="aim-empty">Primeira e única versão — ainda não foi reescrita.</p>
                    @else
                        <ul class="aim-tl">
                            @foreach($history as $v)
                                <li>
                                    <div class="aim-tl__time">{{ $T::format($v->created_at, 'd/m/Y H:i') }}</div>
                                    <div class="aim-tl__row">
                                        @if($v->id_hex === $page->id_hex)
                                            <span class="aim-strong" style="font-size:.88rem">esta versão</span>
                                        @else
                                            <a href="{{ route('admin.ai-memory.pages.show', $v->id_hex) }}">abrir</a>
                                        @endif
                                        @if($v->is_latest)<span class="aim-chip aim-chip--ok">atual</span>@endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            </div>
        </div>
    @endunless
</div>
@endsection
