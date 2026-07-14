<?php

namespace App\Models\GitHubView;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * Commit de um repositório monitorado. Porte de app/models/commit.rb.
 */
class Commit extends Model
{
    protected $table = 'github_commits';

    protected $fillable = [
        'repository_id', 'sha', 'message', 'author_login',
        'committed_at', 'additions', 'deletions',
    ];

    protected $casts = [
        'committed_at' => 'datetime',
        'additions' => 'integer',
        'deletions' => 'integer',
    ];

    public function repository(): BelongsTo
    {
        return $this->belongsTo(Repository::class);
    }

    /** @param  Builder<Commit>  $query */
    public function scopeChronological(Builder $query): Builder
    {
        return $query->orderBy('committed_at');
    }

    public function summary(): string
    {
        return Str::limit(trim((string) $this->message), 100, '');
    }
}
