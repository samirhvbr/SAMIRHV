{{-- Um grupo de versão: cabeçalho + arquivos. Espera $group (array do DownloadPresenter). --}}
<div style="margin-bottom:6px;">
    <div style="display:flex; align-items:center; gap:10px; margin:18px 0 10px; font-family:'JetBrains Mono',monospace; font-size:.74rem;">
        <span style="color:#cbd5e1; font-weight:600;">{{ $group['version'] ? 'v'.$group['version'] : 'sem versão' }}</span>
        @if($group['is_latest'])<span style="color:#34d399;">● mais recente</span>@endif
        @if($group['date'])<span style="margin-left:auto; color:#64748b;">{{ $group['date']->translatedFormat('d M Y') }}</span>@endif
    </div>
    <div style="display:flex; flex-direction:column; gap:8px;">
        @foreach($group['files'] as $file)
            @include('partials.download-file', ['file' => $file])
        @endforeach
    </div>
</div>
