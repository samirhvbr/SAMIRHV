@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Handoffs')

@section('content')
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $T = \App\Services\AiMemory\AiMemoryTime::class;
            $stateBadge = ['open' => 'badge-warn', 'accepted' => 'badge-ok', 'expired' => 'badge-muted'];
        @endphp

        <div class="admin-card">
            <h2>Handoffs <span class="card-sub">— {{ $handoffs->total() }}</span></h2>

            <form method="GET" action="{{ route('admin.ai-memory.handoffs') }}" class="filters">
                <div class="form-row">
                    <label>Estado</label>
                    <select name="state" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        @foreach(['open' => 'Aberto', 'accepted' => 'Aceito', 'expired' => 'Expirado'] as $v => $lbl)
                            <option value="{{ $v }}" @selected($state === $v)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="admin-btn admin-btn-primary">Filtrar</button>
                @if($state)<a href="{{ route('admin.ai-memory.handoffs') }}" class="admin-btn">Limpar</a>@endif
            </form>

            <div style="overflow-x:auto">
                <table class="admin-table">
                    <thead>
                        <tr><th>De</th><th>Para</th><th>Estado</th><th>Projeto</th><th>Perguntas</th><th>Próximos</th><th>Data</th></tr>
                    </thead>
                    <tbody>
                        @forelse($handoffs as $h)
                            <tr>
                                <td><a href="{{ route('admin.ai-memory.handoffs.show', $h->id_hex) }}"><span class="badge badge-accent">{{ $h->from_agent }}</span></a></td>
                                <td>{{ $h->to_agent ? $h->to_agent : '—' }}</td>
                                <td><span class="badge {{ $stateBadge[$h->state] ?? 'badge-muted' }}">{{ $h->state }}</span></td>
                                <td class="muted">{{ $h->project }}</td>
                                <td>{{ $h->open_questions }}</td>
                                <td>{{ $h->next_steps }}</td>
                                <td class="muted" style="white-space:nowrap">{{ $T::format($h->created_at, 'd/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="muted" style="text-align:center;padding:36px">Nenhum handoff.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination">{{ $handoffs->links() }}</div>
        </div>
    @endunless
@endsection
