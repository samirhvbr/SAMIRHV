<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Retrato diário e durável das estatísticas do ai-memory (ver a migration
 * e docs/AI-MEMORY.md). Sobrevive a um reset do ai-memory: é o histórico de
 * longo prazo usado no gráfico de evolução do Dashboard.
 */
class AiMemoryStatSnapshot extends Model
{
    protected $fillable = [
        'captured_on',
        'workspaces', 'projects', 'pages', 'sessions', 'observations',
        'embeddings', 'handoffs_open', 'proposals_pending',
        'raw_json',
    ];

    protected $casts = [
        'captured_on' => 'date',
        'raw_json' => 'array',
    ];
}
