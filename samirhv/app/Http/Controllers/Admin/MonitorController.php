<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\GithubReleaseChecker;
use App\Support\SemVer;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Monitor de projetos: para cada projeto que é fork de um OSS (tem
 * `upstream_repo`), compara a NOSSA versão (maior semver entre os arquivos
 * disponíveis) com a última do upstream no GitHub, sinalizando divergência.
 *
 * Controller fino: a consulta ao GitHub vive em GithubReleaseChecker (com
 * cache), a comparação em SemVer. Aqui só orquestramos e montamos as linhas.
 */
class MonitorController extends Controller
{
    public function __construct(private readonly GithubReleaseChecker $github) {}

    public function index(Request $request): View
    {
        $projects = Project::with('availableFiles')
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();

        // "Verificar agora": fura o cache dos repos antes de recomputar.
        if ($request->boolean('refresh')) {
            $projects->filter->hasUpstream()
                ->each(fn (Project $p) => $this->github->refresh($p->upstream_repo));
        }

        $rows = $projects->map(fn (Project $p) => $this->buildRow($p));

        $tracked = $rows->where('tracked', true);
        $summary = [
            'tracked' => $tracked->count(),
            'outdated' => $tracked->where('status', 'outdated')->count(),
            'errors' => $tracked->where('status', 'error')->count(),
        ];

        return view('admin.monitor.index', compact('rows', 'summary'));
    }

    /** @return array<string,mixed> */
    private function buildRow(Project $project): array
    {
        $local = $project->localVersion();

        $row = [
            'project' => $project,
            'tracked' => $project->hasUpstream(),
            'local' => $local,
            'upstream' => null,
            'upstream_raw' => null,
            'upstream_url' => $project->upstream_url,
            'source' => null,
            'published_at' => null,
            'status' => 'untracked',
            'error' => null,
        ];

        if (! $project->hasUpstream()) {
            return $row;
        }

        $res = $this->github->latest($project->upstream_repo);

        if (! ($res['ok'] ?? false)) {
            return ['status' => 'error', 'error' => $res['error'] ?? 'desconhecido'] + $row;
        }

        $upstream = $res['version'];

        return [
            'upstream' => $upstream,
            'upstream_raw' => $res['raw'] ?? $upstream,
            'upstream_url' => $res['url'] ?? $project->upstream_url,
            'source' => $res['source'] ?? null,
            'published_at' => $res['published_at'] ?? null,
            'status' => $this->status($local, $upstream),
        ] + $row;
    }

    /**
     * Compara nossa versão com a do upstream.
     *  - no_local  : não temos versão local para comparar (mostra só a upstream)
     *  - unknown   : alguma das versões não é semver comparável
     *  - outdated  : upstream à frente (precisa atualizar) ← o alerta
     *  - up_to_date: iguais
     *  - ahead     : estamos à frente do upstream (incomum)
     */
    private function status(?string $local, ?string $upstream): string
    {
        if ($local === null || $local === '') {
            return 'no_local';
        }
        if (! SemVer::isParsable($local) || ! SemVer::isParsable($upstream)) {
            return 'unknown';
        }

        return match (SemVer::compare($local, $upstream) <=> 0) {
            -1 => 'outdated',
            0 => 'up_to_date',
            default => 'ahead',
        };
    }
}
