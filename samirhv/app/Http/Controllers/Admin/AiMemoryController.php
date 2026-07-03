<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiMemoryStatSnapshot;
use App\Services\AiMemory\AiMemoryDatabase;
use App\Services\AiMemory\HandoffRepository;
use App\Services\AiMemory\ObservationRepository;
use App\Services\AiMemory\PageRepository;
use App\Services\AiMemory\ProjectRepository;
use App\Services\AiMemory\SearchRepository;
use App\Services\AiMemory\SessionRepository;
use App\Services\AiMemory\StatsRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Módulo AI-MEMORY do admin — SOMENTE LEITURA sobre o SQLite do ai-memory
 * (ver App\Services\AiMemory\AiMemoryDatabase e docs/AI-MEMORY.md).
 *
 * Controller fino: cada método injeta o(s) repositório(s) da sua tela e devolve
 * a view. `screen()` centraliza o guard de disponibilidade — se o banco não
 * estiver acessível, a view recebe `available = false` e mostra a explicação,
 * sem nunca disparar uma query (que falharia).
 */
class AiMemoryController extends Controller
{
    public function __construct(private readonly AiMemoryDatabase $db) {}

    public function dashboard(StatsRepository $stats): View
    {
        $days = (int) config('aimemory.chart_days', 30);

        return $this->screen('admin.ai-memory.dashboard', fn () => [
            'counts' => $stats->counts(),
            'observationsByDay' => $stats->observationsByDay($days),
            'sessionsByDay' => $stats->sessionsByDay($days),
            // Evolução de longo prazo vem da tabela DURÁVEL (sobrevive a reset).
            'history' => AiMemoryStatSnapshot::orderBy('captured_on')->get(),
        ]);
    }

    public function projects(ProjectRepository $projects): View
    {
        return $this->screen('admin.ai-memory.projects', fn () => [
            'projects' => $projects->all(),
        ]);
    }

    public function projectShow(string $hexId, ProjectRepository $projects, PageRepository $pages, SessionRepository $sessions): View
    {
        return $this->screen('admin.ai-memory.project', function () use ($hexId, $projects, $pages, $sessions) {
            $project = $projects->find($hexId);
            abort_if($project === null, 404);

            return [
                'project' => $project,
                'recentPages' => $pages->paginate($hexId, 10)->items(),
                'recentSessions' => $sessions->paginate($hexId, 10)->items(),
            ];
        });
    }

    public function pages(Request $request, PageRepository $pages, ProjectRepository $projects): View
    {
        $project = $request->string('project')->toString() ?: null;

        return $this->screen('admin.ai-memory.pages', fn () => [
            'pages' => $pages->paginate($project, $this->perPage())->withQueryString(),
            'projectOptions' => $projects->options(),
            'project' => $project,
        ]);
    }

    public function pageShow(string $hexId, PageRepository $pages): View
    {
        return $this->screen('admin.ai-memory.page', function () use ($hexId, $pages) {
            $page = $pages->find($hexId);
            abort_if($page === null, 404);

            return ['page' => $page, 'history' => $pages->history($page)];
        });
    }

    public function sessions(Request $request, SessionRepository $sessions, ProjectRepository $projects): View
    {
        $project = $request->string('project')->toString() ?: null;

        return $this->screen('admin.ai-memory.sessions', fn () => [
            'sessions' => $sessions->paginate($project, $this->perPage())->withQueryString(),
            'projectOptions' => $projects->options(),
            'project' => $project,
        ]);
    }

    public function sessionShow(string $hexId, SessionRepository $sessions): View
    {
        return $this->screen('admin.ai-memory.session', function () use ($hexId, $sessions) {
            $session = $sessions->find($hexId);
            abort_if($session === null, 404);

            return ['session' => $session, 'observations' => $sessions->observations($hexId)];
        });
    }

    public function observations(Request $request, ObservationRepository $observations, ProjectRepository $projects): View
    {
        $filters = $request->validate([
            'kind' => ['nullable', 'string', 'max:80'],
            'importance' => ['nullable', 'integer', 'between:1,10'],
            'project' => ['nullable', 'string', 'max:64'],
            'days' => ['nullable', 'integer', 'in:1,7,30,90'],
        ]);

        return $this->screen('admin.ai-memory.observations', fn () => [
            'observations' => $observations->paginate($filters, $this->perPage())->withQueryString(),
            'kinds' => $observations->kinds(),
            'projectOptions' => $projects->options(),
            'filters' => $filters,
        ]);
    }

    public function observationShow(string $hexId, ObservationRepository $observations): View
    {
        return $this->screen('admin.ai-memory.observation', function () use ($hexId, $observations) {
            $observation = $observations->find($hexId);
            abort_if($observation === null, 404);

            return ['observation' => $observation];
        });
    }

    public function handoffs(Request $request, HandoffRepository $handoffs): View
    {
        $state = $request->validate([
            'state' => ['nullable', 'string', 'in:open,accepted,expired'],
        ])['state'] ?? null;

        return $this->screen('admin.ai-memory.handoffs', fn () => [
            'handoffs' => $handoffs->paginate($state, $this->perPage())->withQueryString(),
            'state' => $state,
        ]);
    }

    public function handoffShow(string $hexId, HandoffRepository $handoffs): View
    {
        return $this->screen('admin.ai-memory.handoff', function () use ($hexId, $handoffs) {
            $handoff = $handoffs->find($hexId);
            abort_if($handoff === null, 404);

            return ['handoff' => $handoff];
        });
    }

    public function search(Request $request, SearchRepository $search): View
    {
        $q = trim($request->string('q')->toString());

        return $this->screen('admin.ai-memory.search', fn () => [
            'q' => $q,
            'results' => $q !== '' ? $search->search($q) : [],
        ]);
    }

    /** Teto de linhas por página (config). */
    private function perPage(): int
    {
        return (int) config('aimemory.per_page', 50);
    }

    /**
     * Renderiza uma tela do módulo com o guard de disponibilidade. Quando o
     * ai-memory não está acessível, devolve `available = false` (a view mostra
     * o aviso explicativo) sem executar nenhuma query.
     */
    private function screen(string $view, callable $data): View
    {
        $base = [
            'available' => $this->db->isAvailable(),
            'aimemoryPath' => $this->db->path(),
            'dockerVolume' => $this->db->dockerVolume(),
        ];

        if (! $base['available']) {
            return view($view, $base);
        }

        return view($view, array_merge($base, $data()));
    }
}
