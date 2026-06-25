<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AuthEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Auditoria de Acesso — duas frentes, em abas (?tab=):
 *  - actions : ações do admin sobre projetos/arquivos (tabela activity_logs).
 *  - logins  : acessos ao painel — login/falha/logout (tabela auth_events).
 */
class AccessAuditController extends Controller
{
    private const TABS = ['actions', 'logins'];

    public function index(Request $request): View
    {
        $tab = in_array($request->input('tab'), self::TABS, true) ? $request->input('tab') : 'actions';

        $data = match ($tab) {
            'logins' => $this->loginsTab($request),
            default => $this->actionsTab($request),
        };

        return view('admin.access-audit.index', $data + ['tab' => $tab]);
    }

    /** Ações do admin sobre projetos/arquivos (activity_logs). */
    private function actionsTab(Request $request): array
    {
        $filters = [
            'event' => $request->input('event'),
            'user' => $request->input('user'),
            'ip' => $request->input('ip'),
            'days' => $request->input('days'),
        ];

        $query = ActivityLog::with('user')->latest();
        if (filled($filters['event'])) {
            $query->where('event', $filters['event']);
        }
        if (filled($filters['user'])) {
            $query->where('user_id', $filters['user']);
        }
        if (filled($filters['ip'])) {
            $query->where('ip_address', 'like', $filters['ip'].'%');
        }
        if (filled($filters['days'])) {
            $query->where('created_at', '>=', now()->subDays((int) $filters['days']));
        }

        $logs = $query->paginate(50)->withQueryString();

        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::where('created_at', '>=', now()->startOfDay())->count(),
            'admins' => ActivityLog::whereNotNull('user_id')->distinct()->count('user_id'),
            'ips' => ActivityLog::whereNotNull('ip_address')->distinct()->count('ip_address'),
        ];

        $events = ActivityLog::query()->select('event')->distinct()->orderBy('event')->pluck('event');
        $adminIds = ActivityLog::query()->whereNotNull('user_id')->distinct()->pluck('user_id');
        $admins = User::whereIn('id', $adminIds)->orderBy('email')->get(['id', 'name', 'email']);

        return compact('logs', 'stats', 'filters', 'events', 'admins');
    }

    /** Acessos ao painel (auth_events). */
    private function loginsTab(Request $request): array
    {
        $filters = [
            'event' => $request->input('event'),
            'ip' => $request->input('ip'),
            'days' => $request->input('days'),
        ];

        $query = AuthEvent::with('user')->latest('created_at');
        if (filled($filters['event'])) {
            $query->where('event', $filters['event']);
        }
        if (filled($filters['ip'])) {
            $query->where('ip_address', 'like', $filters['ip'].'%');
        }
        if (filled($filters['days'])) {
            $query->where('created_at', '>=', now()->subDays((int) $filters['days']));
        }

        $logs = $query->paginate(50)->withQueryString();

        $today = now()->startOfDay();
        $stats = [
            'logins' => AuthEvent::where('event', 'login')->count(),
            'logins_today' => AuthEvent::where('event', 'login')->where('created_at', '>=', $today)->count(),
            'failed' => AuthEvent::where('event', 'failed')->count(),
            'failed_today' => AuthEvent::where('event', 'failed')->where('created_at', '>=', $today)->count(),
        ];

        return compact('logs', 'stats', 'filters');
    }
}
