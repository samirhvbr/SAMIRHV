@extends('layouts.app')

@section('title', 'Samirhv')
@section('description', 'Blog pessoal de Samir Hanna Verza — tecnologia, desenvolvimento e reflexões.')

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
                    <span class="cp-eyebrow">// BLOG PESSOAL</span>
                    <h1 class="cp-hero-h1 mb-4">
                        Ideias, código<br><span style="color: #6366f1;">e reflexões.</span>
                    </h1>
                    <p style="font-family: 'Inter', sans-serif; font-size: 1.125rem; color: #94a3b8; line-height: 1.75; max-width: 480px; margin-bottom: 0;">
                        Escrito por Samir Hanna Verza. Aqui registro o que aprendo, experimento e penso sobre tecnologia, desenvolvimento e o mundo ao redor.
                    </p>
                    <div class="d-flex gap-3 flex-wrap" style="margin-top: 2rem;">
                        <a href="{{ route('blog.index') }}" class="button button-rounded button-large" style="background: #6366f1; border-color: #6366f1; color: #fff; font-family: 'Inter', sans-serif; font-weight: 600; padding: 14px 32px; box-shadow: 0 4px 24px rgba(99,102,241,0.35);">Ver todos os posts</a>
                        <a href="{{ route('blog.index') }}?categoria=dev" class="button button-rounded button-large button-border" style="border-color: rgba(99,102,241,0.45); color: #a5b4fc; font-family: 'Inter', sans-serif; font-weight: 600; padding: 14px 32px;">
                            <i class="fa-solid fa-code me-2" style="font-size: 0.875rem;"></i>Dev & Tech
                        </a>
                    </div>
                    <p style="margin-top: 1.25rem; font-size: 0.875rem; color: #64748b; font-family: 'JetBrains Mono', monospace;">
                        <span style="color: #22c55e;">&#9679;</span>&nbsp; samirhv.com.br &mdash; feito com Laravel + Canvas
                    </p>
                </div>

                <!-- Coluna direita — Post em destaque simulado -->
                <div class="col-lg-6">
                    <div class="cp-terminal-wrapper">
                        <div class="cp-terminal-inner">

                            <!-- Header do card -->
                            <div style="background: #1a1a2e; padding: 12px 18px; display: flex; align-items: center; gap: 8px; border-bottom: 1px solid rgba(99,102,241,0.15);">
                                <span style="width:12px;height:12px;border-radius:50%;background:#ff5f57;display:inline-block;flex-shrink:0;"></span>
                                <span style="width:12px;height:12px;border-radius:50%;background:#febc2e;display:inline-block;flex-shrink:0;"></span>
                                <span style="width:12px;height:12px;border-radius:50%;background:#28c840;display:inline-block;flex-shrink:0;"></span>
                                <span style="margin-left: 14px; font-family: 'JetBrains Mono', monospace; font-size: 0.72rem; color: #64748b; letter-spacing: 0.02em;">post-recente.md</span>
                                <span style="margin-left: auto; font-family: 'JetBrains Mono', monospace; font-size: 0.68rem; color: #374151; background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.2); border-radius: 4px; padding: 2px 8px;">Blog</span>
                            </div>

                            <!-- Conteúdo -->
                            <div style="padding: 24px 26px; font-family: 'JetBrains Mono', monospace; font-size: 0.8125rem; line-height: 1.9; background: #0d0d14; overflow-x: auto;">
                                @if(isset($featuredPost))
                                    <div><span style="color:#6b7280"># {{ $featuredPost['category'] }}</span></div>
                                    <div>&nbsp;</div>
                                    <div><span style="color:#a78bfa">## </span><span style="color:#f1f5f9">{{ $featuredPost['title'] }}</span></div>
                                    <div>&nbsp;</div>
                                    <div><span style="color:#94a3b8">{{ Str::limit($featuredPost['excerpt'], 140) }}</span></div>
                                    <div>&nbsp;</div>
                                    <div><span style="color:#6b7280">// {{ $featuredPost['date'] }} &mdash; {{ $featuredPost['reading_time'] }} min de leitura</span></div>
                                @else
                                    <div><span style="color:#6b7280"># tecnologia</span></div>
                                    <div>&nbsp;</div>
                                    <div><span style="color:#a78bfa">## </span><span style="color:#f1f5f9">Bem-vindo ao blog</span></div>
                                    <div>&nbsp;</div>
                                    <div><span style="color:#94a3b8">Este é o meu espaço pessoal para escrever sobre</span></div>
                                    <div><span style="color:#94a3b8">tecnologia, código e o que mais despertar</span></div>
                                    <div><span style="color:#94a3b8">curiosidade no caminho.</span></div>
                                    <div>&nbsp;</div>
                                    <div><span style="color:#6b7280">// maio 2026 &mdash; primeiros passos</span></div>
                                @endif
                                <div style="padding-left: 0; position: relative; margin-top: 0.5rem;">
                                    <span style="color: rgba(99,102,241,0.55); font-style: italic;">Continue lendo...</span><span class="cp-cursor"></span>
                                </div>
                            </div>

                            <!-- Badge -->
                            <div style="padding: 10px 18px; border-top: 1px solid rgba(99,102,241,0.15); background: rgba(99,102,241,0.07); display: flex; align-items: center; gap: 8px;">
                                <span style="color: #6366f1; font-size: 0.8rem; line-height: 1;">&#10022;</span>
                                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; color: #a5b4fc;">Novo post disponível — <strong style="color: #e0e7ff;">leia agora</strong></span>
                                <a href="{{ route('blog.index') }}" style="margin-left: auto; font-family: 'JetBrains Mono', monospace; font-size: 0.65rem; color: #6366f1; white-space: nowrap; text-decoration: none;">Ver todos →</a>
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
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: rgba(99,102,241,0.3); margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px;"><i class="fa-solid fa-circle-dot" style="font-size:0.5rem;"></i></span>
                <!-- Duplicate for seamless loop -->
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-solid fa-server" style="color:#6366f1;"></i>Linux</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-brands fa-laravel" style="color:#FF2D20;"></i>Laravel</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-brands fa-php" style="color:#777BB4;"></i>PHP</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-brands fa-docker" style="color:#2496ED;"></i>Docker</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-brands fa-git-alt" style="color:#F05032;"></i>Git</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #64748b; margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 7px;"><i class="fa-solid fa-database" style="color:#4479A1;"></i>MySQL</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: rgba(99,102,241,0.3); margin: 0 36px; letter-spacing: 0.05em; white-space: nowrap; display: inline-flex; align-items: center; gap: 6px;"><i class="fa-solid fa-circle-dot" style="font-size:0.5rem;"></i></span>
            </div>
        </div>

    </section>

    <!-- Content -->
    <section id="content">
        <div class="content-wrap py-0">

            <!-- Tópicos / Categorias -->
            <section class="section my-0 dark" style="background-color: #111827; padding: 100px 0;">
                <div class="container">
                    <div class="text-center mb-5" style="max-width: 600px; margin-left: auto; margin-right: auto;">
                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: #6366f1; display: block; margin-bottom: 1rem;">// TÓPICOS</span>
                        <h2 style="font-family: 'Inter', sans-serif; font-weight: 700; font-size: 2.5rem; line-height: 1.2; letter-spacing: -0.01em; color: #f1f5f9; margin-bottom: 1rem;">O que você vai encontrar aqui</h2>
                        <p style="font-family: 'Inter', sans-serif; font-size: 1.125rem; color: #94a3b8; line-height: 1.75; margin: 0;">Escrita honesta sobre temas que me interessam de verdade.</p>
                    </div>
                    <div class="row g-4 mt-2">
                        @foreach($topics as $topic)
                        <div class="col-lg-4 col-md-6 col-12">
                            <a href="{{ route('blog.index') }}?categoria={{ $topic['slug'] }}" style="text-decoration: none;">
                                <div class="cp-feat-card">
                                    <div class="cp-feat-icon" style="width: 48px; height: 48px; border-radius: 10px; background: rgba(99,102,241,0.12); display: flex; align-items: center; justify-content: center; margin-bottom: 20px; transition: background 0.3s ease;">
                                        <i class="{{ $topic['icon'] }}" style="font-size: 1.25rem; color: #6366f1;"></i>
                                    </div>
                                    <h3 style="font-family: 'Inter', sans-serif; font-weight: 600; font-size: 1.125rem; color: #f1f5f9; margin-bottom: 10px;">{{ $topic['name'] }}</h3>
                                    <p style="font-family: 'Inter', sans-serif; font-size: 0.9375rem; color: #94a3b8; line-height: 1.7; margin: 0;">{{ $topic['description'] }}</p>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- Posts Recentes -->
            <section class="section my-0 dark" style="background-color: #0d0d14; padding: 100px 0; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -120px; left: -180px; width: 640px; height: 640px; background: radial-gradient(ellipse, rgba(99,102,241,0.1) 0%, transparent 68%); pointer-events: none; z-index: 0;"></div>

                <div class="container" style="position: relative; z-index: 1;">

                    <div class="text-center" style="max-width: 620px; margin: 0 auto 64px;">
                        <span class="cp-eyebrow">// POSTS RECENTES</span>
                        <h2 style="font-family: 'Inter', sans-serif; font-size: 2.5rem; font-weight: 700; color: #f1f5f9; letter-spacing: -0.02em; line-height: 1.2; margin-bottom: 16px;">Últimas publicações</h2>
                    </div>

                    <div class="row g-4">
                        @forelse($recentPosts as $post)
                        <div class="col-lg-4 col-md-6">
                            <article class="cp-glass-card h-100" style="padding: 32px;">
                                <div style="margin-bottom: 16px;">
                                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.68rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: #6366f1; background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.2); border-radius: 4px; padding: 3px 10px;">{{ $post['category'] }}</span>
                                </div>
                                <h3 style="font-family: 'Inter', sans-serif; font-size: 1.125rem; font-weight: 600; color: #f1f5f9; line-height: 1.4; margin-bottom: 12px;">
                                    <a href="{{ route('blog.show', $post['slug']) }}" style="color: inherit; text-decoration: none;">{{ $post['title'] }}</a>
                                </h3>
                                <p style="font-family: 'Inter', sans-serif; font-size: 0.9rem; color: #94a3b8; line-height: 1.7; margin-bottom: 20px;">{{ Str::limit($post['excerpt'], 120) }}</p>
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 20px; border-top: 1px solid rgba(99,102,241,0.1);">
                                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; color: #64748b;">{{ $post['date'] }}</span>
                                    <a href="{{ route('blog.show', $post['slug']) }}" style="font-family: 'JetBrains Mono', monospace; font-size: 0.72rem; color: #6366f1; text-decoration: none;">ler →</a>
                                </div>
                            </article>
                        </div>
                        @empty
                        <div class="col-12 text-center" style="padding: 60px 0;">
                            <span style="font-family: 'JetBrains Mono', monospace; color: #64748b;">// nenhum post ainda — em breve</span>
                        </div>
                        @endforelse
                    </div>

                    @if(count($recentPosts) > 0)
                    <div class="text-center mt-5">
                        <a href="{{ route('blog.index') }}" class="button button-rounded button-large button-border" style="border-color: rgba(99,102,241,0.45); color: #a5b4fc; font-family: 'Inter', sans-serif; font-weight: 600;">Ver todos os posts <i class="bi-arrow-right ms-2"></i></a>
                    </div>
                    @endif

                </div>
            </section>

        </div>
    </section>

@endsection
