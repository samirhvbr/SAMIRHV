<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProjectFile;
use App\Support\OsDetector;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Monta os dados da página /p/{slug} agrupados por SO: abas por SO com
 * contadores, grupos por versão (mais recente primeiro, demais recolhíveis),
 * disponibilidade e o arquivo "recomendado para você" (detectado pelo
 * User-Agent, null-safe). Mantém o controller fino.
 */
class DownloadPresenter
{
    public function for(Project $project, Request $request): array
    {
        /** @var Collection<int,ProjectFile> $files */
        $files = $project->availableFiles;

        // SO null → linux (não perder arquivo; D3: desconhecido → linux).
        $byOs = $files->groupBy(fn (ProjectFile $f) => in_array($f->os, OsDetector::OSES, true) ? $f->os : 'linux');

        $tabs = [];
        foreach (OsDetector::OSES as $os) {
            $osFiles = $byOs->get($os, collect());
            $tabs[$os] = [
                'os' => $os,
                'label' => OsDetector::label($os),
                'count' => $osFiles->count(),
                'versions' => $this->groupByVersion($osFiles),
            ];
        }

        $available = array_values(array_filter(OsDetector::OSES, fn ($os) => $tabs[$os]['count'] > 0));
        $counts = [];
        foreach (OsDetector::OSES as $os) {
            $counts[$os] = $tabs[$os]['count'];
        }

        $detected = OsDetector::detect($request->userAgent());
        $recommended = $this->recommend($byOs, $tabs, $detected);

        return [
            'detected' => $detected,
            'available' => $available,
            'counts' => $counts,
            'default_os' => $recommended['os'] ?? ($available[0] ?? 'linux'),
            'recommended' => $recommended,
            'tabs' => $tabs,
            'has_any' => $files->isNotEmpty(),
        ];
    }

    /**
     * Agrupa por versão: mais recente primeiro, marca a latest.
     *
     * @return array<int,array{version: ?string, date: mixed, files: array<int,ProjectFile>, is_latest: bool}>
     */
    private function groupByVersion(Collection $osFiles): array
    {
        return $osFiles
            ->groupBy(fn (ProjectFile $f) => (string) ($f->version ?? ''))
            ->map(fn (Collection $vfiles) => [
                'version' => $vfiles->first()->version,
                'date' => $vfiles->max(fn (ProjectFile $f) => $f->effective_date),
                'files' => $vfiles->sortBy(fn (ProjectFile $f) => $f->arch ?? 'zzz')->values()->all(),
            ])
            ->sortByDesc('date')
            ->values()
            ->map(fn (array $group, int $i) => $group + ['is_latest' => $i === 0])
            ->all();
    }

    /**
     * Recomendado: (os detectado + arch) → x64 → qualquer arch → 1º SO com build.
     *
     * @return array{file: ?ProjectFile, os: string, detected_os: string, detected_arch: string, fallback_note: ?string}
     */
    private function recommend(Collection $byOs, array $tabs, array $detected): array
    {
        $pick = function (string $os, ?string $arch) use ($byOs): ?ProjectFile {
            $pool = $byOs->get($os, collect());
            if ($arch) {
                $pool = $pool->where('arch', $arch);
            }

            return $pool->sortByDesc(fn (ProjectFile $f) => $f->effective_date)->first();
        };

        $os = $detected['os'];
        $note = null;
        $file = null;

        if (($tabs[$os]['count'] ?? 0) > 0) {
            $file = $pick($os, $detected['arch']) ?? $pick($os, 'x64') ?? $pick($os, null);
        } else {
            foreach (OsDetector::OSES as $candidate) {
                if (($tabs[$candidate]['count'] ?? 0) > 0) {
                    $os = $candidate;
                    $file = $pick($candidate, 'x64') ?? $pick($candidate, null);
                    $note = 'Ainda não há build para '.OsDetector::label($detected['os'])
                        .' — mostrando '.OsDetector::label($candidate).'.';
                    break;
                }
            }
        }

        return [
            'file' => $file,
            'os' => $file ? ($file->os ?: $os) : $os,
            'detected_os' => $detected['os'],
            'detected_arch' => $detected['arch'],
            'fallback_note' => $note,
        ];
    }
}
