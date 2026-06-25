@extends('layouts.app')

@section('title', 'GitHub Desktop para Linux')
@section('description', 'GitHub Desktop — o cliente Git visual e open-source da GitHub (Electron, TypeScript, React) compilado e empacotado em .deb para Linux.')

@section('content')

    <section class="dark include-header" style="background-color: #0d0d14; min-height: 100vh; position: relative; overflow: hidden;">
        <div class="cp-hero-glow"></div>

        <div class="container" style="position: relative; z-index: 1; padding-top: 60px; padding-bottom: 100px; max-width: 860px;">

            <nav style="margin-bottom: 32px; font-family: 'JetBrains Mono', monospace; font-size: 0.78rem;">
                <a href="{{ route('home') }}" style="color:#6366f1; text-decoration:none;"><i class="fa-solid fa-arrow-left me-2"></i>Início</a>
            </nav>

            <header style="display:flex; align-items:flex-start; gap:18px; margin-bottom: 26px;">
                <span style="width:58px;height:58px;border-radius:14px;background:rgba(99,102,241,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="fa-brands fa-github" style="color:#6366f1;font-size:1.6rem;"></i>
                </span>
                <div>
                    <span class="cp-eyebrow">// APLICATIVO DESKTOP</span>
                    <h1 style="font-family: 'Inter', sans-serif; font-size: 2.2rem; font-weight: 700; color: #f1f5f9; letter-spacing: -0.02em; margin: 4px 0 0;">GitHub Desktop <span style="color:#6366f1;">para Linux</span></h1>
                </div>
            </header>

            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-bottom: 32px; font-family: 'JetBrains Mono', monospace; font-size: 0.7rem;">
                @foreach(['Electron', 'TypeScript', 'React', '.deb / Debian'] as $tech)
                    <span style="color:#a5b4fc; background:rgba(99,102,241,0.1); border:1px solid rgba(99,102,241,0.2); border-radius:6px; padding:4px 11px;">{{ $tech }}</span>
                @endforeach
            </div>

            <div style="font-family: 'Inter', sans-serif; font-size: 1.02rem; color: #cbd5e1; line-height: 1.8; margin-bottom: 36px;">
                <p>O <strong style="color:#f1f5f9;">GitHub Desktop</strong> é o cliente Git visual e open-source da GitHub — construído em <strong style="color:#f1f5f9;">Electron</strong> e escrito em <strong style="color:#f1f5f9;">TypeScript</strong> com <strong style="color:#f1f5f9;">React</strong>. Ele deixa o dia a dia com Git mais simples: commits, branches, histórico, pull requests e resolução de conflitos numa interface limpa, sem precisar decorar comandos.</p>
                <p style="margin-top: 1rem;">Oficialmente, a GitHub <strong style="color:#f1f5f9;">não distribui o app para Linux</strong>. Este projeto é um build da comunidade que compila o GitHub Desktop a partir do código-fonte e o empacota como <strong style="color:#f1f5f9;">.deb</strong>, pronto pra instalar em Debian, Ubuntu e derivados.</p>
            </div>

            <div class="row g-3" style="margin-bottom: 42px;">
                @php
                    $features = [
                        ['fa-solid fa-code-commit', 'Commits visuais', 'Stage, commit, branches e merges sem linha de comando.'],
                        ['fa-solid fa-code-compare', 'Diff lado a lado', 'Veja exatamente o que mudou antes de confirmar.'],
                        ['fa-solid fa-code-pull-request', 'Pull requests', 'Crie e acompanhe PRs e o status dos checks.'],
                        ['fa-solid fa-box-open', 'Pacote .deb', 'Instalação nativa em Debian, Ubuntu e derivados.'],
                    ];
                @endphp
                @foreach($features as [$icon, $title, $desc])
                    <div class="col-md-6">
                        <div class="cp-glass-card h-100" style="padding: 22px;">
                            <i class="{{ $icon }}" style="color:#6366f1; font-size:1.15rem;"></i>
                            <h3 style="font-family:'Inter',sans-serif; font-size:1rem; font-weight:600; color:#f1f5f9; margin:12px 0 6px;">{{ $title }}</h3>
                            <p style="font-family:'Inter',sans-serif; font-size:0.85rem; color:#94a3b8; line-height:1.6; margin:0;">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex gap-3 flex-wrap">
                <a href="{{ route('downloads') }}" class="button button-rounded button-large m-0" style="background:#6366f1; border-color:#6366f1; color:#fff; font-family:'Inter',sans-serif; font-weight:600; padding:14px 30px; box-shadow:0 4px 24px rgba(99,102,241,0.35);">
                    <i class="fa-solid fa-download me-2"></i>Baixar (.deb)
                </a>
                <a href="https://github.com/samirhvbr/GITHUB_DESKTOP" target="_blank" rel="noopener" class="button button-rounded button-large button-border m-0" style="border-color:rgba(99,102,241,0.45); color:#a5b4fc; font-family:'Inter',sans-serif; font-weight:600; padding:14px 30px;">
                    <i class="fa-brands fa-github me-2"></i>Código no GitHub
                </a>
            </div>

            <p style="margin-top: 30px; font-family:'JetBrains Mono',monospace; font-size:0.72rem; color:#64748b; line-height:1.7;">
                <i class="fa-solid fa-circle-info me-1" style="color:#6366f1;"></i>
                Projeto da comunidade, sem vínculo oficial com a GitHub, Inc. As marcas citadas pertencem aos respectivos donos.
            </p>

        </div>
    </section>

@endsection
