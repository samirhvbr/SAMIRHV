<?php

namespace App\Services\GitHub\Visualizations;

use App\Models\GitHubView\Repository;
use Illuminate\Support\Carbon;

/**
 * Heatmap dia × hora dos commits (porte de app/presenters/visualizations/
 * commit_heatmap.rb). Agrupa por [data local, hora] no fuso configurado e
 * devolve uma linha por dia, do 1º commit da janela até hoje.
 *
 * Saída: ['total' => int, 'max' => int, 'rows' => [['label' => 'Jul 5',
 *         'counts' => [24 contagens por hora]]]].
 */
class CommitHeatmap
{
    public const WINDOW_DAYS = 42;

    public function __construct(
        private readonly Repository $repository,
        private readonly int $windowDays = self::WINDOW_DAYS,
    ) {}

    /** @return array{total: int, max: int, rows: array<int, array{label: string, counts: array<int, int>}>} */
    public function toArray(): array
    {
        $tz = $this->timezone();

        // Commits da janela; committed_at é Carbon (cast) em UTC → convertemos p/ o fuso local.
        $times = $this->repository->commits()
            ->where('committed_at', '>=', Carbon::now($tz)->subDays($this->windowDays)->startOfDay()->utc())
            ->pluck('committed_at');

        // counts["Y-m-d"][hora] = n
        $counts = [];
        foreach ($times as $time) {
            $local = $time->copy()->setTimezone($tz);
            $counts[$local->format('Y-m-d')][(int) $local->format('G')] ??= 0;
            $counts[$local->format('Y-m-d')][(int) $local->format('G')]++;
        }

        $rows = [];
        $max = 0;

        if ($counts !== []) {
            $cursor = Carbon::parse(min(array_keys($counts)), $tz)->startOfDay();
            $today = Carbon::now($tz)->startOfDay();

            while ($cursor->lessThanOrEqualTo($today)) {
                $key = $cursor->format('Y-m-d');
                $hourly = [];
                for ($hour = 0; $hour < 24; $hour++) {
                    $count = $counts[$key][$hour] ?? 0;
                    $hourly[] = $count;
                    $max = max($max, $count);
                }
                $rows[] = ['label' => $cursor->format('M j'), 'counts' => $hourly];
                $cursor->addDay();
            }
        }

        return [
            'total' => $times->count(),
            'max' => $max,
            'rows' => $rows,
        ];
    }

    private function timezone(): string
    {
        return config('services.github.timezone') ?: config('app.timezone', 'UTC');
    }
}
