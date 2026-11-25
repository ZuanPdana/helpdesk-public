<?php

namespace App\Policies;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    /**
     * Determine whether the user can view any tickets.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the ticket.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_id
            || $user->id === $ticket->assigned_to
            || ($user->isTechnician() && $ticket->assigned_to === null)
            || $user->isAdmin();
    }

    /**
     * Determine whether the technician can assign the ticket to themselves.
     */
    public function selfAssign(User $user, Ticket $ticket): bool
    {
        return $user->isTechnician()
            && $ticket->assigned_to === null
            && $ticket->status === TicketStatus::Open;
    }

    /**
     * Determine whether the user can create tickets.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can add a comment.
     */
    public function addComment(User $user, Ticket $ticket): bool
    {
        return $user->id === $ticket->user_id
            || $user->id === $ticket->assigned_to
            || $user->isAdmin();
    }
}
