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
            // Destaque (card "releases.txt") só faz sentido com projeto que tem arquivos
            // (download ou híbrido); projetos-link puros não entram.
            'featured' => $projects->first(fn ($p) => $p->files_count > 0),
            'projects' => $projects->take(6),
            'categories' => $this->categories($projects),
        ]);
    }

    public function downloads(): View
    {
        $projects = Project::published()
            // Projetos de download e híbridos entram; projeto-link puro (com site
            // externo e sem arquivos) fica de fora — não há o que baixar.
            ->where(fn ($q) => $q->whereNull('external_url')->orWhereHas('availableFiles'))
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

        $project->load(['availableFiles' => fn ($q) => $q->orderBy('label')]);

        // Projeto-link puro (sem arquivos): manda direto pro site externo. Híbrido
        // (site + arquivos) renderiza a página, com o botão "usar online" + downloads.
        if ($project->isLinkOnly()) {
            return redirect()->away($project->external_url);
        }

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
