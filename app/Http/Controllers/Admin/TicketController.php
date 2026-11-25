<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateTicketAssignmentRequest;
use App\Http\Requests\Admin\UpdateTicketPriorityRequest;
use App\Http\Requests\Admin\UpdateTicketStatusRequest;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $query = Ticket::query()
            ->with(['user', 'assignee']);

        if ($search = $request->input('search')) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('assignee', fn ($assigneeQuery) => $assigneeQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        if ($technician = $request->input('technician')) {
            $query->where('assigned_to', $technician);
        }

        if ($requester = $request->input('requester')) {
            $query->where('user_id', $requester);
        }

        if ($fromDate = $request->input('from_date')) {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate = $request->input('to_date')) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['ticket_number', 'title', 'priority', 'status', 'created_at', 'updated_at'];

        if (in_array($sortField, $allowedSorts, true)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        $tickets = $query->paginate(10)->withQueryString();
        $statuses = TicketStatus::cases();
        $priorities = TicketPriority::cases();
        $technicians = User::query()->where('role', UserRole::Technician)->orderBy('name')->get(['id', 'name']);
        $requesters = User::query()->where('role', UserRole::User)->orderBy('name')->get(['id', 'name']);

        return view('admin.tickets.index', compact('tickets', 'statuses', 'priorities', 'technicians', 'requesters'));
    }

    public function show(Ticket $ticket): View
    {
        $ticket->load([
            'user',
            'assignee',
            'attachments.user',
            'comments.user',
            'histories.user',
        ]);

        $technicians = User::query()
            ->where('role', UserRole::Technician)
            ->withCount([
                'assignedTickets as active_assignments_count' => fn ($query) => $query->whereIn('status', ['open', 'assigned', 'in_progress']),
            ])
            ->orderBy('name')
            ->get();

        return view('admin.tickets.show', [
            'ticket' => $ticket,
            'technicians' => $technicians,
            'priorities' => TicketPriority::cases(),
            'statuses' => array_values(array_filter(
                TicketStatus::orderedSteps(),
                fn (TicketStatus $status): bool => $status !== TicketStatus::Closed,
            )),
        ]);
    }

    public function updatePriority(UpdateTicketPriorityRequest $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validated();
        $nextPriority = TicketPriority::from($validated['priority']);
        $previousPriority = $ticket->priority;

        if ($previousPriority !== $nextPriority) {
            $ticket->priority = $nextPriority;
            $ticket->save();

            $ticket->histories()->create([
                'user_id' => $request->user()->id,
                'action' => 'priority_changed',
                'old_value' => $previousPriority->value,
                'new_value' => $nextPriority->value,
                'description' => 'Priority updated from '.$previousPriority->label().' to '.$nextPriority->label(),
            ]);
        }

        return redirect()
            ->route('admin.tickets.show', $ticket)
            ->with('success', __('tickets.priority_updated'));
    }

    public function updateAssignment(UpdateTicketAssignmentRequest $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validated();
        $previousAssignee = $ticket->assignee;
        $previousStatus = $ticket->status;

        $ticket->assigned_to = $validated['assigned_to'] ?? null;

        if ($ticket->assigned_to && $ticket->status === TicketStatus::Open) {
            $ticket->status = TicketStatus::Assigned;
        }

        $ticket->save();

        $assignee = $ticket->assignee;

        if ($ticket->assigned_to) {
            $ticket->user->notify(TicketNotification::assigned($ticket, $request->user(), $assignee));
        }

        if ($previousStatus !== $ticket->status) {
            $admins = User::query()
                ->where('role', UserRole::Admin)
                ->get();

            Notification::send($admins, TicketNotification::statusChanged($ticket, $previousStatus, $ticket->status, $request->user()));
        }

        $ticket->histories()->create([
            'user_id' => $request->user()->id,
            'action' => $ticket->assigned_to ? 'ticket_assigned' : 'ticket_unassigned',
            'old_value' => $previousAssignee?->name,
            'new_value' => $assignee?->name,
            'description' => $ticket->assigned_to
                ? 'Ticket assigned to '.($assignee?->name ?? 'technician')
                : 'Ticket assignment removed',
        ]);

        return redirect()
            ->route('admin.tickets.show', $ticket)
            ->with('success', 'Ticket assignment updated successfully.');
    }

    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validated();
        $nextStatus = TicketStatus::from($validated['status']);
        $previousStatus = $ticket->status;

        $ticket->status = $nextStatus;

        if ($nextStatus === TicketStatus::Resolved && ! $ticket->resolved_at) {
            $ticket->resolved_at = now();
        }

        if ($nextStatus === TicketStatus::Closed) {
            $ticket->resolved_at ??= now();
            $ticket->closed_at = now();
        }

        if ($nextStatus !== TicketStatus::Closed) {
            $ticket->closed_at = null;
        }

        if (! in_array($nextStatus, [TicketStatus::Resolved, TicketStatus::Closed], true)) {
            $ticket->resolved_at = null;
        }

        $ticket->save();

        $ticket->histories()->create([
            'user_id' => $request->user()->id,
            'action' => 'status_changed',
            'old_value' => $previousStatus->value,
            'new_value' => $nextStatus->value,
            'description' => 'Status updated from '.$previousStatus->label().' to '.$nextStatus->label(),
        ]);

        $admins = User::query()
            ->where('role', UserRole::Admin)
            ->get();

        Notification::send($admins, TicketNotification::statusChanged($ticket, $previousStatus, $nextStatus, $request->user()));
        $ticket->user->notify(TicketNotification::statusChanged($ticket, $previousStatus, $nextStatus, $request->user()));

        return redirect()
            ->route('admin.tickets.show', $ticket)
            ->with('success', 'Ticket status updated successfully.');
    }
}
