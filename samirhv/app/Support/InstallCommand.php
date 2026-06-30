<?php

namespace App\Support;

use App\Models\ProjectFile;

/**
 * Comando de instalação + verificação de integridade por (os, file_type),
 * derivado do nome do arquivo. `install` = null significa instalação manual /
 * pelo assistente (ex.: .exe/.msi/.dmg). `verify` está sempre presente.
 */
final class InstallCommand
{
    /**
     * @return array{install: ?string, verify: string}
     */
    public static function for(ProjectFile $file): array
    {
        $name = $file->original_name ?: basename((string) $file->filename);

        $install = match ($file->file_type) {
            'deb' => 'sudo apt install ./'.$name,
            'rpm' => 'sudo dnf install ./'.$name,
            'appimage' => 'chmod +x '.$name.' && ./'.$name,
            default => null,   // exe/msi/dmg/pkg/zip → instalação manual
        };

        $verify = match ($file->os) {
            'windows' => 'Get-FileHash .\\'.$name.' -Algorithm SHA256',
            'macos' => 'shasum -a 256 '.$name,
            default => 'sha256sum '.$name,
        };

        return ['install' => $install, 'verify' => $verify];
    }
}
