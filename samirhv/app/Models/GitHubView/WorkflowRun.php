<?php

namespace App\Models\GitHubView;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Run de GitHub Actions de um repositório. Porte de app/models/workflow_run.rb.
 */
class WorkflowRun extends Model
{
    protected $table = 'github_workflow_runs';

    protected $fillable = [
        'repository_id', 'github_id', 'workflow_name', 'run_number',
        'status', 'conclusion', 'branch', 'run_started_at',
    ];

    protected $casts = [
        'github_id' => 'integer',
        'run_number' => 'integer',
        'run_started_at' => 'datetime',
    ];

    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }

    /** @param  Builder<WorkflowRun>  $query */
    public function scopeChronological(Builder $query): Builder
    {
        return $query->orderBy('run_started_at');
    }

    public function green(): bool
    {
        return $this->conclusion === 'success';
    }

    public function red(): bool
    {
        return in_array($this->conclusion, ['failure', 'timed_out', 'startup_failure'], true);
    }
}
