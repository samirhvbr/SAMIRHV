<?php

namespace App\Models;

use App\Models\Concerns\DescribesClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trilha de autenticação no painel: login, falha e logout.
 * Escrita pelos listeners em AppServiceProvider. Nunca guarda senhas.
 */
class AuthEvent extends Model
{
    use DescribesClient;

    protected $fillable = [
        'user_id', 'email', 'event', 'ip_address', 'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getEventLabelAttribute(): string
    {
        return match ($this->event) {
            'login' => 'Login',
            'failed' => 'Falha',
            'logout' => 'Logout',
            default => $this->event,
        };
    }
}
