@extends('layouts.app')

@section('title', $project->title)
@section('description', Str::limit($project->description, 150) ?: 'Download de '.$project->title)

@push('styles')
<style>
    .dl-file{ display:flex; align-items:center; gap:14px; background:var(--s-surface); border:1px solid var(--s-line); border-radius:10px; padding:13px 16px; }
    .dl-name{ font-family:var(--s-mono); font-size:.82rem; color:var(--s-ink); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .dl-badge{ font-family:var(--s-mono); font-size:.64rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em; color:var(--s-accent-ink-2); background:var(--s-accent-soft); border:1px solid var(--s-line-2); border-radius:5px; padding:2px 7px; }
    .dl-badge-arch{ color:var(--s-muted); background:rgba(150,155,185,0.08); border-color:var(--s-line); }
    .dl-meta{ font-family:var(--s-mono); font-size:.68rem; color:var(--s-muted); }
    .dl-copy{ font-family:var(--s-mono); font-size:.68rem; color:var(--s-accent-ink-2); background:none; border:none; cursor:pointer; padding:0; }
    .dl-copy:hover{ color:var(--s-ink-2); }
    .dl-copy.copied{ color:var(--s-ok); }
    .dl-btn-off{ display:inline-block; background:var(--s-surface-2); color:var(--s-faint); cursor:not-allowed; border-radius:var(--s-r-sm); padding:9px 16px; font-family:var(--s-sans); font-weight:600; font-size:.82rem; flex-shrink:0; }
    .toggle-older{ font-family:var(--s-mono); font-size:.74rem; color:var(--s-muted); background:none; border:none; cursor:pointer; padding:10px 0; display:flex; align-items:center; gap:8px; }
    .toggle-older:hover{ color:var(--s-ink-2); }
    .toggle-older i{ transition:transform .15s; }
    .toggle-older.open i{ transform:rotate(90deg); }
    @media (max-width:560px){ .s-ostabs{ overflow-x:auto; } }
</style>
@endpush

@section('content')

    <section class="s-page-hero">
        <div class="s-aura"></div>
        <div class="container s-project-shell" style="position:relative; z-index:1;">

            <nav style="margin-bottom:30px;">
                <a href="{{ route('downloads') }}" class="s-meta s-backlink"><i class="fa-solid fa-arrow-left"></i>Downloads</a>
            </nav>

            @php
                $available = $download['available'];
                $primaryOs = $download['default_os'];
                $primaryVersions = $download['tabs'][$primaryOs]['versions'] ?? [];
                $latestGroup = collect($primaryVersions)->firstWhere('is_latest', true) ?? ($primaryVersions[0] ?? null);
                $allArches = collect($download['tabs'])
                    ->flatMap(fn ($t) => collect($t['versions'])->flatMap(fn ($g) => $g['files']))
                    ->map(fn ($f) => $f->arch)->filter()->unique()->values();
            @endphp

            <header class="s-project-header">
                @if($project->icon)
                    <span class="s-icon s-icon--lg"><i class="{{ $project->icon }}"></i></span>
                @endif
                <div style="min-width:0;">
                    @if($project->category)
                        <span class="s-tag s-tag--accent" style="margin-bottom:10px;">{{ $project->category }}</span>
                    @endif
                    <h1 class="s-display" style="font-size:clamp(1.9rem,4vw,2.7rem); margin-top:8px;">{{ $project->title }}</h1>

                    @if($download['has_any'])
                        <div class="s-meta d-flex align-items-center flex-wrap" style="gap:8px 12px; margin-top:12px;">
                            @foreach($available as $os)
                                <span class="d-inline-flex align-items-center" style="gap:5px;"><span aria-hidden="true" style="width:6px;height:6px;border-radius:50%;background:var(--s-accent);display:inline-block;"></span>{{ \App\Support\OsDetector::label($os) }}</span>
                            @endforeach
                            @if($latestGroup && $latestGroup['version'])<span>· v{{ $latestGroup['version'] }}</span>@endif
                            @if($latestGroup && $latestGroup['date'])<span>· {{ $latestGroup['date']->translatedFormat('d M Y') }}</span>@endif
                            @if($allArches->isNotEmpty())<span>· {{ $allArches->implode('·') }}</span>@endif
                        </div>
                    @endif
                </div>
            </header>

            @if($project->description)
                <div class="s-project-description">{{ $project->description }}</div>
            @endif

            {{-- Híbrido: as opções ficam lado a lado quando site e app coexistem. --}}
            @if($project->external_url)
                <div class="s-access-options">
                    <div class="s-panel s-access-option">
                        <span class="s-access-option__eyebrow">Versão online</span>
                        <p class="s-access-option__copy">Use direto no navegador, sem instalar e sempre na última versão.</p>
                        <a href="{{ $project->external_url }}" target="_blank" rel="noopener" class="s-btn s-btn--sm">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i> Usar online
                        </a>
                    </div>
                    @if($download['has_any'])
                        <div class="s-panel s-access-option">
                            <span class="s-access-option__eyebrow">Aplicativo desktop</span>
                            <p class="s-access-option__copy">Baixe um build nativo para o seu sistema e trabalhe localmente.</p>
                            <a href="#arquivos" class="s-btn s-btn--ghost s-btn--sm"><i class="fa-solid fa-download"></i> Ver downloads</a>
                        </div>
                    @endif
                </div>
            @endif

            @if(session('download_unavailable'))
                <div class="s-card" style="padding:14px 18px; margin-bottom:24px; border-color:rgba(248,113,113,0.3); background:rgba(248,113,113,0.08);">
                    <span class="s-body" style="color:#fca5a5; font-size:0.9rem;">O arquivo <strong>{{ session('download_unavailable') }}</strong> está indisponível no momento.</span>
                </div>
            @endif

            @if(! $download['has_any'])
                <p class="s-meta">Sem arquivos disponíveis ainda.</p>
            @else

                {{-- ─── Recomendado para você ─── --}}
                @php($rec = $download['recommended'])
                <div class="s-panel s-download-recommendation">
                    <div class="s-download-recommendation__head">
                        <div>
                            <span class="s-meta" style="color:var(--s-accent-ink-2); text-transform:uppercase; letter-spacing:.06em;">Download recomendado</span>
                            <h2 class="s-download-recommendation__title">Baixar para {{ \App\Support\OsDetector::label($rec['os']) }}@if($rec['file'] && $rec['file']->arch) · {{ $rec['file']->arch }}@endif</h2>
                        </div>
                        <a href="#arquivos" class="s-meta s-backlink">trocar de sistema <i class="fa-solid fa-arrow-down"></i></a>
                    </div>

                    @if($rec['fallback_note'])
                        <div style="background:rgba(245,179,1,0.08); border:1px solid rgba(245,179,1,0.25); color:#fde68a; border-radius:8px; padding:9px 13px; margin-bottom:12px;" class="s-body">{{ $rec['fallback_note'] }}</div>
                    @endif

                    @if($rec['file'])
                        @include('partials.download-file', ['file' => $rec['file']])
                        @if($rec['file']->install_command['install'])
                            @php($cmd = $rec['file']->install_command['install'])
                            <div style="margin-top:12px; background:#0a0a12; border:1px solid var(--s-line); border-radius:10px; padding:12px 14px; display:flex; align-items:center; gap:10px;">
                                <code style="flex:1; min-width:0; font-family:var(--s-mono); font-size:.78rem; color:var(--s-accent-ink-2); overflow-x:auto; white-space:nowrap;">$ {{ $cmd }}</code>
                                <button type="button" class="dl-copy" data-copy="{{ $cmd }}" title="Copiar comando" style="flex-shrink:0;">copiar ⧉</button>
                            </div>
                        @endif
                    @else
                        <div class="s-body s-muted" style="font-size:.9rem;">Nenhum build disponível ainda.</div>
                    @endif
                </div>

                {{-- ─── Arquivos (abas por SO) ─── --}}
                <h2 id="arquivos" class="s-h3" style="font-size:1.15rem; margin-bottom:16px;">Arquivos</h2>

                <div class="s-ostabs" role="tablist" aria-label="Sistemas operacionais" style="margin-bottom:18px;">
                    @foreach(\App\Support\OsDetector::OSES as $os)
                        @php($tab = $download['tabs'][$os])
                        <button type="button" role="tab" id="tab-{{ $os }}" aria-controls="panel-{{ $os }}" aria-selected="{{ $os === $download['default_os'] ? 'true' : 'false' }}" class="s-ostab{{ $os === $download['default_os'] ? ' is-active' : '' }}" data-os-tab="{{ $os }}" @if($tab['count'] === 0) disabled @endif>
                            {{ $tab['label'] }}
                            <span class="cnt">{{ $tab['count'] > 0 ? $tab['count'] : 'em breve' }}</span>
                        </button>
                    @endforeach
                </div>

                @foreach(\App\Support\OsDetector::OSES as $os)
                    @php($tab = $download['tabs'][$os])
                    <div id="panel-{{ $os }}" role="tabpanel" aria-labelledby="tab-{{ $os }}" data-os-panel="{{ $os }}" @if($os !== $download['default_os']) style="display:none" @endif>
                        @if($tab['count'] === 0)
                            <p class="s-meta">Build de {{ $tab['label'] }} em breve.</p>
                        @else
                            @foreach($tab['versions'] as $group)
                                @if($group['is_latest'])
                                    @include('partials.download-version', ['group' => $group])
                                @endif
                            @endforeach

                            @php($olderCount = max(0, count($tab['versions']) - 1))
                            @if($olderCount > 0)
                                <button type="button" class="toggle-older" data-older-os="{{ $os }}" aria-expanded="false">
                                    <i class="fa-solid fa-chevron-right" aria-hidden="true"></i> Versões anteriores ({{ $olderCount }})
                                </button>
                                <div data-older-wrap="{{ $os }}" style="display:none;">
                                    @foreach($tab['versions'] as $group)
                                        @if(! $group['is_latest'])
                                            @include('partials.download-version', ['group' => $group])
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                @endforeach

                <div class="s-meta" style="margin-top:28px; padding-top:18px; border-top:1px solid var(--s-line); line-height:1.9;">
                    Confira a integridade após baixar — Linux/macOS: <span style="color:var(--s-ink-2);">sha256sum arquivo</span> · Windows: <span style="color:var(--s-ink-2);">Get-FileHash .\arquivo -Algorithm SHA256</span>
                </div>

            @endif

        </div>
    </section>

@endsection

@push('scripts')
<script>
(function () {
    // Trocar de aba de SO (a aba default já vem renderizada do servidor).
    document.querySelectorAll('.s-ostab[data-os-tab]').forEach(function (tab) {
        tab.addEventListener('click', function () {
            if (tab.disabled) return;
            var os = tab.getAttribute('data-os-tab');
            document.querySelectorAll('.s-ostab[data-os-tab]').forEach(function (t) {
                var isActive = (t === tab);
                t.classList.toggle('is-active', isActive);
                t.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });
            document.querySelectorAll('[data-os-panel]').forEach(function (p) {
                p.style.display = (p.getAttribute('data-os-panel') === os) ? '' : 'none';
            });
        });
    });

    // Recolher/expandir versões anteriores.
    document.querySelectorAll('.toggle-older[data-older-os]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var os = btn.getAttribute('data-older-os');
            var wrap = document.querySelector('[data-older-wrap="' + os + '"]');
            if (!wrap) return;
            var show = wrap.style.display === 'none';
            wrap.style.display = show ? '' : 'none';
            btn.classList.toggle('open', show);
            btn.setAttribute('aria-expanded', show ? 'true' : 'false');
        });
    });

    // Copiar sha256 / comando de instalação.
    document.querySelectorAll('.dl-copy[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var text = btn.getAttribute('data-copy');
            if (!navigator.clipboard) return;
            navigator.clipboard.writeText(text).then(function () {
                var original = btn.textContent;
                btn.classList.add('copied');
                btn.textContent = 'copiado ✓';
                setTimeout(function () { btn.classList.remove('copied'); btn.textContent = original; }, 1400);
            });
        });
    });
})();
</script>
@endpush
