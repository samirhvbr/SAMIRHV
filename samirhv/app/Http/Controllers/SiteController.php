<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\View\View;

/**
 * Site público: vitrine de projetos disponibilizados para download.
 */
class SiteController extends Controller
{
    public function home(): View
    {
        $projects = Project::published()
            ->withCount('files')
            ->withSum('files as downloads_total', 'downloads_count')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('home', [
            'featured' => $projects->first(),
            'projects' => $projects->take(6),
            'categories' => $this->categories($projects),
        ]);
    }

    public function downloads(): View
    {
        $projects = Project::published()
            ->with(['availableFiles' => fn ($q) => $q->orderBy('label')])
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('downloads.index', [
            'projects' => $projects,
            'totalFiles' => $projects->sum(fn ($p) => $p->availableFiles->count()),
        ]);
    }

    public function show(Project $project): View
    {
        abort_unless($project->is_published, 404);

        $project->load(['availableFiles' => fn ($q) => $q->orderBy('label')]);

        return view('projects.show', [
            'project' => $project,
        ]);
    }

    /** Categorias distintas presentes nos projetos publicados (para os chips/filtros). */
    private function categories($projects): array
    {
        return $projects
            ->pluck('category')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }
}
