<?php

namespace App\Services;

/**
 * Classificação heurística de user-agent + IP (sem dependência externa): bot,
 * dispositivo, navegador e SO. Conservador, mas:
 *  - user-agent vazio → bot (quase sempre script/scanner);
 *  - IP em faixa conhecida de crawler (Googlebot, Bingbot…) → bot MESMO que o
 *    user-agent finja ser navegador.
 */
class UserAgentParser
{
    /** Tokens de bot/crawler/lib-HTTP (comparados em minúsculo). */
    private const BOT_TOKENS = [
        // crawlers / genéricos
        'bot', 'crawl', 'spider', 'slurp', 'mediapartners', 'bingpreview',
        'facebookexternalhit', 'facebot', 'embedly', 'quora link preview',
        'whatsapp', 'telegrambot', 'discordbot', 'slackbot', 'twitterbot',
        'linkedinbot', 'pinterest', 'redditbot', 'applebot', 'petalbot',
        'ahrefs', 'semrush', 'mj12bot', 'dotbot', 'yandex', 'baiduspider',
        'duckduckbot', 'sogou', 'exabot', 'ia_archiver', 'archive.org_bot',
        'headlesschrome', 'phantomjs', 'python-requests', 'curl/', 'wget/',
        'go-http-client', 'okhttp', 'java/', 'libwww', 'httpclient', 'scrapy',
        'monitoring', 'uptimerobot', 'pingdom', 'statuscake', 'censys', 'masscan',
        // fetchers do Google SEM a palavra "bot" no UA
        'google-read-aloud', 'googleother', 'google favicon', 'feedfetcher',
        'apis-google', 'google-site-verification', 'chrome-lighthouse', 'lighthouse',
        // crawlers de IA
        'gptbot', 'oai-searchbot', 'chatgpt-user', 'claudebot', 'claude-web',
        'anthropic-ai', 'ccbot', 'bytespider', 'amazonbot', 'perplexitybot',
        'google-extended', 'cohere-ai', 'diffbot', 'imagesiftbot',
        // SEO / scanners / monitores
        'dataforseo', 'serpstatbot', 'mojeekbot', 'seekport', 'zoominfobot',
        'site24x7', 'newrelicpinger', 'zgrab', 'l9explore', 'leakix', 'expanse',
        'internet-measurement', 'paloaltonetworks', 'netsystemsresearch', 'nuclei',
    ];

    /** Faixas (CIDR IPv4) de crawlers verificados. */
    private const CRAWLER_CIDRS = [
        '66.249.64.0/19',   // Googlebot
        '34.100.182.96/28', // Googlebot (GCP)
        '203.208.60.0/24',  // Googlebot (APAC)
        '157.55.0.0/16',    // Bingbot / Microsoft
        '207.46.0.0/16',    // Bingbot / Microsoft
        '40.77.167.0/24',   // Bingbot
        '199.16.156.0/22',  // Twitterbot
        '199.59.148.0/22',  // Twitterbot
    ];

    /** Prefixos IPv6 de crawler (comparação por prefixo, em minúsculo). */
    private const CRAWLER_V6_PREFIXES = [
        '2001:4860:4801:',  // Googlebot IPv6
    ];

    /**
     * @return array{is_bot: bool, device: string, browser: string, os: string}
     */
    public function parse(?string $ua, ?string $ip = null): array
    {
        $lower = strtolower((string) $ua);
        $isBot = $this->isBotRequest($ua, $ip);

        return [
            'is_bot' => $isBot,
            'device' => $isBot ? 'bot' : $this->device($lower),
            'browser' => $this->browser($lower),
            'os' => $this->os($lower),
        ];
    }

    /** Bot por user-agent OU por faixa de IP de crawler. */
    public function isBotRequest(?string $ua, ?string $ip = null): bool
    {
        return $this->isBot(strtolower((string) $ua)) || $this->ipIsCrawler($ip);
    }

    /** $lower já em minúsculo. user-agent vazio → bot. */
    public function isBot(string $lower): bool
    {
        if (trim($lower) === '') {
            return true;
        }
        foreach (self::BOT_TOKENS as $token) {
            if (str_contains($lower, $token)) {
                return true;
            }
        }

        return false;
    }

    /** O IP pertence a uma faixa conhecida de crawler? */
    public function ipIsCrawler(?string $ip): bool
    {
        $ip = trim((string) $ip);
        if ($ip === '') {
            return false;
        }

        // IPv6: comparação por prefixo conhecido.
        if (str_contains($ip, ':')) {
            $low = strtolower($ip);
            foreach (self::CRAWLER_V6_PREFIXES as $prefix) {
                if (str_starts_with($low, $prefix)) {
                    return true;
                }
            }

            return false;
        }

        // IPv4: máscara em 32 bits (& 0xFFFFFFFF evita sinal em 64-bit).
        $ipLong = ip2long($ip);
        if ($ipLong === false) {
            return false;
        }
        $ipLong &= 0xFFFFFFFF;
        foreach (self::CRAWLER_CIDRS as $cidr) {
            [$subnet, $bits] = explode('/', $cidr);
            $subnetLong = ip2long($subnet);
            if ($subnetLong === false) {
                continue;
            }
            $mask = (~((1 << (32 - (int) $bits)) - 1)) & 0xFFFFFFFF;
            if (($ipLong & $mask) === (($subnetLong & 0xFFFFFFFF) & $mask)) {
                return true;
            }
        }

        return false;
    }

    private function device(string $lower): string
    {
        if (str_contains($lower, 'ipad') || (str_contains($lower, 'tablet') && ! str_contains($lower, 'mobile'))) {
            return 'tablet';
        }
        if (str_contains($lower, 'mobi') || str_contains($lower, 'iphone') || str_contains($lower, 'android')) {
            return 'mobile';
        }

        return 'desktop';
    }

    private function browser(string $lower): string
    {
        // Ordem importa: Edge/Opera/Chrome trazem "safari"/"chrome" no UA.
        return match (true) {
            str_contains($lower, 'edg/') => 'Edge',
            str_contains($lower, 'opr/') || str_contains($lower, 'opera') => 'Opera',
            str_contains($lower, 'firefox') || str_contains($lower, 'fxios') => 'Firefox',
            str_contains($lower, 'chrome') || str_contains($lower, 'crios') => 'Chrome',
            str_contains($lower, 'safari') => 'Safari',
            default => 'Outro',
        };
    }

    private function os(string $lower): string
    {
        return match (true) {
            str_contains($lower, 'windows') => 'Windows',
            str_contains($lower, 'iphone') || str_contains($lower, 'ipad') => 'iOS',
            str_contains($lower, 'mac os') || str_contains($lower, 'macintosh') => 'macOS',
            str_contains($lower, 'android') => 'Android',
            str_contains($lower, 'linux') => 'Linux',
            default => 'Outro',
        };
    }
}
