@extends('layouts.app')

@section('title', 'Blog')
@section('description', 'Todos os posts do blog — tecnologia, desenvolvimento e reflexões.')

@section('content')

    <!-- Page Header -->
    <section class="dark include-header" style="background-color: #0d0d14; padding: 120px 0 60px; position: relative; overflow: hidden;">
        <svg style="position: absolute; inset: 0; z-index: 0; opacity: 0.2;" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
            <defs>
                <pattern id="cp-dots-blog" x="0" y="0" width="24" height="24" patternUnits="userSpaceOnUse">
                    <circle cx="1.5" cy="1.5" r="1.5" fill="#6366f1"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#cp-dots-blog)"/>
        </svg>
        <div style="position: absolute; top: -80px; right: -100px; width: 500px; height: 500px; background: radial-gradient(ellipse, rgba(99,102,241,0.08) 0%, transparent 70%); pointer-events: none; z-index: 0;"></div>

        <div class="container" style="position: relative; z-index: 1;">
            <span class="cp-eyebrow">// BLOG</span>
            <h1 style="font-family: 'Inter', sans-serif; font-size: clamp(2rem, 4vw, 3.5rem); font-weight: 800; color: #f1f5f9; letter-spacing: -0.03em; line-height: 1.1; margin-bottom: 1rem;">
                Todos os <span style="color: #6366f1;">posts</span>
            </h1>
            <p style="font-family: 'Inter', sans-serif; font-size: 1.125rem; color: #94a3b8; max-width: 480px; line-height: 1.75;">
                {{ $totalPosts }} {{ $totalPosts === 1 ? 'publicação' : 'publicações' }}{{ $currentCategory ? ' em "'.$currentCategory.'"' : '' }}
            </p>

            <!-- Filtro de categorias -->
            <div class="d-flex flex-wrap gap-2 mt-4">
                <a href="{{ route('blog.index') }}" class="button button-rounded button-small {{ !$currentCategory ? '' : 'button-border' }}" style="{{ !$currentCategory ? 'background:#6366f1;border-color:#6366f1;color:#fff;' : 'border-color:rgba(99,102,241,0.35);color:#94a3b8;' }} font-family:'JetBrains Mono',monospace;font-size:0.75rem;">Todos</a>
                @foreach($categories as $cat)
                <a href="{{ route('blog.index') }}?categoria={{ $cat['slug'] }}" class="button button-rounded button-small {{ $currentCategory === $cat['slug'] ? '' : 'button-border' }}" style="{{ $currentCategory === $cat['slug'] ? 'background:#6366f1;border-color:#6366f1;color:#fff;' : 'border-color:rgba(99,102,241,0.35);color:#94a3b8;' }} font-family:'JetBrains Mono',monospace;font-size:0.75rem;">{{ $cat['name'] }}</a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Posts Grid -->
    <section id="content">
        <div class="content-wrap py-0">
            <section class="section my-0 dark" style="background-color: #111827; padding: 80px 0 100px;">
                <div class="container">
                    @if($posts->count() > 0)
                    <div class="row g-4">
                        @foreach($posts as $post)
                        <div class="col-lg-4 col-md-6">
                            <article class="cp-glass-card h-100" style="padding: 32px; display: flex; flex-direction: column;">
                                <div style="margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.68rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: #6366f1; background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.2); border-radius: 4px; padding: 3px 10px;">{{ $post['category'] }}</span>
                                    @if($post['featured'] ?? false)
                                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #0d0d14; background: #f59e0b; border-radius: 4px; padding: 3px 8px;">destaque</span>
                                    @endif
                                </div>
                                <h2 style="font-family: 'Inter', sans-serif; font-size: 1.125rem; font-weight: 600; color: #f1f5f9; line-height: 1.4; margin-bottom: 12px;">
                                    <a href="{{ route('blog.show', $post['slug']) }}" style="color: inherit; text-decoration: none;">{{ $post['title'] }}</a>
                                </h2>
                                <p style="font-family: 'Inter', sans-serif; font-size: 0.9rem; color: #94a3b8; line-height: 1.7; margin-bottom: 20px; flex: 1;">{{ Str::limit($post['excerpt'], 140) }}</p>
                                <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 20px; border-top: 1px solid rgba(99,102,241,0.1);">
                                    <div>
                                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; color: #64748b; display: block;">{{ $post['date'] }}</span>
                                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.68rem; color: #4b5563;">{{ $post['reading_time'] }} min de leitura</span>
                                    </div>
                                    <a href="{{ route('blog.show', $post['slug']) }}" style="font-family: 'JetBrains Mono', monospace; font-size: 0.72rem; color: #6366f1; text-decoration: none; white-space: nowrap;">ler →</a>
                                </div>
                            </article>
                        </div>
                        @endforeach
                    </div>

                    <!-- Paginação -->
                    @if($posts instanceof \Illuminate\Pagination\LengthAwarePaginator && $posts->hasPages())
                    <div class="d-flex justify-content-center mt-5 gap-2">
                        @if($posts->onFirstPage())
                            <span class="button button-rounded button-small button-border" style="border-color:rgba(99,102,241,0.2);color:#4b5563;cursor:not-allowed;">← anterior</span>
                        @else
                            <a href="{{ $posts->previousPageUrl() }}" class="button button-rounded button-small button-border" style="border-color:rgba(99,102,241,0.35);color:#94a3b8;">← anterior</a>
                        @endif
                        @if($posts->hasMorePages())
                            <a href="{{ $posts->nextPageUrl() }}" class="button button-rounded button-small" style="background:#6366f1;border-color:#6366f1;color:#fff;">próximo →</a>
                        @else
                            <span class="button button-rounded button-small button-border" style="border-color:rgba(99,102,241,0.2);color:#4b5563;cursor:not-allowed;">próximo →</span>
                        @endif
                    </div>
                    @endif

                    @else
                    <div class="text-center" style="padding: 80px 0;">
                        <div style="font-family: 'JetBrains Mono', monospace; font-size: 2rem; color: rgba(99,102,241,0.3); margin-bottom: 1rem;">&#123; &#125;</div>
                        <h3 style="font-family: 'Inter', sans-serif; color: #64748b; font-weight: 500;">Nenhum post encontrado</h3>
                        <p style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; color: #4b5563; margin-top: 0.5rem;">// em breve por aqui</p>
                        <a href="{{ route('home') }}" class="button button-rounded mt-4" style="background:#6366f1;border-color:#6366f1;color:#fff;">← Voltar ao início</a>
                    </div>
                    @endif
                </div>
            </section>
        </div>
    </section>

@endsection
