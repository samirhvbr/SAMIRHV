@extends('admin.layouts.app')

@section('title', 'AI-MEMORY · Páginas')

@section('content')
<div class="aim">
    @include('admin.ai-memory._tabs')

    @unless($available)
        @include('admin.ai-memory._unavailable')
    @else
        @php
            $T = \App\Services\AiMemory\AiMemoryTime::class;
            $tierChip = ['working' => 'aim-chip--warn', 'episodic' => 'aim-chip--accent', 'semantic' => 'aim-chip--ok'];
            $projectName = collect($projectOptions)->firstWhere('id_hex', $project)?->name;
        @endphp

        <section class="admin-card aim-card">
            <header class="aim-card__head">
                <div>
                    <h2>Páginas</h2>
                    <p class="aim-sub">
                        {{ number_format($pages->total(), 0, ',', '.') }} na versão atual
                        @if($projectName) · projeto {{ $projectName }} @endif
                    </p>
                </div>
            </header>

            <form method="GET" action="{{ route('admin.ai-memory.pages') }}" class="aim-filters">
                <div class="form-row">
                    <label for="f-project">Projeto</label>
                    <select name="project" id="f-project" onchange="this.form.submit()">
                        <option value="">Todos</option>
                        @foreach($projectOptions as $opt)
                            <option value="{{ $opt->id_hex }}" @selected($project === $opt->id_hex)>{{ $opt->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="aim-filters__go">
                    <button type="submit" class="admin-btn admin-btn-primary">Filtrar</button>
                    @if($project)<a href="{{ route('admin.ai-memory.pages') }}" class="admin-btn">Limpar</a>@endif
                </div>
            </form>

            @if($pages->isEmpty())
                <div class="aim-blank">
                    <i class="fa-solid fa-file-lines"></i>
                    <p>{{ $project ? 'Nenhuma página neste projeto.' : 'Nenhuma página consolidada ainda.' }}</p>
                    <p>Páginas são o conhecimento já consolidado — nascem de <code>memory_write_page</code> ou da consolidação automática das sessões.</p>
                </div>
            @else
                <div class="aim-scroll">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th scope="col">Título</th>
                                <th scope="col">Caminho</th>
                                <th scope="col">Tier</th>
                                <th scope="col">Projeto</th>
                                <th scope="col">Atualizada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pages as $p)
                                <tr>
                                    <td>
                                        @if($p->pinned)
                                            <i class="fa-solid fa-thumbtack" style="color:var(--warn);font-size:.68rem;margin-right:5px" title="Fixada"></i>
                                        @endif
                                        <a href="{{ route('admin.ai-memory.pages.show', $p->id_hex) }}" class="aim-strong">{{ $p->title }}</a>
                                    </td>
                                    <td><span class="aim-path" title="{{ $p->path }}">{{ $p->path }}</span></td>
                                    <td><span class="aim-chip {{ $tierChip[$p->tier] ?? '' }}">{{ $p->tier }}</span></td>
                                    <td class="aim-mono">{{ $p->project }}</td>
                                    <td class="aim-when" title="{{ $T::format($p->updated_at) }}">{{ $T::human($p->updated_at) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{ $pages->links() }}
            @endif
        </section>
    @endunless
</div>
@endsection
