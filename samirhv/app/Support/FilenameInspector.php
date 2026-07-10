<?php

namespace App\Support;

/**
 * Deduz (os, arch, file_type) a partir do NOME do arquivo (use `original_name`).
 * Usado pelo backfill (`downloads:backfill-os`) e pelo upload do Admin para
 * pré-preencher os campos — sempre permitindo override manual.
 *
 * Heurística por extensão + tokens comuns no nome (amd64/x86_64 → x64,
 * arm64/aarch64 → arm64). Retorna null no que não der para inferir.
 */
final class FilenameInspector
{
    /**
     * @return array{os: ?string, arch: ?string, file_type: ?string, version: ?string, name: ?string}
     */
    public static function inspect(string $name): array
    {
        $n = strtolower($name);
        $ext = pathinfo($n, PATHINFO_EXTENSION);

        $fileType = match (true) {
            $ext === 'deb' => 'deb',
            $ext === 'rpm' => 'rpm',
            str_contains($n, '.appimage') => 'appimage',
            $ext === 'exe' => 'exe',
            $ext === 'msi' => 'msi',
            $ext === 'dmg' => 'dmg',
            $ext === 'pkg' => 'pkg',
            $ext === 'zip' => 'zip',
            default => $ext !== '' ? $ext : null,
        };

        $os = match (true) {
            in_array($fileType, ['deb', 'rpm', 'appimage'], true) || str_contains($n, 'linux') => 'linux',
            in_array($fileType, ['exe', 'msi'], true) || str_contains($n, 'win') => 'windows',
            in_array($fileType, ['dmg', 'pkg'], true) || str_contains($n, 'mac') || str_contains($n, 'darwin') => 'macos',
            default => null,
        };

        $arch = match (true) {
            str_contains($n, 'arm64') || str_contains($n, 'aarch64') => 'arm64',
            str_contains($n, 'amd64') || str_contains($n, 'x86_64') || str_contains($n, 'x64') => 'x64',
            str_contains($n, 'universal') => 'universal',
            default => null,
        };

        // Versão: primeiro X.Y(.Z) no nome (o ponto exige, então não casa "x86_64").
        $version = preg_match('/\d+\.\d+(?:\.\d+)?/', $name, $m) ? $m[0] : null;

        // Nome do produto: trecho antes da versão; separadores viram espaço.
        $base = pathinfo($name, PATHINFO_FILENAME);
        $before = ($version !== null && ($pos = strpos($base, $version)) !== false)
            ? substr($base, 0, $pos)
            : ($version !== null ? '' : $base);
        $label = trim((string) preg_replace('/[_\-.]+/', ' ', $before));
        $label = $label !== '' ? $label : null;

        return ['os' => $os, 'arch' => $arch, 'file_type' => $fileType, 'version' => $version, 'name' => $label];
    }
}
