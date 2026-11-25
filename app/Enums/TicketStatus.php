<?php

namespace App\Enums;

enum TicketStatus: string
{
    case Open = 'open';
    case Assigned = 'assigned';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Open => __('tickets.status.open'),
            self::Assigned => __('tickets.status.assigned'),
            self::InProgress => __('tickets.status.in_progress'),
            self::Resolved => __('tickets.status.resolved'),
            self::Closed => __('tickets.status.closed'),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Open => 'blue',
            self::Assigned => 'indigo',
            self::InProgress => 'amber',
            self::Resolved => 'emerald',
            self::Closed => 'slate',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Open => 'circle',
            self::Assigned => 'user-check',
            self::InProgress => 'loader',
            self::Resolved => 'check-circle',
            self::Closed => 'archive',
        };
    }

    /**
     * @return array<int, self>
     */
    public static function orderedSteps(): array
    {
        return [
            self::Open,
            self::Assigned,
            self::InProgress,
            self::Resolved,
            self::Closed,
        ];
    }

    public function stepIndex(): int
    {
        return array_search($this, self::orderedSteps(), true);
    }
}
