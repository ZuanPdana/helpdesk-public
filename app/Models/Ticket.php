<?php

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'title',
    'description',
    'location',
    'priority',
    'status',
    'assigned_to',
    'resolved_at',
    'closed_at',
])]
class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory;

    protected $attributes = [
        'status' => 'open',
        'priority' => 'medium',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'priority' => TicketPriority::class,
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket): void {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = self::generateTicketNumber();
            }

            if (empty($ticket->user_id)) {
                $ticket->user_id = auth()->id();
            }
        });

        static::created(function (Ticket $ticket): void {
            $ticket->histories()->create([
                'user_id' => $ticket->user_id,
                'action' => 'created',
                'new_value' => $ticket->status->value,
                'description' => 'Ticket created',
            ]);
        });
    }

    public static function generateTicketNumber(): string
    {
        $year = now()->format('Y');
        $lastTicket = self::whereYear('created_at', $year)
            ->orderByDesc('id')
            ->first();

        $sequence = $lastTicket
            ? ((int) substr($lastTicket->ticket_number, -5)) + 1
            : 1;

        return sprintf('HD-%s-%05d', $year, $sequence);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function evidenceAttachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class)->where('type', 'evidence');
    }

    public function resolutionAttachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class)->where('type', 'resolution');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(TicketHistory::class);
    }
}
