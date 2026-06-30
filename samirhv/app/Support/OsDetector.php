<?php

namespace App\Support;

/**
 * Detecta o sistema operacional (e a arquitetura, best-effort) a partir do
 * User-Agent — para escolher a aba default e o arquivo recomendado na página
 * de download. Desconhecido → linux (decisão D3 do roteiro).
 *
 * Observação: a arquitetura por UA é fraca (Safari no Apple Silicon reporta
 * Intel); por isso o default é x64 e arm64 só quando o UA expõe aarch64/arm64.
 */
final class OsDetector
{
    /** Ordem canônica de exibição das abas. */
    public const OSES = ['linux', 'windows', 'macos'];

    public const LABELS = [
        'linux' => 'Linux',
        'windows' => 'Windows',
        'macos' => 'macOS',
    ];

    /**
     * @return array{os: string, arch: string}
     */
    public static function detect(?string $userAgent): array
    {
        $ua = strtolower((string) $userAgent);

        $os = match (true) {
            str_contains($ua, 'windows nt') || str_contains($ua, 'windows') => 'windows',
            str_contains($ua, 'mac os x') || str_contains($ua, 'macintosh') || str_contains($ua, 'darwin') => 'macos',
            str_contains($ua, 'linux') || str_contains($ua, 'x11') => 'linux',
            default => 'linux',
        };

        $arch = (str_contains($ua, 'arm64') || str_contains($ua, 'aarch64')) ? 'arm64' : 'x64';

        return ['os' => $os, 'arch' => $arch];
    }

    public static function label(?string $os): string
    {
        return self::LABELS[$os] ?? 'Outro';
    }
}
