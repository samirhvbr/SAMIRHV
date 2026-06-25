@extends('admin.layouts.app')

@section('title', 'Arquivos — '.$project->title)

@section('topbar-actions')
    <a href="{{ route('admin.projects.edit', $project) }}" class="admin-btn"><i class="fa-solid fa-pen"></i> Editar projeto</a>
    <a href="{{ route('admin.projects.index') }}" class="admin-btn">Voltar</a>
@endsection

@section('content')

    {{-- Upload --}}
    <div class="admin-card" style="max-width:760px">
        <h2>Enviar arquivo</h2>
        <form id="upload-form" method="POST" action="{{ route('admin.projects.files.store', $project) }}" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <label for="file">Arquivo *</label>
                <input type="file" id="file" name="file" required>
                <div class="hint">Limite de 500&nbsp;MB pelo navegador. Para arquivos maiores, use no servidor: <code>php artisan files:add /caminho/arquivo --project={{ $project->slug }}</code></div>
                @error('file')<div class="err">{{ $message }}</div>@enderror
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-row">
                    <label for="label">Rótulo (nome exibido)</label>
                    <input type="text" id="label" name="label" maxlength="255" placeholder="usa o nome do arquivo se vazio">
                </div>
                <div class="form-row">
                    <label for="version">Versão</label>
                    <input type="text" id="version" name="version" maxlength="30" placeholder="ex: 1.0.0">
                </div>
            </div>

            <div id="upload-progress" style="display:none;margin-bottom:16px">
                <div style="background:var(--panel-2);border-radius:6px;height:10px;overflow:hidden">
                    <div id="upload-bar" style="height:100%;width:0;background:linear-gradient(90deg,var(--accent),rgba(99,102,241,.5));transition:width .15s ease"></div>
                </div>
                <div id="upload-pct" style="font-family:'JetBrains Mono',monospace;font-size:.74rem;color:var(--dim);margin-top:6px">0%</div>
            </div>
            <div id="upload-error" class="admin-alert admin-alert-error" style="display:none"></div>

            <button type="submit" id="upload-btn" class="admin-btn admin-btn-primary"><i class="fa-solid fa-upload"></i> Enviar</button>
        </form>
    </div>

    {{-- Lista --}}
    <div class="admin-card" style="padding:0;overflow:hidden">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Arquivo</th>
                    <th>Versão</th>
                    <th>Tamanho</th>
                    <th>Downloads</th>
                    <th>Status</th>
                    <th style="text-align:right">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($files as $file)
                    <tr>
                        <td>
                            <strong style="color:#f1f5f9">{{ $file->label }}</strong>
                            <div class="muted" style="font-family:'JetBrains Mono',monospace;font-size:.7rem">{{ $file->original_name }}@if($file->short_hash) · {{ $file->short_hash }}…@endif</div>
                        </td>
                        <td>{{ $file->version ? 'v'.$file->version : '—' }}</td>
                        <td>{{ $file->human_size }}</td>
                        <td><span style="font-family:'JetBrains Mono',monospace;color:#c7d2fe">{{ number_format($file->downloads_count, 0, ',', '.') }}</span></td>
                        <td>
                            @if(! $file->is_mirrored)
                                <span class="badge badge-warn">sem arquivo</span>
                            @elseif($file->is_available)
                                <span class="badge badge-ok">disponível</span>
                            @else
                                <span class="badge badge-muted">oculto</span>
                            @endif
                        </td>
                        <td style="text-align:right;white-space:nowrap">
                            <form method="POST" action="{{ route('admin.projects.files.available', [$project, $file]) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="admin-btn admin-btn-sm">{{ $file->is_available ? 'Ocultar' : 'Disponibilizar' }}</button>
                            </form>
                            <form method="POST" action="{{ route('admin.projects.files.destroy', [$project, $file]) }}" style="display:inline" onsubmit="return confirm('Remover o arquivo &quot;{{ $file->label }}&quot;?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="admin-btn admin-btn-sm admin-btn-danger"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="muted" style="text-align:center;padding:40px">Nenhum arquivo enviado ainda.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection

@push('scripts')
<script>
(function () {
    const form = document.getElementById('upload-form');
    const btn = document.getElementById('upload-btn');
    const wrap = document.getElementById('upload-progress');
    const bar = document.getElementById('upload-bar');
    const pct = document.getElementById('upload-pct');
    const errBox = document.getElementById('upload-error');

    form.addEventListener('submit', function (e) {
        const fileInput = document.getElementById('file');
        if (!fileInput.files.length) return; // deixa o required nativo agir

        e.preventDefault();
        errBox.style.display = 'none';
        wrap.style.display = 'block';
        btn.disabled = true;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.upload.addEventListener('progress', function (ev) {
            if (ev.lengthComputable) {
                const p = Math.round((ev.loaded / ev.total) * 100);
                bar.style.width = p + '%';
                pct.textContent = p + '%';
            }
        });

        xhr.addEventListener('load', function () {
            if (xhr.status >= 200 && xhr.status < 300) {
                window.location.reload();
            } else if (xhr.status === 413) {
                showError('Arquivo grande demais para o upload via navegador/servidor web. Suba pelo servidor: php artisan files:add /caminho --project={{ $project->slug }}');
            } else {
                let msg = 'Falha no envio (HTTP ' + xhr.status + ').';
                try { const j = JSON.parse(xhr.responseText); if (j.message) msg = j.message; } catch (_) {}
                showError(msg);
            }
        });
        xhr.addEventListener('error', function () { showError('Erro de rede durante o envio.'); });

        xhr.send(new FormData(form));
    });

    function showError(msg) {
        errBox.textContent = msg;
        errBox.style.display = 'block';
        wrap.style.display = 'none';
        btn.disabled = false;
    }
})();
</script>
@endpush
