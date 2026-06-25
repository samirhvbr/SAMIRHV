@extends('admin.layouts.app')

@section('title', 'Auditoria de Downloads')

@php
    $tz = 'America/Sao_Paulo';
    $deviceLabels = ['desktop' => 'Desktop', 'mobile' => 'Mobile', 'tablet' => 'Tablet', 'bot' => 'Bot'];
@endphp

@section('content')

    {{-- KPIs do dia --}}
    <div class="admin-stats-grid">
        <div class="admin-stat"><div class="label">Acessos hoje (únicos)</div><div class="value">{{ $cards['visits_today'] }}</div></div>
        <div class="admin-stat"><div class="label">Logins hoje</div><div class="value">{{ $cards['logins_today'] }}</div></div>
        <div class="admin-stat"><div class="label">Downloads hoje</div><div class="value">{{ $cards['downloads_today'] }}</div></div>
        <div class="admin-stat"><div class="label">Bots hoje</div><div class="value">{{ $cards['bots_today'] }}</div></div>
    </div>

    {{-- Gráficos por dia --}}
    <div class="an-grid-2">
        <div class="admin-card">
            <h2>Acessos por dia <span class="card-sub">(14d, únicos, sem bots)</span></h2>
            @php $vals = array_values($visitsByDay); $max = $vals ? max(1, max($vals)) : 1; @endphp
            <div class="an-chart">
                @foreach($visitsByDay as $day => $total)
                    <div class="an-bar" style="height:{{ max(2, round($total / $max * 100)) }}%" title="{{ $day }}: {{ $total }}"></div>
                @endforeach
            </div>
            <div class="an-xlabels">
                @foreach($visitsByDay as $day => $total)
                    <span>{{ \Illuminate\Support\Carbon::parse($day)->format('d/m') }}</span>
                @endforeach
            </div>
        </div>

        <div class="admin-card">
            <h2>Downloads por dia <span class="card-sub">(14d, sem bots)</span></h2>
            @php $vals = array_values($downloadsByDay); $max = $vals ? max(1, max($vals)) : 1; @endphp
            <div class="an-chart">
                @foreach($downloadsByDay as $day => $total)
                    <div class="an-bar" style="height:{{ max(2, round($total / $max * 100)) }}%" title="{{ $day }}: {{ $total }}"></div>
                @endforeach
            </div>
            <div class="an-xlabels">
                @foreach($downloadsByDay as $day => $total)
                    <span>{{ \Illuminate\Support\Carbon::parse($day)->format('d/m') }}</span>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Top páginas / IPs --}}
    <div class="an-grid-2">
        <div class="admin-card">
            <h2>Páginas mais acessadas <span class="card-sub">(30d)</span></h2>
            @if(empty($topPages))
                <p class="card-sub">Sem dados.</p>
            @else
                <ul class="an-list">
                    @foreach($topPages as $p)
                        <li><span><code>{{ $p['path'] }}</code></span><span class="total">{{ $p['total'] }}</span></li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="admin-card">
            <h2>IPs que mais acessaram <span class="card-sub">(30d)</span></h2>
            @if(empty($topIps))
                <p class="card-sub">Sem dados.</p>
            @else
                <ul class="an-list">
                    @foreach($topIps as $row)
                        <li>
                            <span><code>{{ $row['ip'] }}</code> <span class="card-sub">· {{ $row['last_at']->timezone($tz)->diffForHumans() }}</span></span>
                            <span class="total">{{ $row['total'] }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- Dispositivos / Navegadores / Downloads por projeto --}}
    <div class="an-grid-3">
        <div class="admin-card">
            <h2>Dispositivos</h2>
            @php $vals = array_values($byDevice); $max = $vals ? max(1, max($vals)) : 1; @endphp
            @forelse($byDevice as $device => $total)
                <div class="an-hbar-row">
                    <span class="lbl">{{ $deviceLabels[$device] ?? ucfirst((string) $device) }}</span>
                    <span class="an-hbar-track"><span class="an-hbar" style="width:{{ round($total / $max * 100) }}%"></span></span>
                    <span class="val">{{ $total }}</span>
                </div>
            @empty
                <p class="card-sub">Sem dados.</p>
            @endforelse
        </div>

        <div class="admin-card">
            <h2>Navegadores</h2>
            @php $vals = array_values($byBrowser); $max = $vals ? max(1, max($vals)) : 1; @endphp
            @forelse($byBrowser as $browser => $total)
                <div class="an-hbar-row">
                    <span class="lbl">{{ $browser ?: 'Outro' }}</span>
                    <span class="an-hbar-track"><span class="an-hbar" style="width:{{ round($total / $max * 100) }}%"></span></span>
                    <span class="val">{{ $total }}</span>
                </div>
            @empty
                <p class="card-sub">Sem dados.</p>
            @endforelse
        </div>

        <div class="admin-card">
            <h2>Downloads por projeto</h2>
            @php $vals = array_values($downloadsByProject); $max = $vals ? max(1, max($vals)) : 1; @endphp
            @forelse($downloadsByProject as $title => $total)
                <div class="an-hbar-row">
                    <span class="lbl" title="{{ $title }}">{{ \Illuminate\Support\Str::limit($title, 12) }}</span>
                    <span class="an-hbar-track"><span class="an-hbar" style="width:{{ round($total / $max * 100) }}%"></span></span>
                    <span class="val">{{ $total }}</span>
                </div>
            @empty
                <p class="card-sub">Sem dados.</p>
            @endforelse
        </div>
    </div>

    {{-- Bots (separados) --}}
    <div class="admin-card">
        <h2>Bots <span class="card-sub">— {{ $bots['visits'] }} acessos · {{ $bots['downloads'] }} downloads · 30 dias</span></h2>
        @php $vals = array_values($bots['by_day']); $max = $vals ? max(1, max($vals)) : 1; @endphp
        <div class="an-grid-2" style="align-items:start">
            <div>
                <div class="an-chart">
                    @foreach($bots['by_day'] as $day => $total)
                        <div class="an-bar bot" style="height:{{ max(2, round($total / $max * 100)) }}%" title="{{ $day }}: {{ $total }}"></div>
                    @endforeach
                </div>
                <div class="an-xlabels">
                    @foreach($bots['by_day'] as $day => $total)
                        <span>{{ \Illuminate\Support\Carbon::parse($day)->format('d/m') }}</span>
                    @endforeach
                </div>
            </div>
            <div>
                @if(empty($bots['top']))
                    <p class="card-sub">Nenhum bot registrado.</p>
                @else
                    <ul class="an-list">
                        @foreach($bots['top'] as $b)
                            <li><span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:340px" title="{{ $b['ua'] }}"><code>{{ \Illuminate\Support\Str::limit($b['ua'], 52) }}</code></span><span class="total">{{ $b['total'] }}</span></li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    {{-- Top arquivos (chips, filtram a tabela) --}}
    @if(! empty($topFiles))
        <div class="admin-card">
            <h2>Arquivos mais baixados <span class="card-sub">(30d)</span></h2>
            <div class="chips">
                @foreach($topFiles as $f)
                    <span class="chip">{{ $f['label'] }} <b>{{ $f['total'] }}</b></span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Registros de download (com filtros) --}}
    <div class="admin-card">
        <h2>Registros de download</h2>

        <form method="GET" action="{{ route('admin.audit.index') }}" class="filters">
            <div class="form-row">
                <label>IP (prefixo)</label>
                <input type="text" name="ip" value="{{ $filters['ip'] ?? '' }}" placeholder="ex: 200.1">
            </div>
            <div class="form-row">
                <label>Projeto</label>
                <select name="project">
                    <option value="">Todos</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" @selected(($filters['project'] ?? null) == $p->id)>{{ $p->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-row">
                <label>Arquivo</label>
                <select name="file">
                    <option value="">Todos</option>
                    @foreach($files as $f)
                        <option value="{{ $f->id }}" @selected(($filters['file'] ?? null) == $f->id)>{{ $f->label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-row">
                <label>Período</label>
                <select name="days">
                    <option value="">Tudo</option>
                    <option value="1" @selected(($filters['days'] ?? null) == 1)>Hoje</option>
                    <option value="7" @selected(($filters['days'] ?? null) == 7)>7 dias</option>
                    <option value="30" @selected(($filters['days'] ?? null) == 30)>30 dias</option>
                    <option value="90" @selected(($filters['days'] ?? null) == 90)>90 dias</option>
                </select>
            </div>
            <button type="submit" class="admin-btn admin-btn-primary">Filtrar</button>
            <a href="{{ route('admin.audit.index') }}" class="admin-btn">Limpar</a>
        </form>

        <div style="overflow-x:auto">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Projeto</th>
                        <th>Arquivo</th>
                        <th>Versão</th>
                        <th>IP</th>
                        <th>Origem</th>
                        <th>Cliente</th>
                        <th>Idioma</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr class="{{ $log->is_bot ? 'an-bot-row' : '' }}">
                            <td style="white-space:nowrap">{{ $log->created_at->timezone($tz)->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $log->file?->project?->title ?? '—' }}</td>
                            <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="{{ $log->file?->label }}">{{ $log->file?->label ?? '—' }}</td>
                            <td>{{ $log->file?->version ? 'v'.$log->file->version : '—' }}</td>
                            <td>
                                <a href="{{ route('admin.audit.index', array_merge(request()->query(), ['ip' => $log->ip])) }}"><code>{{ $log->ip }}</code></a>
                            </td>
                            <td>
                                @if($log->is_bot)
                                    <span class="badge badge-warn">bot</span>
                                @else
                                    <span class="badge badge-accent">Site</span>
                                @endif
                            </td>
                            <td title="{{ $log->user_agent }}">{{ $log->client_label }}</td>
                            <td class="muted">{{ $log->locale ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="muted" style="text-align:center;padding:36px">Nenhum registro para o filtro.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">{{ $logs->links() }}</div>
    </div>

@endsection
