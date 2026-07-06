@extends('layouts.app')

@section('title', 'Downloads')
@section('description', 'Projetos e ferramentas de Samir Hanna Verza disponíveis para download.')

@section('content')

    <section class="s-section" style="padding-top:clamp(7rem,11vw,10rem); position:relative;">
        <div class="s-aura"></div>
        <div class="container" style="position:relative; z-index:1;">

            <div style="max-width:640px; margin-bottom:clamp(2.4rem,5vw,3.6rem);">
                <span class="s-kicker">Downloads</span>
                <h1 class="s-display" style="font-size:clamp(2.2rem,5vw,3.4rem);">Projetos para baixar</h1>
                <p class="s-lead" style="margin-top:1rem;">
                    {{ $totalFiles }} arquivo{{ $totalFiles === 1 ? '' : 's' }} em {{ $projects->count() }} projeto{{ $projects->count() === 1 ? '' : 's' }}. Sempre a versão mais recente.
                </p>
            </div>

            @if(session('download_unavailable'))
                <div class="s-card" style="max-width:820px; margin:0 0 24px; padding:14px 18px; border-color:rgba(248,113,113,0.3); background:rgba(248,113,113,0.08);">
                    <span class="s-body" style="color:#fca5a5; font-size:0.92rem;">O arquivo <strong>{{ session('download_unavailable') }}</strong> está indisponível no momento.</span>
                </div>
            @endif

            <div class="s-stack" style="max-width:880px; gap:16px;">
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
                    <article class="s-card s-card--pad" style="padding:24px 28px;">
                        <div class="d-flex align-items-start gap-3 flex-wrap">
                            @if($project->icon)
                                <span class="s-icon"><i class="{{ $project->icon }}"></i></span>
                            @endif
                            <div style="flex:1; min-width:220px;">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h2 class="s-h3" style="font-size:1.25rem;">
                                        <a href="{{ route('project.show', $project) }}" style="color:inherit;">{{ $project->title }}</a>
                                    </h2>
                                    @if($project->category)<span class="s-tag">{{ $project->category }}</span>@endif
                                    @if($project->external_url)
                                        <a href="{{ $project->external_url }}" target="_blank" rel="noopener" class="s-tag s-tag--accent" style="text-decoration:none;"><i class="fa-solid fa-arrow-up-right-from-square"></i> usar online</a>
                                    @endif
                                </div>

                                @if($project->description)
                                    <p class="s-body s-muted" style="font-size:0.92rem; margin:8px 0 0; max-width:60ch;">{{ Str::limit($project->description, 160) }}</p>
                                @endif

                                @if($files->isNotEmpty())
                                    <div class="s-meta d-flex align-items-center flex-wrap" style="gap:8px 14px; margin-top:13px;">
                                        @foreach($oses as $os)
                                            <span class="d-inline-flex align-items-center" style="gap:5px;"><span aria-hidden="true" style="width:6px;height:6px;border-radius:50%;background:var(--s-accent);display:inline-block;"></span>{{ \App\Support\OsDetector::label($os) }}</span>
                                        @endforeach
                                        @if($latest && $latest->version)<span>· v{{ $latest->version }}</span>@endif
                                        @if($latest && $latest->effective_date)<span>· {{ $latest->effective_date->translatedFormat('d M Y') }}</span>@endif
                                        @if($arches->isNotEmpty())<span>· {{ $arches->implode('·') }}</span>@endif
                                        <span>· {{ $files->count() }} arquivo{{ $files->count() === 1 ? '' : 's' }}</span>
                                        @if($dlTotal > 0)<span>· {{ number_format($dlTotal, 0, ',', '.') }} downloads</span>@endif
                                    </div>
                                @else
                                    <div class="s-meta" style="margin-top:13px;">Arquivos em breve.</div>
                                @endif
                            </div>

                            <a href="{{ route('project.show', $project) }}" class="s-btn s-btn--sm m-0" style="align-self:center; flex-shrink:0;">
                                <i class="fa-solid fa-download"></i> Baixar
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="s-card s-card--pad" style="text-align:center; padding:70px 0;">
                        <span class="s-meta">Nenhum projeto publicado ainda — em breve.</span>
                    </div>
                @endforelse
            </div>

        </div>
    </section>

@endsection
