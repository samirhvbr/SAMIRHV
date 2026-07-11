@extends('layouts.app')

@section('title', 'ai-usagebar')
@section('description', 'Monitor de uso dos seus planos de IA (Claude, Codex, Z.AI, OpenRouter, DeepSeek) na barra do sistema — Linux, macOS e Windows. Como instalar em cada SO.')

@push('styles')
<style>
    /* ── Tipografia de seção ─────────────────────────────────────── */
    .aub-h2{ font-family:var(--s-sans); font-size:1.35rem; font-weight:700; color:#f1f5f9; letter-spacing:-.01em; margin:0 0 6px; }
    .aub-lead{ font-family:var(--s-sans); font-size:.95rem; color:#94a3b8; line-height:1.7; margin:0 0 22px; }
    .aub-h3{ font-family:var(--s-sans); font-size:1.02rem; font-weight:600; color:#e2e8f0; margin:26px 0 10px; display:flex; align-items:center; gap:9px; }
    .aub-h3 i{ color:#6366f1; font-size:.95rem; }

    /* ── Blocos de comando com copiar ────────────────────────────── */
    .aub-cmd{ position:relative; margin:0 0 14px; }
    .aub-code{ margin:0; background:#0a0a12; border:1px solid rgba(99,102,241,0.16); border-radius:11px; padding:15px 54px 15px 16px; overflow-x:auto; }
    .aub-code code{ font-family:'JetBrains Mono',monospace; font-size:.8rem; line-height:1.75; color:#c7d2fe; white-space:pre; background:none; padding:0; }
    .aub-code .cmt{ color:#5b6478; }
    .aub-code .prompt{ color:#6366f1; user-select:none; }
    .aub-copy{ position:absolute; top:9px; right:9px; font-family:'JetBrains Mono',monospace; font-size:.66rem; color:#818cf8; background:#12121c; border:1px solid rgba(99,102,241,0.22); border-radius:7px; padding:4px 9px; cursor:pointer; transition:.15s; }
    .aub-copy:hover{ color:#c7d2fe; border-color:rgba(99,102,241,0.5); }
    .aub-copy.copied{ color:#34d399; border-color:rgba(52,211,153,0.5); }

    /* ── Callout ─────────────────────────────────────────────────── */
    .aub-note{ display:flex; gap:11px; background:rgba(99,102,241,0.07); border:1px solid rgba(99,102,241,0.2); border-radius:11px; padding:13px 16px; margin:0 0 16px; font-family:var(--s-sans); font-size:.86rem; color:#cbd5e1; line-height:1.6; }
    .aub-note i{ color:#6366f1; margin-top:2px; flex-shrink:0; }
    .aub-note.amber{ background:rgba(234,179,8,0.07); border-color:rgba(234,179,8,0.25); color:#fde68a; }
    .aub-note.amber i{ color:#eab308; }

    /* ── Screenshots ─────────────────────────────────────────────── */
    .aub-shots{ display:grid; grid-template-columns:repeat(auto-fit,minmax(240px,1fr)); gap:16px; margin:8px 0 6px; }
    .aub-shot{ margin:0; }
    .aub-shot img{ width:100%; height:auto; border-radius:12px; border:1px solid rgba(99,102,241,0.18); background:#0a0a12; display:block; box-shadow:0 8px 30px rgba(0,0,0,0.4); }
    .aub-shot figcaption{ font-family:'JetBrains Mono',monospace; font-size:.68rem; color:#64748b; margin-top:8px; line-height:1.5; }

    /* ── Abas por SO ─────────────────────────────────────────────── */
    .os-tabs{ display:flex; gap:8px; margin:0 0 22px; flex-wrap:wrap; }
    .os-tab{ font-family:var(--s-sans); font-size:.85rem; font-weight:600; color:#94a3b8; background:#11111c; border:1px solid rgba(99,102,241,0.15); border-radius:9px; padding:9px 18px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; transition:.15s; }
    .os-tab:hover{ color:#e2e8f0; border-color:rgba(99,102,241,0.4); }
    .os-tab.is-active{ color:#fff; background:rgba(99,102,241,0.15); border-color:#6366f1; }
    .os-panel{ animation:aubFade .2s ease; }
    @keyframes aubFade{ from{opacity:0; transform:translateY(4px);} to{opacity:1; transform:none;} }
    @media (max-width:560px){ .os-tabs{ overflow-x:auto; } }
</style>
{{-- Sem JS: mostra todas as seções de SO e esconde as abas (nada fica inacessível). --}}
<noscript><style>.os-panel{ display:block !important; } .os-tabs{ display:none !important; } .os-panel + .os-panel{ margin-top:40px; padding-top:34px; border-top:1px solid rgba(99,102,241,0.12); }</style></noscript>
@endpush

@section('content')

    <section class="s-section" style="padding-top:clamp(7rem,11vw,10rem); position:relative; overflow:hidden;">
        <div class="s-aura"></div>

        <div class="container s-prose" style="position:relative; z-index:1; max-width:880px;">

            <nav style="margin-bottom:32px; font-family:'JetBrains Mono',monospace; font-size:.78rem;">
                <a href="{{ route('home') }}" style="color:#6366f1; text-decoration:none;"><i class="fa-solid fa-arrow-left me-2"></i>Início</a>
            </nav>

            {{-- ── Cabeçalho ────────────────────────────────────────────── --}}
            <header style="display:flex; align-items:flex-start; gap:18px; margin-bottom:24px;">
                <span style="width:58px; height:58px; border-radius:14px; background:rgba(99,102,241,0.12); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa-solid fa-gauge-high" style="color:#6366f1; font-size:1.6rem;"></i>
                </span>
                <div>
                    <span class="s-kicker">Monitor de uso de IA</span>
                    <h1 style="font-family:var(--s-sans); font-size:2.2rem; font-weight:700; color:#f1f5f9; letter-spacing:-.02em; margin:4px 0 0;">ai<span style="color:#6366f1;">-</span>usagebar</h1>
                    <p style="margin:9px 0 0; font-family:var(--s-sans); font-size:.9rem; color:#94a3b8; line-height:1.5;">
                        Projeto de <a href="https://github.com/akitaonrails/ai-usagebar" target="_blank" rel="noopener" style="color:#818cf8; text-decoration:none; font-weight:600;">Fabio Akita</a>.
                        As integrações de desktop (GNOME · macOS · Windows) mostradas aqui são contribuições do <a href="https://github.com/samirhvbr/ai-usagebar" target="_blank" rel="noopener" style="color:#818cf8; text-decoration:none;">fork do Samir</a>.
                    </p>
                </div>
            </header>

            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom:30px; font-family:'JetBrains Mono',monospace; font-size:.7rem;">
                @foreach(['Rust', 'Waybar', 'GNOME Shell', 'macOS menu bar', 'Windows tray', 'TUI', 'MIT'] as $tech)
                    <span style="color:#a5b4fc; background:rgba(99,102,241,0.1); border:1px solid rgba(99,102,241,0.2); border-radius:6px; padding:4px 11px;">{{ $tech }}</span>
                @endforeach
            </div>

            {{-- ── Intro ────────────────────────────────────────────────── --}}
            <div style="font-family:var(--s-sans); font-size:1.02rem; color:#cbd5e1; line-height:1.8; margin-bottom:30px;">
                <p>O <strong style="color:#f1f5f9;">ai-usagebar</strong> mostra o quanto você já consumiu dos seus planos de IA — <strong style="color:#f1f5f9;">Anthropic Claude</strong>, <strong style="color:#f1f5f9;">OpenAI Codex</strong>, <strong style="color:#f1f5f9;">Z.AI (GLM)</strong>, <strong style="color:#f1f5f9;">OpenRouter</strong> e <strong style="color:#f1f5f9;">DeepSeek</strong> — direto na barra do seu sistema, com as barras de uso da <strong style="color:#f1f5f9;">janela de 5 horas</strong> e <strong style="color:#f1f5f9;">semanal</strong> ao lado do relógio e um menu com o detalhamento completo.</p>
                <p style="margin-top:1rem;">É um <strong style="color:#f1f5f9;">backend rápido em Rust</strong> com quatro interfaces: o widget <strong style="color:#f1f5f9;">Waybar</strong> e um <strong style="color:#f1f5f9;">TUI de terminal</strong> (multiplataforma), a extensão de <strong style="color:#f1f5f9;">GNOME Shell</strong> (Linux), o app de <strong style="color:#f1f5f9;">menu bar</strong> (macOS) e o app de <strong style="color:#f1f5f9;">bandeja</strong> (Windows). Todas leem o mesmo <code style="color:#a5b4fc;">--json</code> do binário — a lógica de autenticação e de cada provedor mora só no Rust auditável.</p>
            </div>

            <figure class="aub-shot" style="margin:0 0 40px;">
                <img src="{{ asset('img/projects/ai-usagebar/linux-1.png') }}" alt="Painel do GNOME mostrando as barras de uso do Claude com o menu suspenso aberto" loading="lazy">
                <figcaption>Barras de uso ao lado do relógio + menu com Sessão / Semanal / Sonnet / Uso extra.</figcaption>
            </figure>

            {{-- ── Recursos ─────────────────────────────────────────────── --}}
            <h2 class="aub-h2">Recursos</h2>
            <div class="row g-3" style="margin:0 0 44px;">
                @php
                    $features = [
                        ['fa-solid fa-layer-group', 'Multi-provedor', 'Claude, Codex, Z.AI, OpenRouter e DeepSeek num só lugar.'],
                        ['fa-solid fa-window-maximize', 'UI nativa por SO', 'Waybar/GNOME no Linux, menu bar no macOS, bandeja no Windows.'],
                        ['fa-solid fa-terminal', 'TUI multiplataforma', '`ai-usagebar-tui` roda em qualquer terminal, até por SSH.'],
                        ['fa-solid fa-arrows-rotate', 'Auto-refresh', 'Atualiza a cada 60s no app; cache com flock evita rate-limit.'],
                        ['fa-solid fa-shield-halved', 'Sem reimplementar auth', 'Lê o OAuth que o `claude`/`codex` já gravaram; chaves por env/config.'],
                        ['fa-solid fa-feather', 'Drop-in claudebar', 'Mesmos flags e placeholders do claudebar original, em Rust.'],
                    ];
                @endphp
                @foreach($features as [$icon, $title, $desc])
                    <div class="col-md-6">
                        <div class="s-card h-100" style="padding:20px;">
                            <i class="{{ $icon }}" style="color:#6366f1; font-size:1.1rem;"></i>
                            <h3 style="font-family:var(--s-sans); font-size:.98rem; font-weight:600; color:#f1f5f9; margin:11px 0 5px;">{{ $title }}</h3>
                            <p style="font-family:var(--s-sans); font-size:.84rem; color:#94a3b8; line-height:1.6; margin:0;">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- ── Passo 0: autenticação ────────────────────────────────── --}}
            <h2 class="aub-h2">Antes de tudo: entre uma vez no provedor</h2>
            <p class="aub-lead">O ai-usagebar não pede login próprio — ele lê as credenciais que os CLIs oficiais já gravam. Para o Claude, rode o <strong style="color:#e2e8f0;">Claude Code</strong> uma vez; o token se renova sozinho depois.</p>
            <div class="aub-cmd">
                <pre class="aub-code"><code><span class="prompt">$</span> claude        <span class="cmt"># Linux/Windows → ~/.claude/.credentials.json  ·  macOS → Keychain (lido sozinho)</span>
<span class="prompt">$</span> codex login   <span class="cmt"># OpenAI Codex → ~/.codex/auth.json</span></code></pre>
                <button class="aub-copy" type="button">copiar ⧉</button>
            </div>
            <div class="aub-note">
                <i class="fa-solid fa-circle-info"></i>
                <span>Z.AI, OpenRouter e DeepSeek usam <strong>chave de API</strong> (variável de ambiente <code>ZAI_API_KEY</code> / <code>OPENROUTER_API_KEY</code> / <code>DEEPSEEK_API_KEY</code>, ou inline no <code>~/.config/ai-usagebar/config.toml</code>). Dá pra configurar direto pela aba <strong>Vendors</strong> das UIs.</span>
            </div>

            {{-- ── Instalação por SO ────────────────────────────────────── --}}
            <h2 class="aub-h2" style="margin-top:44px;">Como instalar</h2>
            <p class="aub-lead">Escolha o seu sistema. Os comandos são os mesmos do repositório oficial.</p>

            <div class="os-tabs" role="tablist" aria-label="Sistema operacional">
                <button type="button" class="os-tab is-active" data-os-tab="linux"><i class="fa-brands fa-linux"></i> Linux</button>
                <button type="button" class="os-tab" data-os-tab="macos"><i class="fa-brands fa-apple"></i> macOS</button>
                <button type="button" class="os-tab" data-os-tab="windows"><i class="fa-brands fa-windows"></i> Windows</button>
            </div>

            {{-- ─────────── LINUX ─────────── --}}
            <div class="os-panel" data-os-panel="linux">

                <h3 class="aub-h3"><i class="fa-solid fa-download"></i> 1. Instale o binário</h3>
                <p class="aub-lead">No <strong style="color:#e2e8f0;">Arch</strong>, use o AUR. Em <strong style="color:#e2e8f0;">outras distros</strong>, use o crates.io (precisa de <code>rustup</code>, ou <code>cargo-binstall</code> para baixar pronto).</p>
                <div class="aub-cmd">
                    <pre class="aub-code"><code><span class="cmt"># Arch (AUR) — escolha um:</span>
<span class="prompt">$</span> yay -S ai-usagebar-bin     <span class="cmt"># binário pronto das Releases (~5s)</span>
<span class="prompt">$</span> yay -S ai-usagebar         <span class="cmt"># compila do fonte (~30-60s)</span>

<span class="cmt"># Outras distros (crates.io):</span>
<span class="prompt">$</span> cargo install ai-usagebar  <span class="cmt"># compila do fonte (precisa de rustup)</span>
<span class="prompt">$</span> cargo binstall ai-usagebar <span class="cmt"># baixa o binário pronto (precisa de cargo-binstall)</span></code></pre>
                    <button class="aub-copy" type="button">copiar ⧉</button>
                </div>
                <p class="aub-lead">Instala <code>ai-usagebar</code> + <code>ai-usagebar-tui</code>. Teste na hora:</p>
                <div class="aub-cmd">
                    <pre class="aub-code"><code><span class="prompt">$</span> ai-usagebar --vendor anthropic --pretty   <span class="cmt"># deve imprimir as barras</span>
<span class="prompt">$</span> ai-usagebar-tui                            <span class="cmt"># TUI com abas — funciona sozinho, sem Waybar</span></code></pre>
                    <button class="aub-copy" type="button">copiar ⧉</button>
                </div>

                <h3 class="aub-h3"><i class="fa-brands fa-gnome"></i> 2a. Extensão de GNOME Shell</h3>
                <div class="aub-cmd">
                    <pre class="aub-code"><code><span class="prompt">$</span> cd gnome-extension
<span class="prompt">$</span> ./install.sh          <span class="cmt"># symlink em ~/.local/share + compila o schema GSettings</span>
<span class="cmt"># Recarregue o GNOME Shell: FAÇA LOGOUT / LOGIN (não use gnome-shell --replace)</span>
<span class="prompt">$</span> gnome-extensions enable ai-usagebar@akitaonrails.github.io
<span class="prompt">$</span> gnome-extensions prefs  ai-usagebar@akitaonrails.github.io   <span class="cmt"># barras, cores, login de Vendors</span></code></pre>
                    <button class="aub-copy" type="button">copiar ⧉</button>
                </div>
                <div class="aub-shots">
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/linux-3.png') }}" alt="Preferências do GNOME — aba Vendors" loading="lazy"><figcaption>Preferências → aba Vendors (login/config por provedor).</figcaption></figure>
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/linux-4.png') }}" alt="Barras no painel do GNOME" loading="lazy"><figcaption>As barras aparecem no painel superior, ao lado do relógio.</figcaption></figure>
                </div>

                <h3 class="aub-h3"><i class="fa-solid fa-bars-staggered"></i> 2b. Widget do Waybar (Wayland)</h3>
                <p class="aub-lead">Um módulo só, com scroll para alternar entre provedores e clique para abrir o TUI. Adicione ao seu <code>~/.config/waybar/config</code>:</p>
                <div class="aub-cmd">
                    <pre class="aub-code"><code>"custom/aibar": {
    "exec": "ai-usagebar --format '{vendor_short} {session_pct}% · {session_reset}'",
    "return-type": "json",
    "interval": 300,
    "signal": 13,
    "tooltip": true,
    "on-click": "ai-usagebar-tui",
    "on-scroll-up":   "ai-usagebar --cycle-next",
    "on-scroll-down": "ai-usagebar --cycle-prev"
}</code></pre>
                    <button class="aub-copy" type="button">copiar ⧉</button>
                </div>
                <div class="aub-note amber">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>Mantenha <code>interval: 300</code>. Os endpoints da Anthropic e da OpenAI são não-documentados e aplicam rate-limit abaixo de ~300s. O cache interno de 60s deixa várias telas conviverem sem estourar a API.</span>
                </div>
                <div class="aub-shots">
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/waybar.png') }}" alt="Widget do Waybar mostrando o uso do Claude com o tooltip" loading="lazy"><figcaption>Widget no Waybar com o tooltip de detalhamento (Pango).</figcaption></figure>
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/tui-openai.png') }}" alt="TUI mostrando a aba da OpenAI" loading="lazy"><figcaption>`ai-usagebar-tui` — abas por provedor (aqui, OpenAI Codex).</figcaption></figure>
                </div>

            </div>

            {{-- ─────────── macOS ─────────── --}}
            <div class="os-panel" data-os-panel="macos" style="display:none;">

                <h3 class="aub-h3"><i class="fa-solid fa-list-check"></i> 1. Pré-requisitos</h3>
                <div class="aub-cmd">
                    <pre class="aub-code"><code><span class="prompt">$</span> xcode-select --install     <span class="cmt"># Command Line Tools (para o swiftc)</span>
<span class="prompt">$</span> cargo install ai-usagebar   <span class="cmt"># backend em ~/.cargo/bin (precisa de rustup)</span>
<span class="prompt">$</span> claude                      <span class="cmt"># login uma vez — credenciais vão pro Keychain (lido sozinho)</span></code></pre>
                    <button class="aub-copy" type="button">copiar ⧉</button>
                </div>

                <h3 class="aub-h3"><i class="fa-solid fa-hammer"></i> 2. Compile e rode o app de menu bar</h3>
                <div class="aub-cmd">
                    <pre class="aub-code"><code><span class="prompt">$</span> git clone https://github.com/samirhvbr/ai-usagebar.git
<span class="prompt">$</span> cd ai-usagebar/macos
<span class="prompt">$</span> ./build.sh                  <span class="cmt"># swiftc -O → ./ai-usagebar-menubar (sem projeto do Xcode)</span>
<span class="prompt">$</span> ./ai-usagebar-menubar &      <span class="cmt"># aparece na menu bar (sem ícone no Dock)</span></code></pre>
                    <button class="aub-copy" type="button">copiar ⧉</button>
                </div>

                <h3 class="aub-h3"><i class="fa-solid fa-power-off"></i> 3. (Opcional) iniciar no login</h3>
                <div class="aub-cmd">
                    <pre class="aub-code"><code><span class="prompt">$</span> ./install-agent.sh          <span class="cmt"># LaunchAgent com RunAtLoad — volta a cada login</span></code></pre>
                    <button class="aub-copy" type="button">copiar ⧉</button>
                </div>
                <div class="aub-note">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Abra as <strong>Preferências</strong> pela menu bar (ou <strong>⌘,</strong>): barras, cores por severidade, provedor e intervalo — aplicam ao vivo. A menu bar roda no macOS 10.15+; a janela de Preferências precisa do macOS 12+.</span>
                </div>
                <div class="aub-shots">
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/macosx-1.jpeg') }}" alt="Menu suspenso do app na barra de menus do macOS" loading="lazy"><figcaption>Menu suspenso na menu bar — Sessão / Semanal / Sonnet / Extra.</figcaption></figure>
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/macosx-2.jpeg') }}" alt="Preferências do app no macOS com cores e Vendors" loading="lazy"><figcaption>Preferências — cores por severidade e seção Vendors.</figcaption></figure>
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/macosx-3.jpeg') }}" alt="Detalhe das preferências do app no macOS" loading="lazy"><figcaption>Ajustes de barras, provedor e intervalo.</figcaption></figure>
                </div>

            </div>

            {{-- ─────────── WINDOWS ─────────── --}}
            <div class="os-panel" data-os-panel="windows" style="display:none;">

                <h3 class="aub-h3"><i class="fa-solid fa-list-check"></i> 1. Pré-requisitos</h3>
                <p class="aub-lead">Precisa do <strong style="color:#e2e8f0;">toolchain Rust</strong> e do <strong style="color:#e2e8f0;">.NET 8 SDK</strong>:</p>
                <div class="aub-cmd">
                    <pre class="aub-code"><code><span class="prompt">></span> winget install Rustlang.Rustup
<span class="prompt">></span> winget install Microsoft.DotNet.SDK.8</code></pre>
                    <button class="aub-copy" type="button">copiar ⧉</button>
                </div>

                <h3 class="aub-h3"><i class="fa-solid fa-hammer"></i> 2. Compile e rode o app de bandeja</h3>
                <div class="aub-cmd">
                    <pre class="aub-code"><code><span class="prompt">></span> git clone https://github.com/samirhvbr/ai-usagebar.git
<span class="prompt">></span> cd ai-usagebar
<span class="prompt">></span> cargo build --release                    <span class="cmt"># → target\release\ai-usagebar.exe</span>
<span class="prompt">></span> cd windows-tray
<span class="prompt">></span> dotnet build -c Debug                     <span class="cmt"># rápido (usa o runtime instalado)</span>
<span class="prompt">></span> start "" "bin\Debug\net8.0-windows\ai-usagebar-tray.exe"</code></pre>
                    <button class="aub-copy" type="button">copiar ⧉</button>
                </div>
                <div class="aub-note">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Procure o pontinho colorido na <strong>bandeja</strong> (clique no <code>^</code> para mostrar ícones ocultos): clique esquerdo → painel, direito → menu (Refresh, <strong>seletor de Vendor</strong>, iniciar com o Windows…). As credenciais ficam em <code>%USERPROFILE%\.claude\.credentials.json</code> / <code>%USERPROFILE%\.codex\auth.json</code> — rode o <code>claude</code>/<code>codex</code> uma vez para gerá-las.</span>
                </div>
                <div class="aub-note amber">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>O widget do <strong>Waybar é exclusivo do Wayland</strong> e não se aplica ao Windows. Já o <code>ai-usagebar-tui</code> e o <code>ai-usagebar --json/--pretty</code> rodam nativamente. Para um pacote portátil (sem instalar o .NET): <code>dotnet publish -c Release</code>.</span>
                </div>
                <div class="aub-shots">
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/panel-anthropic.png') }}" alt="Painel do app de bandeja no Windows mostrando o uso do Claude" loading="lazy"><figcaption>Painel da bandeja (clique esquerdo) com o uso do Claude.</figcaption></figure>
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/vendor-menu.png') }}" alt="Menu de seleção de provedor do app de bandeja no Windows" loading="lazy"><figcaption>Menu (clique direito) — seletor de Vendor.</figcaption></figure>
                    <figure class="aub-shot"><img src="{{ asset('img/projects/ai-usagebar/tray-tooltip.png') }}" alt="Tooltip do ícone da bandeja no Windows" loading="lazy"><figcaption>Tooltip do ícone da bandeja.</figcaption></figure>
                </div>
                <p class="aub-lead" style="margin-top:14px; font-size:.82rem;"><i class="fa-solid fa-hands-clapping" style="color:#6366f1; margin-right:6px;"></i>App de bandeja do Windows por <a href="https://github.com/EaeDave/ai-usagebar" target="_blank" rel="noopener" style="color:#818cf8;">EaeDave</a> (MIT), incluído aqui com crédito.</p>

            </div>

            {{-- ── CTA ──────────────────────────────────────────────────── --}}
            <div class="d-flex gap-3 flex-wrap" style="margin-top:44px;">
                <a href="https://github.com/akitaonrails/ai-usagebar" target="_blank" rel="noopener" class="button button-rounded button-large m-0" style="background:#6366f1; border-color:#6366f1; color:#fff; font-family:var(--s-sans); font-weight:600; padding:14px 30px; box-shadow:0 4px 24px rgba(99,102,241,0.35);">
                    <i class="fa-brands fa-github me-2"></i>Repositório de Fabio Akita
                </a>
                <a href="https://github.com/samirhvbr/ai-usagebar/blob/master/DESKTOP.md" target="_blank" rel="noopener" class="button button-rounded button-large button-border m-0" style="border-color:rgba(99,102,241,0.45); color:#a5b4fc; font-family:var(--s-sans); font-weight:600; padding:14px 30px;">
                    <i class="fa-solid fa-book me-2"></i>Guia desktop (fork)
                </a>
            </div>

            <p style="margin-top:30px; font-family:'JetBrains Mono',monospace; font-size:.72rem; color:#64748b; line-height:1.7;">
                <i class="fa-solid fa-circle-info me-1" style="color:#6366f1;"></i>
                ai-usagebar é um projeto de <a href="https://github.com/akitaonrails/ai-usagebar" target="_blank" rel="noopener" style="color:#818cf8;">Fabio Akita</a> — open-source (MIT), port em Rust do <a href="https://github.com/mryll/claudebar" target="_blank" rel="noopener" style="color:#818cf8;">claudebar</a> com suporte a mais provedores. As integrações nativas de desktop (GNOME/macOS/Windows) mostradas aqui vêm do <a href="https://github.com/samirhvbr/ai-usagebar" target="_blank" rel="noopener" style="color:#818cf8;">fork do Samir</a>. Alguns endpoints são não-documentados; as marcas citadas pertencem aos respectivos donos.
            </p>

        </div>
    </section>

@endsection

@push('scripts')
<script>
(function () {
    // Trocar de aba de SO.
    var tabs = document.querySelectorAll('.os-tab[data-os-tab]');
    var panels = document.querySelectorAll('.os-panel[data-os-panel]');
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var os = tab.getAttribute('data-os-tab');
            tabs.forEach(function (t) { t.classList.toggle('is-active', t === tab); });
            panels.forEach(function (p) {
                p.style.display = (p.getAttribute('data-os-panel') === os) ? '' : 'none';
            });
        });
    });

    // Copiar o comando (lê o texto do bloco irmão, preservando quebras de linha).
    document.querySelectorAll('.aub-copy').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var block = btn.parentElement.querySelector('.aub-code');
            if (!block || !navigator.clipboard) return;
            navigator.clipboard.writeText(block.innerText.trim()).then(function () {
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
