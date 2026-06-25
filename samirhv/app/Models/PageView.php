<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageView extends Model
{
    protected $fillable = [
        'path', 'method', 'user_id', 'ip', 'user_agent',
        'is_bot', 'device', 'browser', 'os', 'referer', 'locale',
    ];

    protected $casts = [
        'is_bot' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
