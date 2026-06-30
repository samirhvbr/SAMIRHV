@extends('layouts.app')

@section('title', 'Downloads')
@section('description', 'Projetos e ferramentas de Samir Hanna Verza disponíveis para download.')

@section('content')

    <section class="dark include-header" style="background-color: #0d0d14; min-height: 100vh; position: relative; overflow: hidden;">
        <div class="cp-hero-glow"></div>

        <div class="container" style="position: relative; z-index: 1; padding-top: 60px; padding-bottom: 100px;">

            <div class="text-center" style="max-width: 640px; margin: 0 auto 56px;">
                <span class="cp-eyebrow">// DOWNLOADS</span>
                <h1 style="font-family: 'Inter', sans-serif; font-size: 2.75rem; font-weight: 700; color: #f1f5f9; letter-spacing: -0.02em; line-height: 1.15; margin-bottom: 14px;">Projetos para baixar</h1>
                <p style="font-family: 'Inter', sans-serif; font-size: 1.05rem; color: #94a3b8; line-height: 1.7; margin: 0;">
                    {{ $totalFiles }} arquivo(s) em {{ $projects->count() }} projeto(s). Sempre a versão mais recente.
                </p>
            </div>

            @if(session('download_unavailable'))
                <div class="container" style="max-width: 760px; margin-bottom: 28px;">
                    <div style="background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; border-radius: 10px; padding: 14px 18px; font-family: 'Inter', sans-serif; font-size: 0.9rem;">
                        O arquivo <strong>{{ session('download_unavailable') }}</strong> está indisponível no momento.
                    </div>
                </div>
            @endif

            <div style="max-width: 860px; margin: 0 auto; display: flex; flex-direction: column; gap: 22px;">
                @forelse($projects as $project)
                    @php
                        $files = $project->availableFiles;
                        $oses = collect(\App\Support\OsDetector::OSES)
                            ->filter(fn ($os) => $files->contains(fn ($f) => ($f->os ?: 'linux') === $os))
                            ->values();
                        $latest = $files->sortByDesc(fn ($f) => $f->effective_date)->first();
                        $arches = $files->map(fn ($f) => $f->arch)->filter()->unique()->values();
                        $dlTotal = $files->sum('downloads_count');
                    @endphp
                    <article class="cp-glass-card" style="padding: 26px 30px;">
                        <div style="display:flex; align-items:flex-start; gap:16px; flex-wrap:wrap;">
                            @if($project->icon)
                                <span style="width:46px;height:46px;border-radius:11px;background:rgba(99,102,241,0.12);display:flex;align-items:center;justify-content:center;flex-shrink:0;"><i class="{{ $project->icon }}" style="color:#6366f1;font-size:1.2rem;"></i></span>
                            @endif
                            <div style="flex:1; min-width:220px;">
                                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                                    <h2 style="font-family: 'Inter', sans-serif; font-size: 1.3rem; font-weight: 700; color: #f1f5f9; margin: 0;">
                                        <a href="{{ route('project.show', $project) }}" style="color:inherit;text-decoration:none;">{{ $project->title }}</a>
                                    </h2>
                                    @if($project->category)
                                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.64rem; font-weight: 600; letter-spacing: 0.08em; text-transform: uppercase; color: #6366f1; background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.2); border-radius: 4px; padding: 2px 9px;">{{ $project->category }}</span>
                                    @endif
                                </div>

                                @if($project->description)
                                    <p style="font-family: 'Inter', sans-serif; font-size: 0.92rem; color: #94a3b8; line-height: 1.65; margin: 8px 0 0;">{{ Str::limit($project->description, 160) }}</p>
                                @endif

                                @if($files->isNotEmpty())
                                    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-top:13px; font-family:'JetBrains Mono',monospace; font-size:.72rem; color:#94a3b8;">
                                        @foreach($oses as $os)
                                            <span style="display:inline-flex; align-items:center; gap:5px;"><span aria-hidden="true" style="width:7px;height:7px;border-radius:50%;background:#6366f1;display:inline-block;"></span>{{ \App\Support\OsDetector::label($os) }}</span>
                                        @endforeach
                                        @if($latest && $latest->version)<span>· v{{ $latest->version }}</span>@endif
                                        @if($latest && $latest->effective_date)<span>· {{ $latest->effective_date->translatedFormat('d M Y') }}</span>@endif
                                        @if($arches->isNotEmpty())<span>· {{ $arches->implode('·') }}</span>@endif
                                        <span>· {{ $files->count() }} arquivo{{ $files->count() === 1 ? '' : 's' }}</span>
                                        @if($dlTotal > 0)<span>· {{ number_format($dlTotal, 0, ',', '.') }} downloads</span>@endif
                                    </div>
                                @else
                                    <div style="margin-top:13px; font-family:'JetBrains Mono',monospace; font-size:.72rem; color:#64748b;">// arquivos em breve</div>
                                @endif
                            </div>

                            <a href="{{ route('project.show', $project) }}" class="button button-rounded m-0" style="background:#6366f1; border-color:#6366f1; color:#fff; font-family:'Inter',sans-serif; font-weight:600; font-size:.82rem; padding:9px 20px; flex-shrink:0; align-self:center;">
                                <i class="fa-solid fa-download me-2"></i>Baixar
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="text-center" style="padding: 80px 0;">
                        <span style="font-family: 'JetBrains Mono', monospace; color: #64748b;">// nenhum projeto publicado ainda — em breve</span>
                    </div>
                @endforelse
            </div>

        </div>
    </section>

@endsection
