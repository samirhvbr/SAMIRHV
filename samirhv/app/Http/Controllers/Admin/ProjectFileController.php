<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Services\AuditLogger;
use App\Services\FileIngestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectFileController extends Controller
{
    public function __construct(
        private readonly AuditLogger $audit,
        private readonly FileIngestService $ingest,
    ) {}

    public function index(Project $project): View
    {
        $files = $project->files()->orderBy('label')->get();

        return view('admin.projects.files', compact('project', 'files'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:512000'],   // 500 MB
            'label' => ['nullable', 'string', 'max:255'],
            'version' => ['nullable', 'string', 'max:30'],
        ], [
            'file.max' => 'O arquivo excede o limite de 500 MB do upload via navegador. '
                .'Para arquivos maiores, use o comando files:add no servidor.',
        ]);

        $file = $this->ingest->ingest($request->file('file'), $project, [
            'label' => $request->input('label') ?: null,
            'version' => $request->input('version') ?: null,
        ]);

        $this->audit->record('file.upload', $file->id,
            "Arquivo enviado: {$file->label} ({$file->human_size}) — projeto {$project->title}");

        return redirect()->route('admin.projects.files.index', $project)
            ->with('status', "Arquivo \"{$file->label}\" enviado.");
    }

    public function toggleAvailable(Project $project, ProjectFile $file): RedirectResponse
    {
        abort_unless($file->project_id === $project->id, 404);

        $file->update(['is_available' => ! $file->is_available]);

        $event = $file->is_available ? 'file.available' : 'file.unavailable';
        $this->audit->record($event, $file->id, "Arquivo: {$file->label} — projeto {$project->title}");

        return back()->with('status', $file->is_available ? 'Arquivo disponibilizado.' : 'Arquivo ocultado.');
    }

    public function destroy(Project $project, ProjectFile $file): RedirectResponse
    {
        abort_unless($file->project_id === $project->id, 404);

        $label = $file->label;
        // Remove o binário do disco e a linha (soft delete preserva o histórico de logs).
        Storage::disk(FileIngestService::DISK)->delete($file->filename);
        $file->delete();

        $this->audit->record('file.delete', $file->id, "Arquivo removido: {$label} — projeto {$project->title}");

        return back()->with('status', "Arquivo \"{$label}\" removido.");
    }
}
