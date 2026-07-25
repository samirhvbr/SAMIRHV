@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Projetos')

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
            $maxObs = max(1, (int) collect($projects)->max('observations'));
        @endphp

        <section class="admin-card aim-card">
            <header class="aim-card__head">
                <div>
                    <h2>Projetos</h2>
                    <p class="aim-sub">{{ count($projects) }} na memória · atividade mais recente primeiro</p>
                </div>
            </header>

            @if(empty($projects))
                <div class="aim-blank">
                    <i class="fa-solid fa-folder-open"></i>
                    <p>Nenhum projeto na memória ainda.</p>
                    <p>Um projeto aparece aqui na primeira vez que um agente abre sessão dentro dele.</p>
                </div>
            @else
                <div class="aim-scroll">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th scope="col">Projeto</th>
                                <th scope="col">Repositório</th>
                                <th scope="col">Páginas</th>
                                <th scope="col">Sessões</th>
                                <th scope="col">Observações</th>
                                <th scope="col">Última atividade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projects as $p)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.ai-memory.projects.show', $p->id_hex) }}" class="aim-strong">{{ $p->name }}</a>
                                        <div class="aim-mono">{{ $p->workspace }}</div>
                                    </td>
                                    <td><span class="aim-path" style="--w:300px" title="{{ $p->repo_path }}">{{ $p->repo_path ?? '—' }}</span></td>
                                    <td class="aim-mono">{{ $n($p->pages) }}</td>
                                    <td class="aim-mono">{{ $n($p->sessions) }}</td>
                                    <td>
                                        <span class="aim-bar">
                                            <span class="aim-bar__track"><span class="aim-bar__fill" style="width:{{ round($p->observations / $maxObs * 100, 1) }}%"></span></span>
                                            <span class="aim-bar__n">{{ $n($p->observations) }}</span>
                                        </span>
                                    </td>
                                    <td class="aim-when" title="{{ $T::format($p->last_session_at) }}">{{ $T::human($p->last_session_at) }}</td>
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
