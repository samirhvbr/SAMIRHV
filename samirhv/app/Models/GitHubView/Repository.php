<?php

namespace App\Models\GitHubView;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Repositório do GitHub monitorado (GitHub View). Porte de
 * app/models/repository.rb do github-visualize. A validação de formato de
 * owner/name (NAME_FORMAT) roda na borda (FormRequest), não no model.
 * Ver .continue/migracao-github-visualize.md (§4).
 */
class Repository extends Model
{
    protected $table = 'github_repositories';

    /** Igual ao NAME_FORMAT do Rails: sem '.'/'..' isolado; só \w . - (usar no FormRequest). */
    public const NAME_FORMAT = '/\A(?!\.{1,2}\z)[\w.-]+\z/';

    public const SYNC_STATUSES = ['pending', 'syncing', 'synced', 'failed'];

    protected $fillable = [
        'owner', 'name', 'description', 'default_branch',
        'last_synced_at', 'sync_status', 'sync_error', 'sync_progress',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];

    public function commits(): HasMany
    {
        return $this->hasMany(Commit::class);
    }

    public function workflowRuns(): HasMany
    {
        return $this->hasMany(WorkflowRun::class);
    }

    /** Owner default p/ nomes "curtos" no add-form (env GITHUB_OWNER). */
    public static function defaultOwner(): ?string
    {
        return config('services.github.owner') ?: null;
    }

    public function fullName(): string
    {
        return "{$this->owner}/{$this->name}";
    }

    public function githubUrl(): string
    {
        return 'https://github.com/'.$this->fullName();
    }

    public function isSyncing(): bool
    {
        return $this->sync_status === 'syncing';
    }

    public function startSync(): void
    {
        $this->update([
            'sync_status' => 'syncing',
            'sync_error' => null,
            'sync_progress' => 'starting',
        ]);
    }

    public function finishSync(): void
    {
        $this->update([
            'sync_status' => 'synced',
            'sync_error' => null,
            'sync_progress' => null,
            'last_synced_at' => now(),
        ]);
    }

    public function failSync(string $error): void
    {
        $this->update([
            'sync_status' => 'failed',
            'sync_error' => Str::limit($error, 500, ''),
            'sync_progress' => null,
        ]);
    }
}
