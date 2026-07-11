@extends('layouts.app')

@section('title', 'Downloads')
@section('description', 'Projetos e ferramentas de Samir Hanna Verza disponíveis para download.')

@section('content')

    <section class="s-page-hero">
        <div class="s-aura"></div>
        <div class="container">

            <div class="s-page-hero__content">
                <span class="s-kicker">Downloads</span>
                <h1 class="s-display" style="font-size:clamp(2.2rem,5vw,3.4rem);">Projetos para baixar</h1>
                <p class="s-lead" style="margin-top:1rem;">
                    Releases diretos para as ferramentas que estou construindo. Escolha um projeto, confira a versão e baixe.
                </p>
                <div class="s-page-hero__meta">
                    <span>{{ $projects->count() }} projeto{{ $projects->count() === 1 ? '' : 's' }}</span>
                    <span>{{ $totalFiles === 1 ? '1 arquivo disponível' : $totalFiles.' arquivos disponíveis' }}</span>
                    <span>releases versionados</span>
                </div>
            </div>

            @if(session('download_unavailable'))
                <div class="s-card" style="max-width:820px; margin:0 0 24px; padding:14px 18px; border-color:rgba(248,113,113,0.3); background:rgba(248,113,113,0.08);">
                    <span class="s-body" style="color:#fca5a5; font-size:0.92rem;">O arquivo <strong>{{ session('download_unavailable') }}</strong> está indisponível no momento.</span>
                </div>
            @endif

            <div class="s-project-list">
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
                    <article class="s-card s-download-card">
                        <div class="s-download-card__top">
                            @if($project->icon)
                                <span class="s-icon"><i class="{{ $project->icon }}"></i></span>
                            @endif
                            <div class="s-download-card__body">
                                <div class="s-download-card__title-row">
                                    <h2 class="s-h3" style="font-size:1.25rem;">
                                        <a href="{{ $project->public_url }}" @if($project->redirectsToSite()) target="_blank" rel="noopener" @endif style="color:inherit;">{{ $project->title }}</a>
                                    </h2>
                                    @if($project->category)<span class="s-tag">{{ $project->category }}</span>@endif
                                    {{-- "usar online" só no híbrido (tem site + downloads aqui); o link puro ganha o botão "Acessar site" abaixo. --}}
                                    @if($project->isHybrid())
                                        <a href="{{ $project->external_url }}" target="_blank" rel="noopener" class="s-tag s-tag--accent" style="text-decoration:none;"><i class="fa-solid fa-arrow-up-right-from-square"></i> usar online</a>
                                    @endif
                                </div>

                                @if($project->description)
                                    <p class="s-download-card__description">{{ Str::limit($project->description, 160) }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="s-download-card__footer">
                            @if($project->redirectsToSite())
                                {{-- Projeto-link (ex. SShvTerm): mora no site oficial. Botão ghost = ação que sai daqui. --}}
                                <div class="s-release-meta">
                                    <span class="s-release-meta__os">Site oficial</span>
                                    <span>{{ preg_replace('#^www\.#', '', parse_url($project->external_url, PHP_URL_HOST) ?? '') }}</span>
                                </div>
                                <a href="{{ $project->external_url }}" target="_blank" rel="noopener" class="s-btn s-btn--ghost s-btn--sm m-0">
                                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Acessar site
                                </a>
                            @elseif($project->hasCustomPage())
                                {{-- Documentação (ex. ai-usagebar): sem binários aqui — instala por gerenciador, guia por SO. --}}
                                <div class="s-release-meta">
                                    <span class="s-release-meta__os">Multiplataforma</span>
                                    <span>Linux · macOS · Windows</span>
                                    <span>instala por gerenciador</span>
                                </div>
                                <a href="{{ route('project.show', $project) }}" class="s-btn s-btn--sm m-0">
                                    <i class="fa-solid fa-terminal"></i> Instalar
                                </a>
                            @else
                                {{-- Download / híbrido: procedência à mostra (SO, versão, data, arquitetura, downloads). --}}
                                @if($files->isNotEmpty())
                                    <div class="s-release-meta">
                                        @foreach($oses as $os)
                                            <span class="s-release-meta__os">{{ \App\Support\OsDetector::label($os) }}</span>
                                        @endforeach
                                        @if($latest && $latest->version)<span class="s-release-meta__primary">v{{ $latest->version }}</span>@endif
                                        @if($latest && $latest->effective_date)<span>{{ $latest->effective_date->translatedFormat('d M Y') }}</span>@endif
                                        @if($arches->isNotEmpty())<span>{{ $arches->implode(' · ') }}</span>@endif
                                        @if($dlTotal > 0)<span>{{ number_format($dlTotal, 0, ',', '.') }} downloads</span>@endif
                                    </div>
                                @else
                                    <span class="s-meta">Arquivos em breve.</span>
                                @endif
                                <a href="{{ route('project.show', $project) }}" class="s-btn s-btn--sm m-0">
                                    <i class="fa-solid fa-download"></i> Ver arquivos
                                </a>
                            @endif
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
