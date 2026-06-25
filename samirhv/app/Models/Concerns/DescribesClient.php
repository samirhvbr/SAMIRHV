<?php

namespace App\Models\Concerns;

use App\Services\UserAgentParser;

/**
 * Accessor `client_label` para modelos de auditoria que guardam `user_agent` +
 * `ip_address`: devolve "Navegador · SO" (ou "Bot" / "—"). Reúsa a mesma
 * heurística da auditoria de downloads (UserAgentParser).
 */
trait DescribesClient
{
    public function getClientLabelAttribute(): string
    {
        $ua = (string) ($this->user_agent ?? '');
        if (trim($ua) === '') {
            return '—';
        }

        $info = app(UserAgentParser::class)->parse($ua, $this->ip_address ?? null);

        if ($info['is_bot']) {
            return 'Bot';
        }

        return $info['browser'].' · '.$info['os'];
    }
}
