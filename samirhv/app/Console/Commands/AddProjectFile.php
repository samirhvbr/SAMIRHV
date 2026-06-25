<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\FileIngestService;
use Illuminate\Console\Command;

/**
 * Publica um arquivo grande direto pelo servidor, sem passar pelo limite de
 * upload do navegador/servidor web. Ex.:
 *   scp pacote.AppImage servidor:/tmp/
 *   php artisan files:add /tmp/pacote.AppImage --project=meu-projeto --version=1.0.0
 */
class AddProjectFile extends Command
{
    protected $signature = 'files:add
        {path : Caminho absoluto do arquivo no servidor}
        {--project= : Slug ou ID do projeto de destino}
        {--label= : Rótulo exibido (default: nome do arquivo)}
        {--version= : Versão do arquivo}';

    protected $description = 'Adiciona um arquivo a um projeto (bypassa o limite de upload via navegador)';

    public function handle(FileIngestService $ingest): int
    {
        $path = $this->argument('path');
        if (! is_file($path)) {
            $this->error("Arquivo não encontrado: {$path}");

            return self::FAILURE;
        }

        $key = $this->option('project');
        if (! $key) {
            $this->error('Informe --project=<slug|id>.');

            return self::FAILURE;
        }

        $project = is_numeric($key)
            ? Project::find($key)
            : Project::where('slug', $key)->first();

        if (! $project) {
            $this->error("Projeto não encontrado: {$key}");

            return self::FAILURE;
        }

        $file = $ingest->ingest($path, $project, [
            'label' => $this->option('label') ?: null,
            'version' => $this->option('version') ?: null,
        ]);

        $this->info("OK: \"{$file->label}\" ({$file->human_size}) adicionado ao projeto {$project->title}.");

        return self::SUCCESS;
    }
}
