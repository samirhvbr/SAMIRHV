{{-- Lista de arquivos para download. Espera $files (coleção de ProjectFile). --}}
@if($files->isEmpty())
    <p style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; color: #64748b; margin: 0;">// sem arquivos disponíveis</p>
@else
    <div style="display:flex; flex-direction:column; gap:10px;">
        @foreach($files as $file)
            <div style="display:flex; align-items:center; gap:14px; background:#11111c; border:1px solid rgba(99,102,241,0.12); border-radius:10px; padding:14px 16px;">
                <i class="fa-solid fa-file-arrow-down" style="color:#6366f1; font-size:1.1rem; flex-shrink:0;"></i>
                <div style="flex:1; min-width:0;">
                    <div style="font-family: 'Inter', sans-serif; font-size: 0.95rem; font-weight: 600; color: #e2e8f0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                        {{ $file->label }}
                        @if($file->version)<span style="color:#6366f1; font-weight:500;">v{{ $file->version }}</span>@endif
                    </div>
                    <div style="font-family: 'JetBrains Mono', monospace; font-size: 0.68rem; color: #64748b; margin-top:3px;">
                        {{ $file->human_size }}
                        @if($file->short_hash) · sha256 {{ $file->short_hash }}…@endif
                        · {{ number_format($file->downloads_count, 0, ',', '.') }} downloads
                    </div>
                </div>
                @if($file->is_mirrored)
                    <a href="{{ route('download.track', $file) }}" class="button button-rounded m-0" style="background:#6366f1; border-color:#6366f1; color:#fff; font-family:'Inter',sans-serif; font-weight:600; font-size:0.82rem; padding:9px 18px; flex-shrink:0;">
                        <i class="fa-solid fa-download me-2"></i>Baixar
                    </a>
                @else
                    <span class="button button-rounded m-0" style="background:#1f2937; border-color:#374151; color:#6b7280; font-family:'Inter',sans-serif; font-weight:600; font-size:0.82rem; padding:9px 18px; flex-shrink:0; cursor:not-allowed;">em publicação…</span>
                @endif
            </div>
        @endforeach
    </div>
@endif
