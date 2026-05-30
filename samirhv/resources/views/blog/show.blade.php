@extends('layouts.app')

@section('title', $post['title'])
@section('description', Str::limit($post['excerpt'], 160))

@section('content')

    <!-- Post Header -->
    <section class="dark include-header" style="background-color: #0d0d14; padding: 120px 0 60px; position: relative; overflow: hidden;">
        <svg style="position: absolute; inset: 0; z-index: 0; opacity: 0.15;" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%">
            <defs>
                <pattern id="cp-dots-post" x="0" y="0" width="24" height="24" patternUnits="userSpaceOnUse">
                    <circle cx="1.5" cy="1.5" r="1.5" fill="#6366f1"/>
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#cp-dots-post)"/>
        </svg>
        <div style="position: absolute; top: -60px; right: -80px; width: 500px; height: 500px; background: radial-gradient(ellipse, rgba(99,102,241,0.08) 0%, transparent 70%); pointer-events: none; z-index: 0;"></div>

        <div class="container" style="position: relative; z-index: 1; max-width: 820px;">
            <div style="margin-bottom: 20px;">
                <a href="{{ route('blog.index') }}" style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: #6366f1; text-decoration: none;">← voltar ao blog</a>
            </div>
            <div style="margin-bottom: 16px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.68rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: #6366f1; background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.2); border-radius: 4px; padding: 3px 10px;">{{ $post['category'] }}</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; color: #64748b;">{{ $post['date'] }}</span>
                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; color: #4b5563;">· {{ $post['reading_time'] }} min de leitura</span>
            </div>
            <h1 style="font-family: 'Inter', sans-serif; font-size: clamp(1.75rem, 4vw, 3rem); font-weight: 800; color: #f1f5f9; letter-spacing: -0.03em; line-height: 1.15; margin-bottom: 1.25rem;">{{ $post['title'] }}</h1>
            <p style="font-family: 'Inter', sans-serif; font-size: 1.125rem; color: #94a3b8; line-height: 1.75; max-width: 640px;">{{ $post['excerpt'] }}</p>
        </div>
    </section>

    <!-- Post Content -->
    <section id="content">
        <div class="content-wrap py-0">
            <section class="section my-0 dark" style="background-color: #111827; padding: 80px 0 100px;">
                <div class="container" style="max-width: 820px;">
                    <div class="row justify-content-center">
                        <div class="col-12">

                            <!-- Conteúdo do post -->
                            <div class="post-content" style="font-family: 'Inter', sans-serif; font-size: 1.0625rem; color: #cbd5e1; line-height: 1.875;">
                                {!! $post['content'] !!}
                            </div>

                            <!-- Tags -->
                            @if(!empty($post['tags']))
                            <div style="margin-top: 48px; padding-top: 32px; border-top: 1px solid rgba(99,102,241,0.1);">
                                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.7rem; color: #64748b; margin-right: 12px;">// tags</span>
                                @foreach($post['tags'] as $tag)
                                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.72rem; color: #94a3b8; background: rgba(255,255,255,0.04); border: 1px solid rgba(99,102,241,0.15); border-radius: 4px; padding: 3px 10px; margin-right: 6px; display: inline-block; margin-bottom: 6px;">#{{ $tag }}</span>
                                @endforeach
                            </div>
                            @endif

                            <!-- Navegação entre posts -->
                            <div class="row g-3 mt-4">
                                @if($prevPost)
                                <div class="col-6">
                                    <a href="{{ route('blog.show', $prevPost['slug']) }}" class="cp-glass-card d-block" style="padding: 20px 24px; text-decoration: none;">
                                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.68rem; color: #6366f1; display: block; margin-bottom: 6px;">← anterior</span>
                                        <span style="font-family: 'Inter', sans-serif; font-size: 0.9rem; color: #f1f5f9; font-weight: 500; line-height: 1.4;">{{ Str::limit($prevPost['title'], 60) }}</span>
                                    </a>
                                </div>
                                @else
                                <div class="col-6"></div>
                                @endif

                                @if($nextPost)
                                <div class="col-6">
                                    <a href="{{ route('blog.show', $nextPost['slug']) }}" class="cp-glass-card d-block text-end" style="padding: 20px 24px; text-decoration: none;">
                                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.68rem; color: #6366f1; display: block; margin-bottom: 6px;">próximo →</span>
                                        <span style="font-family: 'Inter', sans-serif; font-size: 0.9rem; color: #f1f5f9; font-weight: 500; line-height: 1.4;">{{ Str::limit($nextPost['title'], 60) }}</span>
                                    </a>
                                </div>
                                @endif
                            </div>

                            <!-- CTA voltar -->
                            <div class="text-center mt-5">
                                <a href="{{ route('blog.index') }}" class="button button-rounded button-large button-border" style="border-color: rgba(99,102,241,0.45); color: #a5b4fc; font-family: 'Inter', sans-serif; font-weight: 600;">← Ver todos os posts</a>
                            </div>

                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>

@endsection

@push('styles')
<style>
.post-content h1, .post-content h2, .post-content h3, .post-content h4 {
    font-family: 'Inter', sans-serif;
    color: #f1f5f9;
    letter-spacing: -0.02em;
    margin-top: 2.5rem;
    margin-bottom: 1rem;
}
.post-content h2 { font-size: 1.75rem; font-weight: 700; }
.post-content h3 { font-size: 1.375rem; font-weight: 600; }
.post-content p { margin-bottom: 1.5rem; }
.post-content a { color: #818cf8; text-decoration: underline; }
.post-content a:hover { color: #a5b4fc; }
.post-content code {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.875em;
    color: #a5b4fc;
    background: rgba(99,102,241,0.1);
    border: 1px solid rgba(99,102,241,0.2);
    border-radius: 4px;
    padding: 2px 6px;
}
.post-content pre {
    background: #0d0d14;
    border: 1px solid rgba(99,102,241,0.2);
    border-radius: 10px;
    padding: 24px;
    overflow-x: auto;
    margin-bottom: 1.5rem;
}
.post-content pre code {
    background: none;
    border: none;
    padding: 0;
    color: #e2e8f0;
    font-size: 0.875rem;
    line-height: 1.8;
}
.post-content blockquote {
    border-left: 3px solid #6366f1;
    padding: 12px 24px;
    margin: 2rem 0;
    background: rgba(99,102,241,0.05);
    border-radius: 0 8px 8px 0;
    color: #94a3b8;
    font-style: italic;
}
.post-content ul, .post-content ol {
    padding-left: 1.5rem;
    margin-bottom: 1.5rem;
}
.post-content li { margin-bottom: 0.5rem; }
.post-content hr {
    border: none;
    border-top: 1px solid rgba(99,102,241,0.15);
    margin: 3rem 0;
}
</style>
@endpush
