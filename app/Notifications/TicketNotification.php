<?php

namespace App\Notifications;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class TicketNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $event,
        public int $ticketId,
        public string $ticketNumber,
        public ?string $actorName = null,
        public ?string $fromStatus = null,
        public ?string $toStatus = null,
        public ?string $priority = null,
        public ?string $assignedTechnicianName = null,
    ) {}

    public static function created(Ticket $ticket, User $actor): self
    {
        return new self(
            event: 'ticket_created',
            ticketId: $ticket->id,
            ticketNumber: $ticket->ticket_number,
            actorName: $actor->name,
            priority: $ticket->priority instanceof TicketPriority ? $ticket->priority->value : (string) $ticket->priority,
        );
    }

    public static function assigned(Ticket $ticket, User $actor, ?User $assignee): self
    {
        return new self(
            event: 'ticket_assigned',
            ticketId: $ticket->id,
            ticketNumber: $ticket->ticket_number,
            actorName: $actor->name,
            assignedTechnicianName: $assignee?->name,
        );
    }

    public static function statusChanged(Ticket $ticket, TicketStatus $fromStatus, TicketStatus $toStatus, User $actor): self
    {
        return new self(
            event: 'ticket_status_changed',
            ticketId: $ticket->id,
            ticketNumber: $ticket->ticket_number,
            actorName: $actor->name,
            fromStatus: $fromStatus->value,
            toStatus: $toStatus->value,
        );
    }

    public static function commentAdded(Ticket $ticket, User $actor): self
    {
        return new self(
            event: 'ticket_comment_added',
            ticketId: $ticket->id,
            ticketNumber: $ticket->ticket_number,
            actorName: $actor->name,
        );
    }

    public static function resolutionEvidenceUploaded(Ticket $ticket, User $actor): self
    {
        return new self(
            event: 'ticket_resolution_evidence_uploaded',
            ticketId: $ticket->id,
            ticketNumber: $ticket->ticket_number,
            actorName: $actor->name,
        );
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $isAdmin = $notifiable instanceof User && $notifiable->isAdmin();
        $isRequester = $notifiable instanceof User && $notifiable->isUser();

        return [
            'event' => $this->event,
            'category' => $this->category(),
            'title' => $this->titleFor($isAdmin, $isRequester),
            'message' => $this->messageFor($isAdmin, $isRequester),
            'ticket_id' => $this->ticketId,
            'ticket_number' => $this->ticketNumber,
            'actor_name' => $this->actorName,
            'from_status' => $this->fromStatus,
            'to_status' => $this->toStatus,
            'priority' => $this->priority,
            'assigned_technician_name' => $this->assignedTechnicianName,
            'action_url' => $this->actionUrlFor($notifiable),
            'icon' => $this->icon(),
        ];
    }

    private function titleFor(bool $isAdmin, bool $isRequester): string
    {
        return match ($this->event) {
            'ticket_created' => $isAdmin && $this->isUrgent()
                ? 'Urgent Ticket'
                : ($isAdmin ? 'New Ticket' : 'Ticket Created'),
            'ticket_assigned' => $isRequester ? 'Technician Assigned' : 'Ticket Assigned',
            'ticket_status_changed' => $this->statusTitle(),
            'ticket_comment_added' => $isRequester ? 'New Technician Reply' : 'New Ticket Comment',
            'ticket_resolution_evidence_uploaded' => $isRequester ? 'Repair Evidence Available' : 'New Repair Evidence',
            default => 'Notification',
        };
    }

    private function messageFor(bool $isAdmin, bool $isRequester): string
    {
        return match ($this->event) {
            'ticket_created' => $isAdmin
                ? ($this->isUrgent()
                    ? 'Ticket #'.$this->ticketNumber.' requires immediate attention.'
                    : $this->actorName.' created ticket #'.$this->ticketNumber.'.')
                : 'Your ticket #'.$this->ticketNumber.' was successfully submitted.',
            'ticket_assigned' => $this->assignedTechnicianName
                ? $this->assignedTechnicianName.' has been assigned to ticket #'.$this->ticketNumber.'.'
                : 'A technician has been assigned to ticket #'.$this->ticketNumber.'.',
            'ticket_status_changed' => $isAdmin
                ? 'Ticket #'.$this->ticketNumber.' changed from '.$this->statusLabel($this->fromStatus).' to '.$this->statusLabel($this->toStatus).'.'
                : 'Your ticket #'.$this->ticketNumber.' is now '.$this->statusLabel($this->toStatus).'.',
            'ticket_comment_added' => $isRequester
                ? 'A technician replied to ticket #'.$this->ticketNumber.'.'
                : 'A new comment was added to ticket #'.$this->ticketNumber.'.',
            'ticket_resolution_evidence_uploaded' => $isRequester
                ? 'Repair evidence has been uploaded for ticket #'.$this->ticketNumber.'.'
                : 'Technician uploaded repair evidence for ticket #'.$this->ticketNumber.'.',
            default => 'You have a new notification.',
        };
    }

    private function actionUrlFor(object $notifiable): string
    {
        if ($notifiable instanceof User && $notifiable->isAdmin()) {
            return route('admin.tickets.show', $this->ticketId);
        }

        return route('user.tickets.show', $this->ticketId);
    }

    private function category(): string
    {
        return match ($this->event) {
            'ticket_comment_added' => 'comment',
            'ticket_status_changed' => 'status',
            'ticket_resolution_evidence_uploaded' => 'system',
            default => 'ticket',
        };
    }

    private function icon(): string
    {
        return match ($this->event) {
            'ticket_created' => 'ticket-plus',
            'ticket_assigned' => 'user-check',
            'ticket_status_changed' => 'status-updated',
            'ticket_comment_added' => 'message',
            'ticket_resolution_evidence_uploaded' => 'paperclip',
            default => 'bell',
        };
    }

    private function statusTitle(): string
    {
        return match ($this->toStatus) {
            TicketStatus::Resolved->value => 'Ticket Resolved',
            TicketStatus::Closed->value => 'Ticket Closed',
            default => 'Ticket Status Updated',
        };
    }

    private function statusLabel(?string $status): string
    {
        return match ($status) {
            TicketStatus::Open->value => 'Open',
            TicketStatus::Assigned->value => 'Assigned',
            TicketStatus::InProgress->value => 'In Progress',
            TicketStatus::Resolved->value => 'Resolved',
            TicketStatus::Closed->value => 'Closed',
            default => Str::title(str_replace('_', ' ', (string) $status)),
        };
    }

    private function isUrgent(): bool
    {
        return in_array($this->priority, [TicketPriority::High->value, TicketPriority::Urgent->value], true);
    }
}
