{{-- Uma linha de arquivo para download. Espera $file (ProjectFile). --}}
<div class="dl-file">
    <div style="flex:1; min-width:0;">
        <div class="dl-name">{{ $file->original_name ?: $file->label }}</div>
        <div class="d-flex align-items-center flex-wrap" style="gap:8px; margin-top:7px;">
            @if($file->file_type)<span class="dl-badge">{{ $file->file_type }}</span>@endif
            @if($file->arch)<span class="dl-badge dl-badge-arch">{{ $file->arch }}</span>@endif
            <span class="dl-meta">{{ $file->human_size }}</span>
            @if($file->effective_date)<span class="dl-meta">{{ $file->effective_date->translatedFormat('d M Y') }}</span>@endif
            @if($file->short_hash)
                <button type="button" class="dl-copy" data-copy="{{ $file->sha256 }}" title="Copiar sha256">sha256 {{ $file->short_hash }}… ⧉</button>
            @endif
            <span class="dl-meta">{{ number_format($file->downloads_count, 0, ',', '.') }} downloads</span>
        </div>
    </div>
    @if($file->is_mirrored)
        <a href="{{ route('download.track', $file) }}" class="s-btn s-btn--sm m-0" style="flex-shrink:0;"><i class="fa-solid fa-download"></i> Baixar</a>
    @else
        <span class="dl-btn-off">em publicação…</span>
    @endif
</div>
