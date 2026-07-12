{{-- Barra de commits de um repo. Porte de dashboard/_commit_bar.html.erb.
     Log scale por padrão (um repo gigante achataria o resto); linear guardado
     em data-linear-width p/ o toggle. Vars: repo, stats, max, color. --}}
@php
    $tc = (int) $stats['total_commits'];
    $logW = ($max === 0 || $tc === 0) ? 0 : round(log(1 + $tc) * 100 / log(1 + $max), 1);
    $linW = ($max === 0 || $tc === 0) ? 0 : min(100, max(1, round($tc * 100 / $max, 1)));
@endphp
<div class="gh-bar">
    <a href="{{ route('admin.github-view.repos.show', ['owner' => $repo->owner, 'name' => $repo->name]) }}" class="gh-bar__name">{{ $repo->fullName() }}</a>
    <div class="gh-bar__track">
        <div class="gh-bar__fill" data-gh-bar data-log-width="{{ $logW }}%" data-linear-width="{{ $linW }}%"
             style="width: {{ $logW }}%; background: {{ $color }}"></div>
    </div>
    <span class="gh-bar__count">{{ number_format($tc, 0, ',', '.') }}</span>
</div>
