@extends('layouts.app')

@section('title', 'GitHub Desktop para Linux')
@section('description', 'GitHub Desktop — o cliente Git visual e open-source da GitHub (Electron, TypeScript, React) compilado e empacotado em .deb para Linux.')

@section('content')

    <section class="s-section" style="padding-top:clamp(7rem,11vw,10rem); position:relative;">
        <div class="s-aura"></div>
        <div class="container s-prose" style="position:relative; z-index:1; max-width:820px;">

            <nav style="margin-bottom:30px;">
                <a href="{{ route('home') }}" class="s-meta" style="color:var(--s-accent-ink-2);"><i class="fa-solid fa-arrow-left" style="margin-right:7px;"></i>Início</a>
            </nav>

            <header class="d-flex align-items-start gap-3" style="margin-bottom:24px;">
                <span class="s-icon s-icon--lg"><i class="fa-brands fa-github"></i></span>
                <div>
                    <span class="s-kicker" style="margin-bottom:8px;">Aplicativo desktop</span>
                    <h1 class="s-display" style="font-size:clamp(1.9rem,4vw,2.7rem);">GitHub Desktop <span style="color:var(--s-accent-ink-2);">para Linux</span></h1>
                </div>
            </header>

            <div class="d-flex flex-wrap" style="gap:8px; margin-bottom:32px;">
                @foreach(['Electron', 'TypeScript', 'React', '.deb / Debian'] as $tech)
                    <span class="s-tag">{{ $tech }}</span>
                @endforeach
            </div>

            <div class="s-body" style="color:var(--s-ink-2); line-height:1.8; margin-bottom:36px;">
                <p>O <strong style="color:var(--s-ink);">GitHub Desktop</strong> é o cliente Git visual e open-source da GitHub — construído em <strong style="color:var(--s-ink);">Electron</strong> e escrito em <strong style="color:var(--s-ink);">TypeScript</strong> com <strong style="color:var(--s-ink);">React</strong>. Ele deixa o dia a dia com Git mais simples: commits, branches, histórico, pull requests e resolução de conflitos numa interface limpa, sem precisar decorar comandos.</p>
                <p style="margin-top:1rem;">Oficialmente, a GitHub <strong style="color:var(--s-ink);">não distribui o app para Linux</strong>. Este projeto é um build da comunidade que compila o GitHub Desktop a partir do código-fonte e o empacota como <strong style="color:var(--s-ink);">.deb</strong>, pronto pra instalar em Debian, Ubuntu e derivados.</p>
            </div>

            <div class="s-grid" style="margin-bottom:42px; grid-template-columns:repeat(auto-fit,minmax(240px,1fr));">
                @php
                    $features = [
                        ['fa-solid fa-code-commit', 'Commits visuais', 'Stage, commit, branches e merges sem linha de comando.'],
                        ['fa-solid fa-code-compare', 'Diff lado a lado', 'Veja exatamente o que mudou antes de confirmar.'],
                        ['fa-solid fa-code-pull-request', 'Pull requests', 'Crie e acompanhe PRs e o status dos checks.'],
                        ['fa-solid fa-box-open', 'Pacote .deb', 'Instalação nativa em Debian, Ubuntu e derivados.'],
                    ];
                @endphp
                @foreach($features as [$icon, $title, $desc])
                    <div class="s-card s-card--pad" style="padding:22px;">
                        <i class="{{ $icon }}" style="color:var(--s-accent-ink-2); font-size:1.15rem;"></i>
                        <h3 class="s-h3" style="font-size:1rem; margin:12px 0 6px;">{{ $title }}</h3>
                        <p class="s-body s-muted" style="font-size:0.85rem; margin:0;">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>

            <div class="d-flex gap-3 flex-wrap">
                <a href="{{ route('downloads') }}" class="s-btn s-btn--lg"><i class="fa-solid fa-download"></i> Baixar (.deb)</a>
                <a href="https://github.com/samirhvbr/GITHUB_DESKTOP" target="_blank" rel="noopener" class="s-btn s-btn--ghost s-btn--lg"><i class="fa-brands fa-github"></i> Código no GitHub</a>
            </div>

            <p class="s-meta" style="margin-top:30px; line-height:1.7;">
                <i class="fa-solid fa-circle-info" style="color:var(--s-accent-ink-2); margin-right:5px;"></i>
                Projeto da comunidade, sem vínculo oficial com a GitHub, Inc. As marcas citadas pertencem aos respectivos donos.
            </p>

        </div>
    </section>

@endsection
