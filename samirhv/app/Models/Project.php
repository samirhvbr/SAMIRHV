<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'description', 'category', 'icon', 'external_url',
        'is_published', 'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Tem um site externo associado (projeto-link ou híbrido). */
    public function isLink(): bool
    {
        return filled($this->external_url);
    }

    /** Tem ao menos um arquivo disponível para download. Prefere dados já carregados (evita N+1). */
    public function hasFiles(): bool
    {
        if ($this->relationLoaded('availableFiles')) {
            return $this->availableFiles->isNotEmpty();
        }

        if (array_key_exists('files_count', $this->attributes)) {
            return (int) $this->files_count > 0;
        }

        return $this->availableFiles()->exists();
    }

    /** Projeto-link puro: só aponta pro site externo, sem arquivos próprios (→ redireciona). */
    public function isLinkOnly(): bool
    {
        return $this->isLink() && ! $this->hasFiles();
    }

    /** Híbrido: tem site externo E arquivos para download (ex: ShvIA — web + app desktop). */
    public function isHybrid(): bool
    {
        return $this->isLink() && $this->hasFiles();
    }

    /** URL pública do projeto: o site externo (se link puro) ou a página /p/{slug}. */
    public function getPublicUrlAttribute(): string
    {
        return $this->isLinkOnly() ? $this->external_url : route('project.show', $this);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class);
    }

    /** Arquivos visíveis ao público (disponíveis e com espelho no disco). */
    public function availableFiles(): HasMany
    {
        return $this->files()->where('is_available', true);
    }

    /** Soma de downloads de todos os arquivos do projeto. */
    public function getDownloadsCountAttribute(): int
    {
        return (int) $this->files()->sum('downloads_count');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
