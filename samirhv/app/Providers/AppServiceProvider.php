<?php

namespace App\Providers;

use App\Models\AuthEvent;
use App\Models\Project;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Uma instância por requisição: memoiza isAvailable() (um único stat/
        // SELECT de sonda) e o PRAGMA query_only entre todos os repositórios
        // do módulo AI-MEMORY.
        $this->app->singleton(\App\Services\AiMemory\AiMemoryDatabase::class);
    }

    public function boot(): void
    {
        $this->configureRateLimiters();
        $this->registerAuthEventListeners();
        $this->shareNavProjects();
        $this->shareAdminVersion();
    }

    /**
     * Expõe a versão da app (raiz `version.md`, um nível acima do Laravel) ao
     * layout do painel. Composer em vez de config('app.version') de propósito:
     * roda em runtime, então não fica "assado" por `config:cache` no deploy —
     * mostra a versão realmente publicada. Lê o arquivo uma vez por processo.
     */
    private function shareAdminVersion(): void
    {
        View::composer('admin.layouts.app', function ($view) {
            static $version = null;
            if ($version === null) {
                $raw = @file_get_contents(base_path('../version.md'));
                $version = $raw !== false ? trim($raw) : '';
            }
            $view->with('appVersion', $version);
        });
    }

    /** Injeta os projetos publicados no menu "Projetos" do layout público. */
    private function shareNavProjects(): void
    {
        View::composer('layouts.app', function ($view) {
            try {
                $projects = Project::published()
                    ->orderBy('sort_order')
                    ->orderByDesc('created_at')
                    ->get(['id', 'title', 'slug', 'icon', 'category', 'external_url', 'redirect_to_site']);
            } catch (\Throwable $e) {
                $projects = collect(); // DB indisponível/migração pendente: menu não quebra a página.
            }

            $view->with('navProjects', $projects);
        });
    }

    /** Limites de tentativa: login 5/min por (IP + e-mail). */
    private function configureRateLimiters(): void
    {
        RateLimiter::for('login', fn (Request $r) => Limit::perMinute(5)
            ->by($r->ip().'|'.Str::lower((string) $r->input('email'))));
    }

    /** Trilha de autenticação do painel (login/falha/logout) → auth_events. */
    private function registerAuthEventListeners(): void
    {
        Event::listen(Login::class, function (Login $event) {
            $event->user->forceFill([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ])->saveQuietly();
            $this->recordAuthEvent('login', $event->user->id ?? null, $event->user->email ?? null);
        });

        Event::listen(Failed::class, fn (Failed $e) => $this->recordAuthEvent(
            'failed', null, $e->credentials['email'] ?? null
        ));

        Event::listen(Logout::class, fn (Logout $e) => $this->recordAuthEvent(
            'logout', $e->user?->id, $e->user?->email
        ));
    }

    /** Grava um evento de autenticação (best-effort; nunca bloqueia o login). */
    private function recordAuthEvent(string $event, int|string|null $userId, ?string $email): void
    {
        try {
            AuthEvent::create([
                'user_id' => $userId,
                'email' => $email ? Str::lower(mb_substr($email, 0, 255)) : null,
                'event' => $event,
                'ip_address' => request()->ip(),
                'user_agent' => substr((string) request()->userAgent(), 0, 1000),
            ]);
        } catch (\Throwable $e) {
            Log::warning('auth_event_failed', ['event' => $event, 'error' => $e->getMessage()]);
        }
    }
}
