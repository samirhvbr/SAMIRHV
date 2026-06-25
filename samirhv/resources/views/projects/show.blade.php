@extends('layouts.app')

@section('title', $project->title)
@section('description', Str::limit($project->description, 150) ?: 'Download de '.$project->title)

@section('content')

    <section class="dark include-header" style="background-color: #0d0d14; min-height: 100vh; position: relative; overflow: hidden;">
        <div class="cp-hero-glow"></div>

        <div class="container" style="position: relative; z-index: 1; padding-top: 60px; padding-bottom: 100px; max-width: 820px;">

            <nav style="margin-bottom: 32px; font-family: 'JetBrains Mono', monospace; font-size: 0.78rem;">
                <a href="{{ route('downloads') }}" style="color:#6366f1; text-decoration:none;"><i class="fa-solid fa-arrow-left me-2"></i>Downloads</a>
            </nav>

            <header style="display:flex; align-items:flex-start; gap:18px; margin-bottom: 32px;">
                @if($project->icon)
                    <span style="width:58px;height:58px;border-radius:14px;background:rgba(99,102,241,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="{{ $project->icon }}" style="color:#6366f1;font-size:1.5rem;"></i></span>
                @endif
                <div>
                    @if($project->category)
                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.66rem; font-weight: 600; letter-spacing: 0.1em; text-transform: uppercase; color: #6366f1;">// {{ $project->category }}</span>
                    @endif
                    <h1 style="font-family: 'Inter', sans-serif; font-size: 2.2rem; font-weight: 700; color: #f1f5f9; letter-spacing: -0.02em; margin: 4px 0 0;">{{ $project->title }}</h1>
                </div>
            </header>

            @if($project->description)
                <div style="font-family: 'Inter', sans-serif; font-size: 1.02rem; color: #cbd5e1; line-height: 1.8; margin-bottom: 40px; white-space: pre-line;">{{ $project->description }}</div>
            @endif

            @if(session('download_unavailable'))
                <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; border-radius: 10px; padding: 14px 18px; font-family: 'Inter', sans-serif; font-size: 0.9rem; margin-bottom: 24px;">
                    O arquivo <strong>{{ session('download_unavailable') }}</strong> está indisponível no momento.
                </div>
            @endif

            <h2 style="font-family: 'Inter', sans-serif; font-size: 1.15rem; font-weight: 700; color: #f1f5f9; margin-bottom: 18px;">Arquivos</h2>
            @include('partials.file-list', ['files' => $project->availableFiles])

        </div>
    </section>

@endsection
