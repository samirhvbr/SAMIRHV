<?php

namespace App\Models;

use App\Models\Concerns\DescribesClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Trilha de ações do admin (criar/editar/remover projetos e arquivos).
 * Escrita por App\Services\AuditLogger. Nunca guarda segredos.
 */
class ActivityLog extends Model
{
    use DescribesClient;

    protected $fillable = [
        'user_id', 'event', 'subject_type', 'subject_id',
        'ip_address', 'user_agent', 'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Rótulo amigável do evento. */
    public function getEventLabelAttribute(): string
    {
        return match ($this->event) {
            'project.create' => 'Projeto criado',
            'project.update' => 'Projeto atualizado',
            'project.delete' => 'Projeto removido',
            'project.publish' => 'Projeto publicado',
            'project.unpublish' => 'Projeto despublicado',
            'file.upload' => 'Arquivo enviado',
            'file.delete' => 'Arquivo removido',
            'file.available' => 'Arquivo disponibilizado',
            'file.unavailable' => 'Arquivo ocultado',
            default => $this->event,
        };
    }
}
