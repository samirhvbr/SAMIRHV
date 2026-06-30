<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\DownloadPresenter;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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
            // Destaque (card "releases.txt") só faz sentido com projeto de download.
            'featured' => $projects->first(fn ($p) => ! $p->isLink()),
            'projects' => $projects->take(6),
            'categories' => $this->categories($projects),
        ]);
    }

    public function downloads(): View
    {
        $projects = Project::published()
            ->whereNull('external_url')   // projetos-link não têm o que baixar
            ->with(['availableFiles' => fn ($q) => $q->orderBy('label')])
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        return view('downloads.index', [
            'projects' => $projects,
            'totalFiles' => $projects->sum(fn ($p) => $p->availableFiles->count()),
        ]);
    }

    public function show(Project $project, Request $request, DownloadPresenter $presenter): View|RedirectResponse
    {
        abort_unless($project->is_published, 404);

        // Projeto-link: manda direto pro site externo.
        if ($project->isLink()) {
            return redirect()->away($project->external_url);
        }

        $project->load(['availableFiles' => fn ($q) => $q->orderBy('label')]);

        return view('projects.show', [
            'project' => $project,
            'download' => $presenter->for($project, $request),
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
