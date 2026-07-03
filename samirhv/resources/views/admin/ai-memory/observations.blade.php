@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Observações')

@section('content')
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $T = \App\Services\AiMemory\AiMemoryTime::class;
            $impBadge = fn ($i) => $i >= 8 ? 'badge-danger' : ($i >= 5 ? 'badge-warn' : 'badge-muted');
        @endphp

        <div class="admin-card">
            <h2>Observações <span class="card-sub">— {{ $observations->total() }}</span></h2>

            <form method="GET" action="{{ route('admin.ai-memory.observations') }}" class="filters">
                <div class="form-row">
                    <label>Tipo</label>
                    <select name="kind">
                        <option value="">Todos</option>
                        @foreach($kinds as $k)
                            <option value="{{ $k }}" @selected(($filters['kind'] ?? null) === $k)>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>Importância mín.</label>
                    <select name="importance">
                        <option value="">Qualquer</option>
                        @foreach([3 => '≥ 3', 5 => '≥ 5', 8 => '≥ 8'] as $v => $lbl)
                            <option value="{{ $v }}" @selected((int) ($filters['importance'] ?? 0) === $v)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>Projeto</label>
                    <select name="project">
                        <option value="">Todos</option>
                        @foreach($projectOptions as $opt)
                            <option value="{{ $opt->id_hex }}" @selected(($filters['project'] ?? null) === $opt->id_hex)>{{ $opt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>Período</label>
                    <select name="days">
                        <option value="">Tudo</option>
                        @foreach([1 => 'Hoje', 7 => '7 dias', 30 => '30 dias', 90 => '90 dias'] as $v => $lbl)
                            <option value="{{ $v }}" @selected((int) ($filters['days'] ?? 0) === $v)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="admin-btn admin-btn-primary">Filtrar</button>
                <a href="{{ route('admin.ai-memory.observations') }}" class="admin-btn">Limpar</a>
            </form>

            <div style="overflow-x:auto">
                <table class="admin-table">
                    <thead>
                        <tr><th>Tipo</th><th>Título</th><th>Imp.</th><th>Projeto</th><th>Sessão</th><th>Data</th></tr>
                    </thead>
                    <tbody>
                        @forelse($observations as $o)
                            <tr>
                                <td><span class="badge badge-muted">{{ $o->kind }}</span></td>
                                <td><a href="{{ route('admin.ai-memory.observations.show', $o->id_hex) }}">{{ \Illuminate\Support\Str::limit($o->title, 80) }}</a></td>
                                <td><span class="badge {{ $impBadge($o->importance) }}">{{ $o->importance }}</span></td>
                                <td class="muted">{{ $o->project }}</td>
                                <td>@if($o->session_hex)<a href="{{ route('admin.ai-memory.sessions.show', $o->session_hex) }}" class="muted"><i class="fa-solid fa-arrow-up-right-from-square" style="font-size:.7rem"></i></a>@else <span class="muted">—</span>@endif</td>
                                <td class="muted" style="white-space:nowrap">{{ $T::format($o->created_at, 'd/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="muted" style="text-align:center;padding:36px">Nenhuma observação para o filtro.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination">{{ $observations->links() }}</div>
        </div>
    @endunless
@endsection
