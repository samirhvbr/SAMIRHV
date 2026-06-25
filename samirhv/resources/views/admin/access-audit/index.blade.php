@extends('admin.layouts.app')

@section('title', 'Auditoria de Acesso')

@php
    $tz = 'America/Sao_Paulo';
    $badgeFor = function (string $event): string {
        return match (true) {
            str_contains($event, 'delete') => 'badge-danger',
            str_contains($event, 'create'), str_contains($event, 'upload'), $event === 'login' => 'badge-ok',
            str_contains($event, 'unpublish'), str_contains($event, 'unavailable'), $event === 'logout' => 'badge-muted',
            $event === 'failed' => 'badge-danger',
            default => 'badge-accent',
        };
    };
@endphp

@section('content')

    <div class="tabs">
        <a href="{{ route('admin.access-audit.index', ['tab' => 'actions']) }}" class="admin-btn {{ $tab === 'actions' ? 'admin-btn-primary' : '' }}">Ações do admin</a>
        <a href="{{ route('admin.access-audit.index', ['tab' => 'logins']) }}" class="admin-btn {{ $tab === 'logins' ? 'admin-btn-primary' : '' }}">Logins no painel</a>
    </div>

    @if($tab === 'actions')

        <div class="admin-stats-grid">
            <div class="admin-stat"><div class="label">Eventos no total</div><div class="value">{{ $stats['total'] }}</div></div>
            <div class="admin-stat"><div class="label">Hoje</div><div class="value">{{ $stats['today'] }}</div></div>
            <div class="admin-stat"><div class="label">Admins distintos</div><div class="value">{{ $stats['admins'] }}</div></div>
            <div class="admin-stat"><div class="label">IPs distintos</div><div class="value">{{ $stats['ips'] }}</div></div>
        </div>

        <div class="admin-card">
            <form method="GET" action="{{ route('admin.access-audit.index') }}" class="filters">
                <input type="hidden" name="tab" value="actions">
                <div class="form-row">
                    <label>Evento</label>
                    <select name="event">
                        <option value="">Todos</option>
                        @foreach($events as $ev)
                            <option value="{{ $ev }}" @selected(($filters['event'] ?? null) === $ev)>{{ $ev }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>Admin</label>
                    <select name="user">
                        <option value="">Todos</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" @selected(($filters['user'] ?? null) == $admin->id)>{{ $admin->name }} ({{ $admin->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-row">
                    <label>IP (prefixo)</label>
                    <input type="text" name="ip" value="{{ $filters['ip'] ?? '' }}">
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
                <a href="{{ route('admin.access-audit.index', ['tab' => 'actions']) }}" class="admin-btn">Limpar</a>
            </form>

            <div style="overflow-x:auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Admin</th>
                            <th>Evento</th>
                            <th>Descrição</th>
                            <th>Alvo</th>
                            <th>IP</th>
                            <th>Cliente</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td style="white-space:nowrap">{{ $log->created_at->timezone($tz)->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    {{ $log->user?->name ?? 'sistema' }}
                                    @if($log->user)<div class="muted" style="font-size:.72rem">{{ $log->user->email }}</div>@endif
                                </td>
                                <td><span class="badge {{ $badgeFor($log->event) }}">{{ $log->event_label }}</span></td>
                                <td style="max-width:280px">{{ $log->description }}</td>
                                <td class="muted">{{ $log->subject_type ? $log->subject_type.' #'.$log->subject_id : '—' }}</td>
                                <td><code>{{ $log->ip_address }}</code></td>
                                <td title="{{ $log->user_agent }}">{{ $log->client_label }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="muted" style="text-align:center;padding:36px">Nenhum registro.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $logs->links() }}</div>
        </div>

    @else

        <div class="admin-stats-grid">
            <div class="admin-stat"><div class="label">Logins no total</div><div class="value">{{ $stats['logins'] }}</div></div>
            <div class="admin-stat"><div class="label">Logins hoje</div><div class="value">{{ $stats['logins_today'] }}</div></div>
            <div class="admin-stat"><div class="label">Falhas no total</div><div class="value">{{ $stats['failed'] }}</div></div>
            <div class="admin-stat"><div class="label">Falhas hoje</div><div class="value">{{ $stats['failed_today'] }}</div></div>
        </div>

        <div class="admin-card">
            <form method="GET" action="{{ route('admin.access-audit.index') }}" class="filters">
                <input type="hidden" name="tab" value="logins">
                <div class="form-row">
                    <label>Evento</label>
                    <select name="event">
                        <option value="">Todos</option>
                        <option value="login" @selected(($filters['event'] ?? null) === 'login')>Login</option>
                        <option value="failed" @selected(($filters['event'] ?? null) === 'failed')>Falha</option>
                        <option value="logout" @selected(($filters['event'] ?? null) === 'logout')>Logout</option>
                    </select>
                </div>
                <div class="form-row">
                    <label>IP (prefixo)</label>
                    <input type="text" name="ip" value="{{ $filters['ip'] ?? '' }}">
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
                <a href="{{ route('admin.access-audit.index', ['tab' => 'logins']) }}" class="admin-btn">Limpar</a>
            </form>

            <div style="overflow-x:auto">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Data/Hora</th>
                            <th>Evento</th>
                            <th>Usuário / E-mail</th>
                            <th>IP</th>
                            <th>Cliente</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td style="white-space:nowrap">{{ $log->created_at->timezone($tz)->format('d/m/Y H:i:s') }}</td>
                                <td><span class="badge {{ $badgeFor($log->event) }}">{{ $log->event_label }}</span></td>
                                <td>
                                    @if($log->user)
                                        {{ $log->user->name }} <div class="muted" style="font-size:.72rem">{{ $log->user->email }}</div>
                                    @elseif($log->email)
                                        {{ $log->email }} <span class="muted">(tentativa)</span>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                                <td><code>{{ $log->ip_address }}</code></td>
                                <td title="{{ $log->user_agent }}">{{ $log->client_label }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="muted" style="text-align:center;padding:36px">Nenhum registro.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination">{{ $logs->links() }}</div>
        </div>

    @endif

@endsection
