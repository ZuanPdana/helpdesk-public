<?php

namespace App\Http\Controllers\User;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\User;
use App\Notifications\TicketNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $query = Ticket::whereBelongsTo($request->user())
            ->with('assignee');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('ticket_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $allowedSorts = ['created_at', 'title', 'priority', 'status', 'ticket_number'];

        if (in_array($sortField, $allowedSorts, true)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->latest();
        }

        $tickets = $query->paginate(10)->withQueryString();

        $statuses = TicketStatus::cases();
        $priorities = TicketPriority::cases();

        return view('user.tickets.index', compact('tickets', 'statuses', 'priorities'));
    }

    public function create(): View
    {
        $priorities = TicketPriority::cases();

        return view('user.tickets.create', compact('priorities'));
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $ticket = Ticket::create([
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location' => $validated['location'] ?? null,
            'priority' => $validated['priority'],
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments/'.$ticket->id, 'public');

                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $request->user()->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'type' => 'evidence',
                ]);
            }
        }

        $admins = User::query()
            ->where('role', UserRole::Admin)
            ->get();

        Notification::send($admins, TicketNotification::created($ticket, $request->user()));
        $request->user()->notify(TicketNotification::created($ticket, $request->user()));

        return redirect()
            ->route('user.tickets.show', $ticket)
            ->with('success', 'Ticket created successfully! Your ticket number is '.$ticket->ticket_number);
    }

    public function show(Ticket $ticket): View
    {
        Gate::authorize('view', $ticket);

        $ticket->load([
            'user',
            'assignee',
            'attachments.user',
            'comments.user',
            'histories.user',
        ]);

        return view('user.tickets.show', compact('ticket'));
    }

    public function closeTicket(\App\Http\Requests\User\CloseTicketRequest $request, Ticket $ticket): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validated();
        $previousStatus = $ticket->status;

        $ticket->status = \App\Enums\TicketStatus::Closed;
        $ticket->closed_at = now();
        if (! $ticket->resolved_at) {
            $ticket->resolved_at = now();
        }
        $ticket->save();

        $ticket->histories()->create([
            'user_id' => $request->user()->id,
            'action' => 'status_changed',
            'old_value' => $previousStatus->value,
            'new_value' => \App\Enums\TicketStatus::Closed->value,
            'description' => 'Ticket closed by requester',
        ]);

        // Notify admins and technician
        $admins = User::query()
            ->where('role', \App\Enums\UserRole::Admin)
            ->get();

        if ($ticket->assignee) {
            $ticket->assignee->notify(\App\Notifications\TicketNotification::statusChanged($ticket, $previousStatus, \App\Enums\TicketStatus::Closed, $request->user()));
        }

        Notification::send($admins, \App\Notifications\TicketNotification::statusChanged($ticket, $previousStatus, \App\Enums\TicketStatus::Closed, $request->user()));

        return redirect()
            ->route('user.tickets.show', $ticket)
            ->with('success', 'Ticket closed successfully.');
    }
}
