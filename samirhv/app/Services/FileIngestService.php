<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Recebe um arquivo (upload via form OU caminho absoluto via CLI), grava no
 * disco `downloads` sob a pasta do projeto, calcula tamanho + sha256 e cria/
 * atualiza a linha `ProjectFile`. Reupload do mesmo nome ATUALIZA (e restaura
 * da lixeira) em vez de duplicar — preservando o contador de downloads.
 */
class FileIngestService
{
    public const DISK = 'downloads';

    /**
     * @param  UploadedFile|string  $file  Upload do form ou caminho absoluto no servidor.
     * @param  array{label?: string, version?: ?string}  $opts
     */
    public function ingest(UploadedFile|string $file, Project $project, array $opts = []): ProjectFile
    {
        if ($file instanceof UploadedFile) {
            $sourcePath = $file->getRealPath();
            $originalName = $file->getClientOriginalName();
            $fileForStore = $file;
        } else {
            if (! is_file($file)) {
                throw new \RuntimeException("Arquivo não encontrado: {$file}");
            }
            $sourcePath = $file;
            $originalName = basename($file);
            $fileForStore = new File($file);
        }

        $safeName = $this->sanitizeName($originalName);
        $stored = $project->id.'/'.$safeName;          // caminho relativo no disco

        $size = (int) (@filesize($sourcePath) ?: 0);
        $sha256 = @hash_file('sha256', $sourcePath) ?: null;

        // Grava sobrescrevendo se já existir (mesmo identity → update da linha).
        Storage::disk(self::DISK)->putFileAs((string) $project->id, $fileForStore, $safeName);

        $payload = [
            'label' => $opts['label'] ?? pathinfo($originalName, PATHINFO_FILENAME),
            'original_name' => $originalName,
            'version' => $opts['version'] ?? null,
            'size' => $size,
            'sha256' => $sha256,
            'is_available' => true,
        ];

        $existing = ProjectFile::withTrashed()
            ->where('project_id', $project->id)
            ->where('filename', $stored)
            ->first();

        if ($existing) {
            if ($existing->trashed()) {
                $existing->restore();
            }
            $existing->update($payload);

            return $existing;
        }

        return $project->files()->create($payload + ['filename' => $stored]);
    }

    /** Remove caracteres perigosos do nome, preservando extensão e legibilidade. */
    private function sanitizeName(string $name): string
    {
        $name = basename($name);                       // sem componentes de caminho
        $name = preg_replace('/\s+/', '-', trim($name));
        $name = preg_replace('/[^A-Za-z0-9._-]/', '', $name);
        $name = ltrim($name, '.');                     // evita arquivos ocultos / "."

        return $name !== '' ? $name : 'arquivo-'.substr(md5($name.microtime(false)), 0, 8);
    }
}
