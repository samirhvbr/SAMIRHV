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

    /** Projeto-link: aponta pra um site externo, sem arquivos próprios. */
    public function isLink(): bool
    {
        return filled($this->external_url);
    }

    /** URL pública do projeto: o site externo (se link) ou a página /p/{slug}. */
    public function getPublicUrlAttribute(): string
    {
        return $this->external_url ?: route('project.show', $this);
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
