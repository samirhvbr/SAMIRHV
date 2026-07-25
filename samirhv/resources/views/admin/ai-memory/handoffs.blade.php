@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Handoffs')

@section('content')
<div class="aim">
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $T = \App\Services\AiMemory\AiMemoryTime::class;
            $stateChip = ['open' => 'aim-chip--warn', 'accepted' => 'aim-chip--ok', 'expired' => ''];
            $stateLabel = ['open' => 'aberto', 'accepted' => 'aceito', 'expired' => 'expirado'];
        @endphp

        <section class="admin-card aim-card">
            <header class="aim-card__head">
                <div>
                    <h2>Handoffs</h2>
                    <p class="aim-sub">{{ number_format($handoffs->total(), 0, ',', '.') }} passagens de bastão entre sessões</p>
                </div>
            </header>

            <form method="GET" action="{{ route('admin.ai-memory.handoffs') }}" class="aim-filters">
                <div class="form-row">
                    <label for="f-state">Estado</label>
                    <select name="state" id="f-state" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        @foreach(['open' => 'Aberto', 'accepted' => 'Aceito', 'expired' => 'Expirado'] as $v => $lbl)
                            <option value="{{ $v }}" @selected($state === $v)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="aim-filters__go">
                    <button type="submit" class="admin-btn admin-btn-primary">Filtrar</button>
                    @if($state)<a href="{{ route('admin.ai-memory.handoffs') }}" class="admin-btn">Limpar</a>@endif
                </div>
            </form>

            @if($handoffs->isEmpty())
                <div class="aim-blank">
                    <i class="fa-solid fa-right-left"></i>
                    <p>{{ $state ? 'Nenhum handoff neste estado.' : 'Nenhum handoff registrado ainda.' }}</p>
                    <p>Um handoff é o bilhete que uma sessão deixa para a próxima: resumo, perguntas em aberto e próximos passos.</p>
                </div>
            @else
                <div class="aim-scroll">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th scope="col">Passagem</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Projeto</th>
                                <th scope="col">Em aberto</th>
                                <th scope="col">Próximos</th>
                                <th scope="col">Criado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($handoffs as $h)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.ai-memory.handoffs.show', $h->id_hex) }}">
                                            <span class="aim-chip aim-chip--accent">{{ $h->from_agent }}</span>
                                            <span class="aim-mono" style="margin:0 4px">→</span>
                                            <span class="aim-chip">{{ $h->to_agent ?: 'qualquer agente' }}</span>
                                        </a>
                                    </td>
                                    <td><span class="aim-chip {{ $stateChip[$h->state] ?? '' }}">{{ $stateLabel[$h->state] ?? $h->state }}</span></td>
                                    <td class="aim-mono">{{ $h->project }}</td>
                                    <td class="aim-mono">{{ $h->open_questions }}</td>
                                    <td class="aim-mono">{{ $h->next_steps }}</td>
                                    <td class="aim-when" title="{{ $T::format($h->created_at) }}">{{ $T::human($h->created_at) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $handoffs->links() }}
            @endif
        </section>
    @endunless
</div>
@endsection
