<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restringe o painel aos administradores. Admin único do site → flag is_admin
 * no usuário (sem Spatie). Quem não é admin é mandado para o login.
 */
class EnsureIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || ! Auth::user()->is_admin) {
            Auth::logout();

            return redirect()->route('login')
                ->with('error', 'Acesso restrito ao administrador.');
        }

        return $next($request);
    }
}
