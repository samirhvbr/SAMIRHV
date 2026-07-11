@extends('layouts.app')

@section('title', 'Samirhv')
@section('description', 'Projetos e ferramentas de Samir Hanna Verza disponibilizados para download.')

@section('content'

    {{-- ═══ HERO ═══ --}}
    <section class="s-hero">
        <div class="s-aura"></div>
        <svg class="s-hero__dots" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" aria-hidden="true">
            <defs>
                <pattern id="s-dots" x="0" y="0" width="26" height="26" patternUnits="userSpaceOnUse">
                    <circle cx="1.5" cy="1.5" r="1.3" fill="#6366f1"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#s-dots)"/>
        </svg>
        <!-- Glow secundário (canto inferior esquerdo) -->
        <div style="position: absolute; bottom: -15%; left: -8%; width: 420px; height: 420px; background: radial-gradient(circle, rgba(99,102,241,0.05) 0%, transparent 62%); pointer-events: none; z-index: 0;"></div>

        <div class="container" style="position:relative; z-index:1;">
            <div class="row align-items-center g-5">

                <div class="col-lg-6 s-reveal" data-d="1">
                    <span class="s-kicker">Central de projetos</span>
                    <h1 class="s-display">Software que eu construo,<br>pronto pra você baixar.</h1>
                    <p class="s-lead" style="margin-top: 1.5rem;">
                        Um espaço pessoal de Samir Hanna Verza para organizar e disponibilizar o que desenvolvo —
                        apps desktop, ferramentas de linha de comando e produtos como o <strong style="color:var(--s-ink);font-weight:600;">ShvIA</strong> e o
                        <strong style="color:var(--s-ink);font-weight:600;">SShvTerm</strong>. Sempre na versão mais recente.
                    </p>
                    <div class="d-flex gap-3 flex-wrap" style="margin-top: 2.2rem;">
                        <a href="{{ route('downloads') }}" class="s-btn s-btn--lg">
                            <i class="fa-solid fa-download"></i> Ver downloads
                        </a>
                        <a href="https://github.com/samirhvbr" target="_blank" rel="noopener" class="s-btn s-btn--ghost s-btn--lg">
                            <i class="fa-brands fa-github"></i> GitHub
                        </a>
                    </div>
                    <p class="s-hero__trust" style="margin-top: 1.8rem;">
                        <span class="dot"></span> samirhv.com.br · Laravel · Debian · open-source
                    </p>
                </div>

                <div class="col-lg-6 s-reveal" data-d="2">
                    <div class="s-term" role="img" aria-label="Exemplo do agente de IA do SShvTerm operando um terminal SSH: propõe um comando, pede aprovação e executa.">
                        <div class="s-term__bar">
                            <span class="s-term__dot s-term__dot--r"></span>
                            <span class="s-term__dot s-term__dot--y"></span>
                            <span class="s-term__dot s-term__dot--g"></span>
                            <span class="s-term__title">deploy@prod-01 — SShvTerm</span>
                        </div>
                        <div class="s-term__body">
                            <div><span class="c-key">agente ▸</span> preciso liberar espaço em /var — o que está pesado ali?</div>
                            <div><span class="c-cmt"># o agente propõe; você mantém o controle</span></div>
                            <div><span class="c-prompt">$</span> du -sh /var/* | sort -h | tail -2</div>
                            <div><span class="c-dim">1.2G</span>&nbsp;&nbsp;/var/log</div>
                            <div><span class="c-dim">3.4G</span>&nbsp;&nbsp;/var/lib/docker</div>
                            <div><span class="c-key">ask ▸</span> sudo journalctl --vacuum-size=200M &nbsp;<span class="c-cmt">[permitir]</span></div>
                            <div><span class="c-prompt">$</span> sudo journalctl --vacuum-size=200M</div>
                            <div><span class="c-ok">&#10003;</span> 980M liberados · /var/log agora em 220M</div>
                        </div>
                        <div class="s-term__foot">
                            <i class="fa-solid fa-bolt"></i> política <span style="color:var(--s-ok)">allow</span> · <span style="color:var(--s-warn)">ask</span> · <span style="color:var(--s-danger)">deny</span> — no seu terminal visível
                        </div>
                    </div>
                </div>

            </div>

            <div class="s-techrow s-reveal" data-d="3" style="margin-top: clamp(2.5rem, 5vw, 4rem); padding-top: 1.5rem; border-top: 1px solid var(--s-line);">
                <span><i class="fa-brands fa-linux"></i> Linux</span>
                <span><i class="fa-solid fa-server"></i> Debian</span>
                <span><i class="fa-brands fa-laravel"></i> Laravel</span>
                <span><i class="fa-brands fa-php"></i> PHP</span>
                <span><i class="fa-brands fa-rust"></i> Rust</span>
                <span><i class="fa-brands fa-docker"></i> Docker</span>
                <span><i class="fa-brands fa-git-alt"></i> Git</span>
                <span><i class="fa-solid fa-database"></i> MariaDB</span>
            </div>
        </div>
    </section>

    {{-- ═══ EM DESTAQUE — o agente do SShvTerm opera o terminal ═══ --}}
    <section class="s-section s-bg-2" style="position: relative;">
        <div style="position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 500px; height: 250px; background: radial-gradient(ellipse at center top, rgba(99,102,241,0.04) 0%, transparent 65%); pointer-events: none;"></div>

        <div class="container" style="position: relative;">
            <div class="row g-5 align-items-center">

                <div class="col-lg-6 s-reveal" data-d="1">
                    <h2 class="s-h2">A IA agora opera o<br>terminal — com política.</h2>
                    <p class="s-lead" style="margin-top: 1.25rem;">
                        No <strong style="color:var(--s-ink);font-weight:600;">SShvTerm</strong>, um agente de IA propõe e executa comandos
                        no seu terminal SSH <em style="color:var(--s-ink-2);font-style:normal;">visível</em> — nada de caixa-preta. Você decide
                        quanto de autonomia dar, subindo em degraus.
                    </p>
                    <ul class="s-points" style="margin-top: 2.2rem;">
                        <li>
                            <span class="n"><i class="fa-solid fa-terminal"></i></span>
                            <span><strong>Digita no terminal real</strong><span class="d">O agente executa no PTY visível da aba — você vê cada comando entrar, como se tivesse digitado.</span></span>
                        </li>
                        <li>
                            <span class="n"><i class="fa-solid fa-shield-halved"></i></span>
                            <span><strong>Política allow · ask · deny</strong><span class="d">Comando confiável roda, perigoso é bloqueado, o resto pergunta. A escada L0→L3 é o próprio modelo de segurança.</span></span>
                        </li>
                        <li>
                            <span class="n"><i class="fa-solid fa-key"></i></span>
                            <span><strong>BYO-key, no seu cliente</strong><span class="d">Sua chave de API fica no cliente. Zero-knowledge preservado: o servidor nunca vê a saída dos seus hosts.</span></span>
                        </li>
                    </ul>
                    <div class="d-flex gap-3 flex-wrap" style="margin-top: 2.2rem;">
                        <a href="https://sshvterm.com" target="_blank" rel="noopener" class="s-btn">
                            Conhecer o SShvTerm <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-6 s-reveal" data-d="2">
                    <div class="s-panel s-card--pad" style="padding: clamp(1.4rem, 2.4vw, 1.9rem);">
                        <div class="d-flex align-items-center gap-2" style="margin-bottom: 1.15rem;">
                            <i class="fa-solid fa-sliders" style="color: var(--s-accent-ink-2); font-size: 0.95rem;"></i>
                            <span class="s-meta" style="color: var(--s-ink-2);">settings.json — política de comandos</span>
                        </div>
                        <div class="s-stack" style="gap: 11px; font-family: var(--s-mono); font-size: 0.82rem; line-height: 1.55;">
                            <div style="display:flex; gap:13px; align-items:baseline;">
                                <span style="color: var(--s-ok); font-weight: 600; width: 54px; flex-shrink: 0;">allow</span>
                                <span style="color: var(--s-ink-2);">ls, cat, git status, systemctl status</span>
                            </div>
                            <div class="s-divider"></div>
                            <div style="display:flex; gap:13px; align-items:baseline;">
                                <span style="color: var(--s-warn); font-weight: 600; width: 54px; flex-shrink: 0;">ask</span>
                                <span style="color: var(--s-ink-2);">sudo *, systemctl restart *, docker *</span>
                            </div>
                            <div class="s-divider"></div>
                            <div style="display:flex; gap:13px; align-items:baseline;">
                                <span style="color: var(--s-danger); font-weight: 600; width: 54px; flex-shrink: 0;">deny</span>
                                <span style="color: var(--s-ink-2);">rm -rf /*, mkfs*, dd of=/dev/*</span>
                            </div>
                        </div>
                        <p class="s-meta" style="margin: 1.3rem 0 0; line-height: 1.65;">
                            <i class="fa-solid fa-lock" style="color: var(--s-accent-ink-2); margin-right: 6px;"></i>
                            A fronteira instrução-vs-dado trata a saída do terminal como <span style="color: var(--s-ink-2);">dado</span>, nunca como comando.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ═══ PROJETOS ═══ --}}
    <section class="s-section" id="projetos" style="position: relative;">
        <div style="position: absolute; top: 60px; right: -10%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(99,102,241,0.04) 0%, transparent 62%); pointer-events: none;"></div>

        <div class="container" style="position: relative;">

            <div style="max-width: 640px; margin-bottom: clamp(2.2rem, 4vw, 3.2rem);" class="s-reveal" data-d="1">
                <h2 class="s-h2">Projetos</h2>
                <p class="s-lead s-muted" style="margin-top: 0.75rem;">Disponíveis para baixar e usar — sempre na versão mais recente.</p>
            </div>

            @if($projects->isNotEmpty())
                @php $featured = $projects->first(); $rest = $projects->slice(1); @endphp

                {{-- Destaque: primeiro projeto, em linha horizontal --}}
                @php
                    $fLink = $featured->redirectsToSite(); $fDocs = $featured->hasCustomPage();
                    $fAction = $fLink ? 'Visitar site' : ($fDocs ? 'Abrir' : 'Ver downloads');
                @endphp
                <a href="{{ $featured->public_url }}" @if($fLink) target="_blank" rel="noopener" @endif class="s-card s-card--hover s-card--pad s-featured s-reveal" data-d="2" style="margin-bottom: 20px; text-decoration: none;">
                    <span class="s-icon s-icon--lg"><i class="{{ $featured->icon ?: 'fa-solid fa-box-open' }}"></i></span>
                    <div style="min-width: 0;">
                        <div class="d-flex align-items-center gap-2 flex-wrap" style="margin-bottom: 9px;">
                            <h3 class="s-h3" style="font-size: 1.38rem;">{{ $featured->title }}</h3>
                            @if($featured->category)<span class="s-tag s-tag--accent">{{ $featured->category }}</span>@endif
                        </div>
                        <p class="s-body s-muted" style="margin: 0; font-size: 0.95rem; max-width: 60ch;">{{ Str::limit($featured->description, 165) }}</p>
                    </div>
                    <span class="s-btn s-btn--ghost s-btn--sm" style="pointer-events:none; align-self:center;">
                        {{ $fAction }} <i class="fa-solid {{ $fLink ? 'fa-arrow-up-right-from-square' : 'fa-arrow-right' }}"></i>
                    </span>
                </a>

                {{-- Restante em grade --}}
                @if($rest->isNotEmpty())
                <div class="s-grid s-reveal" data-d="3">
                    @foreach($rest as $project)
                        @php
                            $isLink = $project->redirectsToSite(); $isDocs = $project->hasCustomPage();
                            if ($isLink)      { $mIcon='fa-arrow-up-right-from-square'; $mText='site externo'; }
                            elseif ($isDocs)  { $mIcon='fa-book'; $mText='documentação'; }
                            else              { $mIcon='fa-download'; $mText=number_format($project->downloads_total ?? 0,0,',','.').' downloads'; }
                        @endphp
                        <a href="{{ $project->public_url }}" @if($isLink) target="_blank" rel="noopener" @endif class="s-card s-card--hover s-card--pad s-stack" style="text-decoration: none;">
                            <div class="d-flex align-items-center gap-2" style="margin-bottom: 14px;">
                                <span class="s-icon"><i class="{{ $project->icon ?: 'fa-solid fa-box-open' }}"></i></span>
                                @if($project->category)<span class="s-tag">{{ $project->category }}</span>@endif
                            </div>
                            <h3 class="s-h3" style="font-size: 1.12rem; margin-bottom: 8px;">{{ $project->title }}</h3>
                            <p class="s-body s-muted" style="font-size: 0.88rem; margin: 0; flex-grow: 1;">{{ Str::limit($project->description, 100) }}</p>
                            <div class="d-flex align-items-center justify-content-between" style="margin-top: 16px; padding-top: 14px; border-top: 1px solid var(--s-line);">
                                <span class="s-meta"><i class="fa-solid {{ $mIcon }}" style="margin-right: 5px;"></i>{{ $mText }}</span>
                                <span class="s-meta s-accent-ink">abrir <i class="fa-solid fa-arrow-right" style="font-size: 0.7rem; transition: transform 0.2s;"></i></span>
                            </div>
                        </a>
                    @endforeach
                </div>
                @endif

                <div style="margin-top: clamp(2.2rem, 4vw, 3rem); text-align: center;" class="s-reveal" data-d="4">
                    <a href="{{ route('downloads') }}" class="s-btn s-btn--ghost">
                        Ver todos os downloads <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            @else
                <div class="s-card s-card--pad s-reveal" data-d="2" style="text-align: center; padding: 70px 0;">
                    <span class="s-meta" style="font-size: 0.85rem;">Nenhum projeto publicado ainda — em breve.</span>
                </div>
            @endif

        </div>
    </section>

@endsection
