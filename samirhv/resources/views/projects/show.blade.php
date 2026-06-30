@extends('layouts.app')

@section('title', $project->title)
@section('description', Str::limit($project->description, 150) ?: 'Download de '.$project->title)

@push('styles')
<style>
    .os-tab{ font-family:'Inter',sans-serif; font-size:.85rem; font-weight:600; color:#94a3b8; background:#11111c; border:1px solid rgba(99,102,241,0.15); border-radius:9px; padding:8px 16px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:.15s; }
    .os-tab:hover:not(:disabled){ color:#e2e8f0; border-color:rgba(99,102,241,0.4); }
    .os-tab.is-active{ color:#fff; background:rgba(99,102,241,0.15); border-color:#6366f1; }
    .os-tab:disabled{ opacity:.45; cursor:not-allowed; }
    .os-tab .cnt{ font-family:'JetBrains Mono',monospace; font-size:.68rem; color:#64748b; background:#0a0a12; border-radius:5px; padding:1px 7px; }
    .os-tab.is-active .cnt{ color:#c7d2fe; }
    .dl-badge{ font-family:'JetBrains Mono',monospace; font-size:.64rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em; color:#c7d2fe; background:rgba(99,102,241,0.12); border-radius:5px; padding:2px 7px; }
    .dl-badge-arch{ color:#94a3b8; background:rgba(148,163,184,0.1); }
    .dl-meta{ font-family:'JetBrains Mono',monospace; font-size:.68rem; color:#64748b; }
    .dl-copy{ font-family:'JetBrains Mono',monospace; font-size:.68rem; color:#818cf8; background:none; border:none; cursor:pointer; padding:0; }
    .dl-copy:hover{ color:#a5b4fc; }
    .dl-copy.copied{ color:#34d399; }
    .dl-btn{ background:#6366f1; border-color:#6366f1; color:#fff; font-family:'Inter',sans-serif; font-weight:600; font-size:.82rem; padding:9px 18px; flex-shrink:0; }
    .dl-btn-off{ display:inline-block; background:#1f2937; color:#6b7280; cursor:not-allowed; border-radius:999px; padding:9px 18px; font-family:'Inter',sans-serif; font-weight:600; font-size:.82rem; flex-shrink:0; }
    .toggle-older{ font-family:'JetBrains Mono',monospace; font-size:.74rem; color:#94a3b8; background:none; border:none; cursor:pointer; padding:10px 0; display:flex; align-items:center; gap:8px; }
    .toggle-older:hover{ color:#c7d2fe; }
    .toggle-older i{ transition:transform .15s; }
    .toggle-older.open i{ transform:rotate(90deg); }
    @media (max-width:560px){ .os-tabs{ overflow-x:auto; } .dl-btn, .dl-btn-off{ padding:8px 14px; } }
</style>
@endpush

@section('content')

    <section class="dark include-header" style="background-color: #0d0d14; min-height: 100vh; position: relative; overflow: hidden;">
        <div class="cp-hero-glow"></div>

        <div class="container" style="position: relative; z-index: 1; padding-top: 60px; padding-bottom: 100px; max-width: 860px;">

            <nav style="margin-bottom: 32px; font-family: 'JetBrains Mono', monospace; font-size: 0.78rem;">
                <a href="{{ route('downloads') }}" style="color:#6366f1; text-decoration:none;"><i class="fa-solid fa-arrow-left me-2"></i>Downloads</a>
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

            <header style="display:flex; align-items:flex-start; gap:18px; margin-bottom: 22px;">
                @if($project->icon)
                    <span style="width:58px;height:58px;border-radius:14px;background:rgba(99,102,241,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="{{ $project->icon }}" style="color:#6366f1;font-size:1.5rem;"></i></span>
                @endif
                <div style="min-width:0;">
                    @if($project->category)
                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.66rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: #6366f1;">// {{ $project->category }}</span>
                    @endif
                    <h1 style="font-family: 'Inter', sans-serif; font-size: 2.2rem; font-weight: 700; color: #f1f5f9; letter-spacing: -0.02em; margin: 4px 0 0;">{{ $project->title }}</h1>

                    @if($download['has_any'])
                        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-top:10px; font-family:'JetBrains Mono',monospace; font-size:.72rem; color:#94a3b8;">
                            @foreach($available as $os)
                                <span style="display:inline-flex; align-items:center; gap:5px;"><span aria-hidden="true" style="width:7px;height:7px;border-radius:50%;background:#6366f1;display:inline-block;"></span>{{ \App\Support\OsDetector::label($os) }}</span>
                            @endforeach
                            @if($latestGroup && $latestGroup['version'])<span>· v{{ $latestGroup['version'] }}</span>@endif
                            @if($latestGroup && $latestGroup['date'])<span>· {{ $latestGroup['date']->translatedFormat('d M Y') }}</span>@endif
                            @if($allArches->isNotEmpty())<span>· {{ $allArches->implode('·') }}</span>@endif
                        </div>
                    @endif
                </div>
            </header>

            @if($project->description)
                <div style="font-family: 'Inter', sans-serif; font-size: 1.02rem; color: #cbd5e1; line-height: 1.8; margin-bottom: 34px; white-space: pre-line;">{{ $project->description }}</div>
            @endif

            @if(session('download_unavailable'))
                <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; border-radius: 10px; padding: 14px 18px; font-family: 'Inter', sans-serif; font-size: 0.9rem; margin-bottom: 24px;">
                    O arquivo <strong>{{ session('download_unavailable') }}</strong> está indisponível no momento.
                </div>
            @endif

            @if(! $download['has_any'])
                <p style="font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: #64748b;">// sem arquivos disponíveis ainda</p>
            @else

                {{-- ─── Recomendado para você ─── --}}
                @php($rec = $download['recommended'])
                <div style="background:#0f0f1a; border:1px solid rgba(99,102,241,0.25); border-radius:14px; padding:18px; margin-bottom:34px;">
                    <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                        <span style="font-family:'JetBrains Mono',monospace; font-size:.66rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:#6366f1;">
                            Recomendado para você · {{ \App\Support\OsDetector::label($rec['os']) }}@if($rec['file'] && $rec['file']->arch) ({{ $rec['file']->arch }})@endif
                        </span>
                        <a href="#arquivos" style="margin-left:auto; font-family:'JetBrains Mono',monospace; font-size:.7rem; color:#94a3b8; text-decoration:none;">trocar de sistema ↓</a>
                    </div>

                    @if($rec['fallback_note'])
                        <div style="background:rgba(234,179,8,0.08); border:1px solid rgba(234,179,8,0.25); color:#fde68a; border-radius:8px; padding:9px 13px; font-family:'Inter',sans-serif; font-size:.82rem; margin-bottom:12px;">{{ $rec['fallback_note'] }}</div>
                    @endif

                    @if($rec['file'])
                        @include('partials.download-file', ['file' => $rec['file']])
                        @if($rec['file']->install_command['install'])
                            @php($cmd = $rec['file']->install_command['install'])
                            <div style="margin-top:12px; background:#0a0a12; border:1px solid rgba(99,102,241,0.15); border-radius:10px; padding:12px 14px; display:flex; align-items:center; gap:10px;">
                                <code style="flex:1; min-width:0; font-family:'JetBrains Mono',monospace; font-size:.78rem; color:#a5b4fc; overflow-x:auto; white-space:nowrap;">$ {{ $cmd }}</code>
                                <button type="button" class="dl-copy" data-copy="{{ $cmd }}" title="Copiar comando" style="flex-shrink:0;">copiar ⧉</button>
                            </div>
                        @endif
                    @else
                        <div style="font-family:'Inter',sans-serif; color:#94a3b8; font-size:.9rem;">Nenhum build disponível ainda.</div>
                    @endif
                </div>

                {{-- ─── Arquivos (abas por SO) ─── --}}
                <h2 id="arquivos" style="font-family: 'Inter', sans-serif; font-size: 1.15rem; font-weight: 700; color: #f1f5f9; margin-bottom: 16px;">Arquivos</h2>

                <div class="os-tabs" role="tablist" aria-label="Sistemas operacionais" style="display:flex; gap:8px; margin-bottom:18px; flex-wrap:wrap;">
                    @foreach(\App\Support\OsDetector::OSES as $os)
                        @php($tab = $download['tabs'][$os])
                        <button type="button" role="tab" id="tab-{{ $os }}" aria-controls="panel-{{ $os }}" aria-selected="{{ $os === $download['default_os'] ? 'true' : 'false' }}" class="os-tab{{ $os === $download['default_os'] ? ' is-active' : '' }}" data-os-tab="{{ $os }}" @if($tab['count'] === 0) disabled @endif>
                            {{ $tab['label'] }}
                            <span class="cnt">{{ $tab['count'] > 0 ? $tab['count'] : 'em breve' }}</span>
                        </button>
                    @endforeach
                </div>

                @foreach(\App\Support\OsDetector::OSES as $os)
                    @php($tab = $download['tabs'][$os])
                    <div id="panel-{{ $os }}" role="tabpanel" aria-labelledby="tab-{{ $os }}" data-os-panel="{{ $os }}" @if($os !== $download['default_os']) style="display:none" @endif>
                        @if($tab['count'] === 0)
                            <p style="font-family:'JetBrains Mono',monospace; font-size:.8rem; color:#64748b;">// build de {{ $tab['label'] }} em breve</p>
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

                <div style="margin-top:28px; padding-top:18px; border-top:1px solid rgba(99,102,241,0.1); font-family:'JetBrains Mono',monospace; font-size:.72rem; color:#64748b; line-height:1.9;">
                    Confira a integridade após baixar — Linux/macOS: <span style="color:#94a3b8;">sha256sum arquivo</span> · Windows: <span style="color:#94a3b8;">Get-FileHash .\arquivo -Algorithm SHA256</span>
                </div>

            @endif

        </div>
    </section>

@endsection

@push('scripts')
<script>
(function () {
    // Trocar de aba de SO (a aba default já vem renderizada do servidor).
    document.querySelectorAll('.os-tab[data-os-tab]').forEach(function (tab) {
        tab.addEventListener('click', function () {
            if (tab.disabled) return;
            var os = tab.getAttribute('data-os-tab');
            document.querySelectorAll('.os-tab[data-os-tab]').forEach(function (t) {
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
