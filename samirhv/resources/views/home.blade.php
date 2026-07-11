@extends('layouts.app')

@section('title', 'Samirhv')
@section('description', 'Projetos e ferramentas de Samir Hanna Verza disponibilizados para download.')

@section('content')

    @php
        $featuredProject = $featured ?? $projects->first();
        $otherProjects = $featuredProject
            ? $projects->reject(fn ($project) => $project->id === $featuredProject->id)->values()
            : collect();
    @endphp

    {{-- ═══ HERO ═══ --}}
    <section class="s-home-hero">
        <div class="s-aura"></div>
        <div class="container">
            <div class="s-home-hero__grid">
                <div class="s-home-hero__copy s-reveal" data-d="1">
                    <span class="s-kicker">Engenharia independente</span>
                    <h1 class="s-home-title">Ferramentas que nascem do trabalho real.</h1>
                    <p class="s-lead s-home-intro">
                        Apps, utilitários e software de infraestrutura construídos por Samir Hanna Verza.
                        Projetos diretos, releases verificáveis e downloads sem atrito.
                    </p>

                    <div class="s-home-actions">
                        <a href="#projetos" class="s-btn s-btn--lg">
                            Explorar projetos <i class="fa-solid fa-arrow-down"></i>
                        </a>
                        <a href="https://github.com/samirhvbr" target="_blank" rel="noopener" class="s-text-link">
                            <i class="fa-brands fa-github"></i> Ver código no GitHub
                        </a>
                    </div>

                    <div class="s-home-trust">
                        <span><i class="fa-solid fa-circle-check"></i> releases versionados</span>
                        <span><i class="fa-solid fa-shield-halved"></i> hashes publicados</span>
                        <span><i class="fa-brands fa-linux"></i> foco em Linux</span>
                    </div>
                </div>

                <div class="s-release-board s-reveal" data-d="2" aria-label="Catálogo de projetos publicados">
                    <div class="s-release-board__bar">
                        <div>
                            <span class="s-release-board__eyebrow">Catálogo público</span>
                            <strong>Projetos em produção</strong>
                        </div>
                        <span class="s-live-status"><i></i> online</span>
                    </div>

                    @if($featuredProject)
                        <a href="{{ $featuredProject->public_url }}" @if($featuredProject->redirectsToSite()) target="_blank" rel="noopener" @endif class="s-release-board__featured">
                            <span class="s-release-board__icon"><i class="{{ $featuredProject->icon ?: 'fa-solid fa-cube' }}"></i></span>
                            <span class="s-release-board__featured-copy">
                                <small>Projeto em destaque</small>
                                <strong>{{ $featuredProject->title }}</strong>
                                <span>{{ Str::limit($featuredProject->description, 105) }}</span>
                            </span>
                            <i class="fa-solid fa-arrow-up-right-from-square s-release-board__arrow"></i>
                        </a>
                    @endif

                    <div class="s-release-board__list">
                        @foreach($projects->take(4) as $project)
                            <a href="{{ $project->public_url }}" @if($project->redirectsToSite()) target="_blank" rel="noopener" @endif class="s-release-row">
                                <span class="s-release-row__index">0{{ $loop->iteration }}</span>
                                <span class="s-release-row__name">{{ $project->title }}</span>
                                <span class="s-release-row__type">{{ $project->category ?: ($project->redirectsToSite() ? 'site' : 'software') }}</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        @endforeach
                    </div>

                    <div class="s-release-board__foot">
                        <span>{{ $projects->count() }} projetos publicados</span>
                        <a href="{{ route('downloads') }}">abrir central de downloads <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══ PROJETOS ═══ --}}
    <section class="s-projects-section" id="projetos">
        <div class="container">
            <div class="s-heading-row">
                <div>
                    <span class="s-section-number">01 / PROJETOS</span>
                    <h2 class="s-h2">Trabalho publicado</h2>
                    <p class="s-lead s-muted">Software com propósito claro, pronto para usar ou acompanhar.</p>
                </div>
                <a href="{{ route('downloads') }}" class="s-text-link d-none d-md-inline-flex">
                    Ver todos os downloads <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            @if($featuredProject)
                <div class="s-portfolio-layout">
                    <a href="{{ $featuredProject->public_url }}" @if($featuredProject->redirectsToSite()) target="_blank" rel="noopener" @endif class="s-portfolio-featured">
                        <div class="s-portfolio-featured__top">
                            <span class="s-project-mark"><i class="{{ $featuredProject->icon ?: 'fa-solid fa-cube' }}"></i></span>
                            <span class="s-project-state"><i></i> disponível</span>
                        </div>
                        <div class="s-portfolio-featured__content">
                            <span class="s-project-overline">Projeto principal</span>
                            <h3>{{ $featuredProject->title }}</h3>
                            <p>{{ Str::limit($featuredProject->description, 220) }}</p>
                        </div>
                        <div class="s-portfolio-featured__foot">
                            <div>
                                @if($featuredProject->category)<span>{{ $featuredProject->category }}</span>@endif
                                @if(($featuredProject->files_count ?? 0) > 0)<span>{{ $featuredProject->files_count }} builds</span>@endif
                                @if(($featuredProject->downloads_total ?? 0) > 0)<span>{{ number_format($featuredProject->downloads_total, 0, ',', '.') }} downloads</span>@endif
                            </div>
                            <strong>{{ $featuredProject->redirectsToSite() ? 'Visitar projeto' : 'Ver projeto' }} <i class="fa-solid fa-arrow-right"></i></strong>
                        </div>
                    </a>

                    <div class="s-portfolio-list">
                        @foreach($otherProjects as $project)
                            @php
                                $isLink = $project->redirectsToSite();
                                $meta = $isLink
                                    ? 'site externo'
                                    : (($project->downloads_total ?? 0) > 0
                                        ? number_format($project->downloads_total, 0, ',', '.').' downloads'
                                        : ($project->hasCustomPage() ? 'documentação' : 'projeto'));
                            @endphp
                            <a href="{{ $project->public_url }}" @if($isLink) target="_blank" rel="noopener" @endif class="s-portfolio-item">
                                <span class="s-portfolio-item__icon"><i class="{{ $project->icon ?: 'fa-solid fa-cube' }}"></i></span>
                                <span class="s-portfolio-item__copy">
                                    <small>{{ $project->category ?: 'Software' }}</small>
                                    <strong>{{ $project->title }}</strong>
                                    <span>{{ Str::limit($project->description, 90) }}</span>
                                </span>
                                <span class="s-portfolio-item__meta">{{ $meta }}</span>
                                <i class="fa-solid {{ $isLink ? 'fa-arrow-up-right-from-square' : 'fa-arrow-right' }}"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="s-empty-state">Nenhum projeto publicado ainda.</div>
            @endif

            <a href="{{ route('downloads') }}" class="s-btn s-btn--ghost d-md-none" style="width:100%; margin-top:24px;">
                Ver todos os downloads <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </section>

    {{-- ═══ MÉTODO ═══ --}}
    <section class="s-method-section">
        <div class="container">
            <div class="s-method-intro">
                <span class="s-section-number">02 / PRINCÍPIOS</span>
                <h2 class="s-h2">Software calmo.<br>Engenharia explícita.</h2>
            </div>
            <div class="s-method-grid">
                <article>
                    <span>01</span>
                    <h3>Problema antes da interface</h3>
                    <p>Cada projeto começa em uma necessidade real de desenvolvimento, operação ou infraestrutura.</p>
                </article>
                <article>
                    <span>02</span>
                    <h3>Procedência visível</h3>
                    <p>Versão, sistema, arquitetura, data e hash aparecem onde a decisão de instalar acontece.</p>
                </article>
                <article>
                    <span>03</span>
                    <h3>Sem atrito artificial</h3>
                    <p>Você entende o projeto, encontra o build correto e baixa sem cadastro ou etapas desnecessárias.</p>
                </article>
            </div>
        </div>
    </section>

@endsection
