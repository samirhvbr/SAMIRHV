@extends('layouts.app')

@section('title', 'Samirhv')
@section('description', 'Projetos e ferramentas de Samir Hanna Verza disponibilizados para download.')

@section('content')

    <!-- Hero -->
    <section id="hero" class="min-vh-100 d-flex align-items-center dark include-header py-5 py-lg-0" style="background-color: #0d0d14; position: relative; overflow: hidden;">

        <!-- Dot Grid -->
        <svg class="cp-dot-grid" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
            <defs>
                <pattern id="cp-dots" x="0" y="0" width="24" height="24" patternUnits="userSpaceOnUse">
                    <circle cx="1.5" cy="1.5" r="1.5" fill="#6366f1"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#cp-dots)"/>
        </svg>

        <div class="cp-hero-glow"></div>

        <div class="container" style="position: relative; z-index: 1; padding-bottom: 80px;">
            <div class="row align-items-center g-5">

                <!-- Coluna esquerda -->
                <div class="col-lg-6">
                    <span class="cp-eyebrow">// PROJETOS & DOWNLOADS</span>
                    <h1 class="cp-hero-h1 mb-4">
                        Ferramentas, código<br><span style="color: #6366f1;">prontos pra baixar.</span>
                    </h1>
                    <p style="font-family: 'Inter', sans-serif; font-size: 1.125rem; color: #94a3b8; line-height: 1.75; max-width: 480px; margin-bottom: 0;">
                        Um espaço pessoal de Samir Hanna Verza para organizar e disponibilizar os projetos que desenvolvo — instaladores, scripts e utilitários, sempre com a versão mais recente.
                    </p>
                    <div class="d-flex gap-3 flex-wrap" style="margin-top: 2rem;">
                        <a href="{{ route('downloads') }}" class="button button-rounded button-large" style="background: #6366f1; border-color: #6366f1; color: #fff; font-family: 'Inter', sans-serif; font-weight: 600; padding: 14px 32px; box-shadow: 0 4px 24px rgba(99,102,241,0.35);">Ver downloads</a>
                        <a href="https://github.com/samirhvbr" target="_blank" rel="noopener" class="button button-rounded button-large button-border" style="border-color: rgba(99,102,241,0.45); color: #a5b4fc; font-family: 'Inter', sans-serif; font-weight: 600; padding: 14px 32px;">
                            <i class="fa-brands fa-github me-2" style="font-size: 0.875rem;"></i>GitHub
                        </a>
                    </div>
                    <p style="margin-top: 1.25rem; font-size: 0.875rem; color: #64748b; font-family: 'JetBrains Mono', monospace;">
                        <span style="color: #22c55e;">&#9679;</span>&nbsp; samirhv.com.br &mdash; feito com Laravel + Canvas
                    </p>
                </div>

                <!-- Coluna direita — Projeto em destaque -->
                <div class="col-lg-6">
                    <div class="cp-terminal-wrapper">
                        <div class="cp-terminal-inner">

                            <!-- Header do card -->
                            <div style="background: #1a1a2e; padding: 12px 18px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid rgba(99,102,241,0.15);">
                                <span style="width:12px;height:12px;border-radius:50%;background:#ff5f57;display:inline-block;flex-shrink:0;"></span>
                                <span style="width:12px;height:12px;border-radius:50%;background:#febc2e;display:inline-block;flex-shrink:0;"></span>
                                <span style="width:12px;height:12px;border-radius:50%;background:#28c840;display:inline-block;flex-shrink:0;"></span>
                                <span style="margin-left: 14px; font-family: 'JetBrains Mono', monospace; font-size: 0.72rem; color: #64748b; letter-spacing: 0.02em;">releases.txt</span>
                                <span style="margin-left: auto; font-family: 'JetBrains Mono', monospace; font-size: 0.68rem; color: #374151; background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.2); border-radius: 4px; padding: 2px 8px;">Download</span>
                            </div>

                            <!-- Conteúdo -->
                            <div style="padding: 24px 26px; font-family: 'JetBrains Mono', monospace; font-size: 0.8125rem; line-height: 1.9; background: #0d0d14; overflow-x: auto;">
                                @if($featured)
                                    <div><span style="color:#6b7280"># {{ $featured->category ?: 'projeto' }}</span></div>
                                    <div>&nbsp;</div>
                                    <div><span style="color:#a78bfa">## </span><span style="color:#f1f5f9">{{ $featured->title }}</span></div>
                                    <div>&nbsp;</div>
                                    <div><span style="color:#94a3b8">{{ Str::limit($featured->description, 140) }}</span></div>
                                    <div>&nbsp;</div>
                                    <div><span style="color:#6b7280">// {{ $featured->files_count }} arquivo(s) &mdash; {{ number_format($featured->downloads_total ?? 0, 0, ',', '.') }} downloads</span></div>
                                @else
                                    <div><span style="color:#6b7280"># em breve</span></div>
                                    <div>&nbsp;</div>
                                    <div><span style="color:#a78bfa">## </span><span style="color:#f1f5f9">Central de downloads</span></div>
                                    <div>&nbsp;</div>
                                    <div><span style="color:#94a3b8">Os primeiros projetos estão a caminho.</span></div>
                                @endif
                                <div style="padding-left: 0; position: relative; margin-top: 0.5rem;">
                                    <span style="color: rgba(99,102,241,0.55); font-style: italic;">@if($featured) baixar agora… @else aguarde… @endif</span><span class="cp-cursor"></span>
                                </div>
                            </div>

                            <!-- Badge -->
                            <div style="padding: 10px 18px; border-top: 1px solid rgba(99,102,241,0.15); background: rgba(99,102,241,0.07); display: flex; align-items: center; gap: 8px;">
                                <span style="color: #6366f1; font-size: 0.8rem; line-height: 1;">&#10022;</span>
                                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; color: #a5b4fc;">@if($featured) <strong style="color: #e0e7ff;">{{ $featured->title }}</strong> disponível @else Em breve @endif</span>
                                <a href="{{ $featured ? route('project.show', $featured) : route('downloads') }}" style="margin-left: auto; font-family: 'JetBrains Mono', monospace; font-size: 0.65rem; color: #6366f1; white-space: nowrap; text-decoration: none;">abrir →</a>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Marquee Strip -->
        <div style="position: absolute; bottom: 0; left: 0; right: 0; background: #1a1a2e; border-top: 1px solid rgba(99,102,241,0.15); padding: 14px 0; overflow: hidden; z-index: 2;">
            <div class="cp-marquee-track">
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-solid fa-server" style="color:#6366f1;"></i>Linux</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-brands fa-laravel" style="color:#FF2D20;"></i>Laravel</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-brands fa-php" style="color:#777BB4;"></i>PHP</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-brands fa-docker" style="color:#2496ED;"></i>Docker</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-brands fa-git-alt" style="color:#F05032;"></i>Git</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-solid fa-database" style="color:#4479A1;"></i>MySQL</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: rgba(99,102,241,0.3); margin: 0 36px;"><i class="fa-solid fa-circle-dot" style="font-size:0.5rem;"></i></span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-solid fa-server" style="color:#6366f1;"></i>Linux</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-solid fa-server" style="color:#fff000;"></i>Debian</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-brands fa-laravel" style="color:#FF2D20;"></i>Laravel</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-brands fa-php" style="color:#777BB4;"></i>PHP</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-brands fa-docker" style="color:#2496ED;"></i>Docker</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-brands fa-git-alt" style="color:#F05032;"></i>Git</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-solid fa-database" style="color:#4479A1;"></i>MySQL</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: rgba(99,102,241,0.3); margin: 0 36px;"><i class="fa-solid fa-circle-dot" style="font-size:0.5rem;"></i></span>
            </div>
        </div>

    </section>

    <!-- Content -->
    <section id="content">
        <div class="content-wrap py-0">

            <!-- Projetos -->
            <section class="section my-0 dark" style="background-color: #0d0d14; padding: 100px 0; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -120px; left: -180px; width: 640px; height: 640px; background: radial-gradient(ellipse, rgba(99,102,241,0.1) 0%, transparent 68%); pointer-events: none; z-index: 0;"></div>

                <div class="container" style="position: relative; z-index: 1;">

                    <div class="text-center" style="max-width: 620px; margin: 0 auto 64px;">
                        <span class="cp-eyebrow">// PROJETOS</span>
                        <h2 style="font-family: 'Inter', sans-serif; font-size: 2.5rem; font-weight: 700; color: #f1f5f9; letter-spacing: -0.02em; line-height: 1.2; margin-bottom: 16px;">Disponíveis para download</h2>
                    </div>

                    <div class="row g-4">
                        @forelse($projects as $project)
                        <div class="col-lg-4 col-md-6">
                            <article class="cp-glass-card h-100" style="padding: 32px;">
                                <div style="margin-bottom: 16px; display:flex; align-items:center; gap:12px;">
                                    @if($project->icon)
                                        <span style="width:42px;height:42px;border-radius:10px;background:rgba(99,102,241,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="{{ $project->icon }}" style="color:#6366f1;font-size:1.1rem;"></i></span>
                                    @endif
                                    @if($project->category)
                                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.68rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: #6366f1; background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.2); border-radius: 4px; padding: 3px 10px;">{{ $project->category }}</span>
                                    @endif
                                </div>
                                @php $isLink = $project->redirectsToSite(); @endphp
                                <h3 style="font-family: 'Inter', sans-serif; font-size: 1.125rem; font-weight: 600; color: #f1f5f9; line-height: 1.4; margin-bottom: 12px;">
                                    <a href="{{ $project->public_url }}" @if($isLink) target="_blank" rel="noopener" @endif style="color: inherit; text-decoration: none;">{{ $project->title }}</a>
                                </h3>
                                <p style="font-family: 'Inter', sans-serif; font-size: 0.9rem; color: #94a3b8; line-height: 1.7; margin-bottom: 20px;">{{ Str::limit($project->description, 120) }}</p>
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 20px; border-top: 1px solid rgba(99,102,241,0.1);">
                                    @if($isLink)
                                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; color: #64748b;"><i class="fa-solid fa-up-right-from-square" style="margin-right:5px;"></i>site</span>
                                        <a href="{{ $project->public_url }}" target="_blank" rel="noopener" style="font-family: 'JetBrains Mono', monospace; font-size: 0.72rem; color: #6366f1; text-decoration: none;">visitar ↗</a>
                                    @else
                                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; color: #64748b;"><i class="fa-solid fa-download" style="margin-right:5px;"></i>{{ number_format($project->downloads_total ?? 0, 0, ',', '.') }}</span>
                                        <a href="{{ $project->public_url }}" style="font-family: 'JetBrains Mono', monospace; font-size: 0.72rem; color: #6366f1; text-decoration: none;">abrir →</a>
                                    @endif
                                </div>
                            </article>
                        </div>
                        @empty
                        <div class="col-12 text-center" style="padding: 60px 0;">
                            <span style="font-family: 'JetBrains Mono', monospace; color: #64748b;">// nenhum projeto publicado ainda — em breve</span>
                        </div>
                        @endforelse
                    </div>

                    @if($projects->isNotEmpty())
                    <div class="text-center mt-5">
                        <a href="{{ route('downloads') }}" class="button button-rounded button-large button-border" style="border-color: rgba(99,102,241,0.45); color: #a5b4fc; font-family: 'Inter', sans-serif; font-weight: 600;">Ver todos os downloads <i class="bi-arrow-right ms-2"></i></a>
                    </div>
                    @endif

                </div>
            </section>

        </div>
    </section>

@endsection
