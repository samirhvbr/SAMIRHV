<?php

namespace App\Services\AiMemory;

use Illuminate\Support\Carbon;

/**
 * Formatação dos timestamps do ai-memory para as views. O ai-memory grava
 * tempo como INTEGER = microssegundos desde a epoch (UTC); estes helpers
 * convertem para o fuso de exibição (config `aimemory.timezone`) e formatam.
 * Estático de propósito, para ser chamado direto do Blade.
 */
class AiMemoryTime
{
    public static function toCarbon(int|float|null $micros): ?Carbon
    {
        if (! $micros || $micros <= 0) {
            return null;
        }

        return Carbon::createFromTimestamp((int) ($micros / 1_000_000))
            ->timezone((string) config('aimemory.timezone', 'America/Sao_Paulo'));
    }

    /** Data/hora formatada, ou "—" se vazio. */
    public static function format(int|float|null $micros, string $format = 'd/m/Y H:i'): string
    {
        return self::toCarbon($micros)?->format($format) ?? '—';
    }

    /** "há 3 dias", ou "—". */
    public static function human(int|float|null $micros): string
    {
        return self::toCarbon($micros)?->diffForHumans() ?? '—';
    }

    /** Duração entre início e fim (sessão), tolerante a fim nulo. */
    public static function duration(int|float|null $start, int|float|null $end): string
    {
        $a = self::toCarbon($start);
        if ($a === null) {
            return '—';
        }
        $b = self::toCarbon($end);
        if ($b === null) {
            return 'em aberto';
        }

        $seconds = abs($b->getTimestamp() - $a->getTimestamp());
        $h = intdiv($seconds, 3600);
        $m = intdiv($seconds % 3600, 60);

        return $h > 0 ? "{$h}h {$m}min" : ($m > 0 ? "{$m}min" : "{$seconds}s");
    }
}
