@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Workspaces')

@section('content')
<div class="aim">
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $T = \App\Services\AiMemory\AiMemoryTime::class;
            $n = fn ($v) => number_format((int) $v, 0, ',', '.');
            // Barra proporcional: dá para ver de relance onde a memória se concentra.
            $maxObs = max(1, (int) collect($workspaces)->max('observations'));
        @endphp

        <section class="admin-card aim-card">
            <header class="aim-card__head">
                <div>
                    <h2>Workspaces</h2>
                    <p class="aim-sub">{{ count($workspaces) }} na memória · atividade mais recente primeiro</p>
                </div>
            </header>

            @if(empty($workspaces))
                <div class="aim-blank">
                    <i class="fa-solid fa-layer-group"></i>
                    <p>Nenhum workspace na memória ainda.</p>
                    <p>Um workspace aparece aqui na primeira vez que um agente abre sessão num projeto dentro dele.</p>
                </div>
            @else
                <div class="aim-scroll">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th scope="col">Workspace</th>
                                <th scope="col">Projetos</th>
                                <th scope="col">Páginas</th>
                                <th scope="col">Sessões</th>
                                <th scope="col">Observações</th>
                                <th scope="col">Última atividade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workspaces as $w)
                                <tr>
                                    <td><span class="aim-strong">{{ $w->name }}</span></td>
                                    <td class="aim-mono">{{ $n($w->projects) }}</td>
                                    <td class="aim-mono">{{ $n($w->pages) }}</td>
                                    <td class="aim-mono">{{ $n($w->sessions) }}</td>
                                    <td>
                                        <span class="aim-bar">
                                            <span class="aim-bar__track"><span class="aim-bar__fill" style="width:{{ round($w->observations / $maxObs * 100, 1) }}%"></span></span>
                                            <span class="aim-bar__n">{{ $n($w->observations) }}</span>
                                        </span>
                                    </td>
                                    <td class="aim-when" title="{{ $T::format($w->last_session_at) }}">{{ $T::human($w->last_session_at) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endunless
</div>
@endsection
