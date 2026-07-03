<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Retrato diário das estatísticas do ai-memory → histórico durável em MySQL
// (sobrevive a um reset do ai-memory). Ver app/Console/Commands/SnapshotAiMemoryStats.
// Requer o cron do Laravel ativo no servidor: `* * * * * php artisan schedule:run`.
Schedule::command('aimemory:snapshot')->dailyAt('03:10')->withoutOverlapping();
