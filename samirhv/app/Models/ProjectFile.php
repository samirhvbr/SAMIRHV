<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProjectFile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id', 'label', 'filename', 'original_name', 'version',
        'size', 'sha256', 'is_available', 'downloads_count',
        'os', 'arch', 'file_type', 'released_at',
    ];

    protected $casts = [
        'size' => 'integer',
        'downloads_count' => 'integer',
        'is_available' => 'boolean',
        'released_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class)->withTrashed();
    }

    public function downloadLogs(): HasMany
    {
        return $this->hasMany(DownloadLog::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /** O arquivo está realmente no disco de downloads? */
    public function getIsMirroredAttribute(): bool
    {
        return $this->filename && Storage::disk('downloads')->exists($this->filename);
    }

    /** Tamanho legível (B/KB/MB/GB). */
    public function getHumanSizeAttribute(): string
    {
        $bytes = (int) $this->size;
        if ($bytes <= 0) {
            return '—';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = (int) floor(log($bytes, 1024));
        $i = min($i, count($units) - 1);

        return round($bytes / (1024 ** $i), $i === 0 ? 0 : 1).' '.$units[$i];
    }

    /** Primeiros 16 hex do sha256, para exibição. */
    public function getShortHashAttribute(): ?string
    {
        return $this->sha256 ? substr($this->sha256, 0, 16) : null;
    }
}
