{{-- Uma linha de arquivo para download. Espera $file (ProjectFile). --}}
<div style="display:flex; align-items:center; gap:14px; background:#11111c; border:1px solid rgba(99,102,241,0.12); border-radius:10px; padding:13px 16px;">
    <div style="flex:1; min-width:0;">
        <div style="font-family:'JetBrains Mono',monospace; font-size:.82rem; color:#e2e8f0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
            {{ $file->original_name ?: $file->label }}
        </div>
        <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-top:7px;">
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
        <a href="{{ route('download.track', $file) }}" class="button button-rounded m-0 dl-btn"><i class="fa-solid fa-download me-2"></i>Baixar</a>
    @else
        <span class="dl-btn-off">em publicação…</span>
    @endif
</div>
