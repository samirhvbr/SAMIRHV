@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Observações')

@section('content')
<div class="aim">
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $T = \App\Services\AiMemory\AiMemoryTime::class;
            // Importância é uma escala 0–10: medidor lê melhor que um número solto.
            $impClass = fn ($i) => $i >= 8 ? 'aim-imp--high' : ($i >= 5 ? 'aim-imp--mid' : '');
            $hasFilters = ! empty(array_filter($filters, fn ($v) => $v !== null && $v !== ''));
        @endphp

        <section class="admin-card aim-card">
            <header class="aim-card__head">
                <div>
                    <h2>Observações</h2>
                    <p class="aim-sub">{{ number_format($observations->total(), 0, ',', '.') }} fatos aprendidos · mais recentes primeiro</p>
                </div>
            </header>

            <form method="GET" action="{{ route('admin.ai-memory.observations') }}" class="aim-filters">
                <div class="form-row">
                    <label for="f-kind">Tipo</label>
                    <select name="kind" id="f-kind">
                        <option value="">Todos</option>
                        @foreach($kinds as $k)
                            <option value="{{ $k }}" @selected(($filters['kind'] ?? null) === $k)>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label for="f-imp">Importância mín.</label>
                    <select name="importance" id="f-imp">
                        <option value="">Qualquer</option>
                        @foreach([3 => '≥ 3', 5 => '≥ 5', 8 => '≥ 8'] as $v => $lbl)
                            <option value="{{ $v }}" @selected((int) ($filters['importance'] ?? 0) === $v)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label for="f-project">Projeto</label>
                    <select name="project" id="f-project">
                        <option value="">Todos</option>
                        @foreach($projectOptions as $opt)
                            <option value="{{ $opt->id_hex }}" @selected(($filters['project'] ?? null) === $opt->id_hex)>{{ $opt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label for="f-days">Período</label>
                    <select name="days" id="f-days">
                        <option value="">Tudo</option>
                        @foreach([1 => 'Hoje', 7 => '7 dias', 30 => '30 dias', 90 => '90 dias'] as $v => $lbl)
                            <option value="{{ $v }}" @selected((int) ($filters['days'] ?? 0) === $v)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="aim-filters__go">
                    <button type="submit" class="admin-btn admin-btn-primary">Filtrar</button>
                    @if($hasFilters)<a href="{{ route('admin.ai-memory.observations') }}" class="admin-btn">Limpar</a>@endif
                </div>
            </form>

            @if($observations->isEmpty())
                <div class="aim-blank">
                    <i class="fa-solid fa-lightbulb"></i>
                    <p>{{ $hasFilters ? 'Nenhuma observação para este filtro.' : 'Nenhuma observação registrada ainda.' }}</p>
                    <p>{{ $hasFilters ? 'Baixe a importância mínima ou amplie o período.' : 'Cada observação é um fato que um agente aprendeu durante uma sessão.' }}</p>
                </div>
            @else
                <div class="aim-scroll">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th scope="col">Tipo</th>
                                <th scope="col">Título</th>
                                <th scope="col">Importância</th>
                                <th scope="col">Projeto</th>
                                <th scope="col">Quando</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($observations as $o)
                                <tr>
                                    <td><span class="aim-chip">{{ $o->kind }}</span></td>
                                    <td>
                                        <a href="{{ route('admin.ai-memory.observations.show', $o->id_hex) }}" class="aim-strong">{{ \Illuminate\Support\Str::limit($o->title, 90) }}</a>
                                        @if($o->session_hex)
                                            <div><a href="{{ route('admin.ai-memory.sessions.show', $o->session_hex) }}" class="aim-mono" title="Abrir a sessão que gerou esta observação">na sessão ↗</a></div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="aim-imp {{ $impClass($o->importance) }}" title="Importância {{ $o->importance }} de 10">
                                            <span class="aim-imp__track"><span class="aim-imp__fill" style="width:{{ min(100, (int) $o->importance * 10) }}%"></span></span>
                                            <span class="aim-imp__n">{{ $o->importance }}</span>
                                        </span>
                                    </td>
                                    <td class="aim-mono">{{ $o->project }}</td>
                                    <td class="aim-when" title="{{ $T::format($o->created_at) }}">{{ $T::human($o->created_at) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $observations->links() }}
            @endif
        </section>
    @endunless
</div>
@endsection
