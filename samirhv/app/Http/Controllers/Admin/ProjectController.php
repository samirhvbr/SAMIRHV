<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function __construct(private readonly AuditLogger $audit) {}

    public function index(): View
    {
        $projects = Project::withCount('files')
            ->withSum('files as downloads_total', 'downloads_count')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.projects.index', compact('projects'));
    }

    public function create(): View
    {
        return view('admin.projects.create', ['project' => new Project]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $project = Project::create($data);

        $this->audit->record('project.create', $project->id, "Projeto criado: {$project->title}");

        return redirect()
            ->route('admin.projects.files.index', $project)
            ->with('status', 'Projeto criado. Agora envie os arquivos para download.');
    }

    public function edit(Project $project): View
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $data = $this->validateData($request, $project);

        $wasPublished = $project->is_published;
        $project->update($data);

        if ($wasPublished !== $project->is_published) {
            $event = $project->is_published ? 'project.publish' : 'project.unpublish';
            $this->audit->record($event, $project->id, "Projeto: {$project->title}");
        } else {
            $this->audit->record('project.update', $project->id, "Projeto atualizado: {$project->title}");
        }

        return redirect()->route('admin.projects.index')->with('status', 'Projeto atualizado.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $title = $project->title;
        $project->delete();

        $this->audit->record('project.delete', $project->id, "Projeto removido: {$title}");

        return redirect()->route('admin.projects.index')->with('status', 'Projeto removido.');
    }

    private function validateData(Request $request, ?Project $project = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable', 'string', 'max:255', 'alpha_dash',
                'unique:projects,slug'.($project ? ','.$project->id : ''),
            ],
            'description' => ['nullable', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'max:60'],
            'icon' => ['nullable', 'string', 'max:60'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $this->uniqueSlug($data['slug'] ?? Str::slug($data['title']), $project);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_published'] = $request->boolean('is_published');

        return $data;
    }

    /** Garante um slug único (acrescenta -2, -3… se já existir). */
    private function uniqueSlug(string $slug, ?Project $project): string
    {
        $base = Str::slug($slug) ?: 'projeto';
        $slug = $base;
        $i = 2;

        while (
            Project::withTrashed()
                ->where('slug', $slug)
                ->when($project, fn ($q) => $q->whereKeyNot($project->id))
                ->exists()
        ) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
