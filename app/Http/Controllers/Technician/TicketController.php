<?php

namespace App\Http\Controllers\Technician;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Technician\UpdateTicketStatusRequest;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\User;
use App\Notifications\TicketNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class TicketController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Ticket::query()
            ->where('assigned_to', $user->id)
            ->with(['user', 'assignee']);

        if ($search = $request->input('search')) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($priority = $request->input('priority')) {
            $query->where('priority', $priority);
        }

        $sortField = $request->input('sort', 'updated_at');
        $sortDirection = $request->input('direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['ticket_number', 'title', 'priority', 'status', 'created_at', 'updated_at'];

        if (in_array($sortField, $allowedSorts, true)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        $tickets = $query->paginate(10)->withQueryString();
        $availableTicketsQuery = Ticket::query()
            ->whereNull('assigned_to')
            ->where('status', TicketStatus::Open)
            ->with('user');

        if ($search = $request->input('search')) {
            $availableTicketsQuery->where(function ($builder) use ($search): void {
                $builder->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"));
            });
        }

        if ($priority = $request->input('priority')) {
            $availableTicketsQuery->where('priority', $priority);
        }

        $availableTickets = $availableTicketsQuery
            ->latest()
            ->paginate(10, ['*'], 'available_page')
            ->withQueryString();
        $statuses = TicketStatus::cases();
        $priorities = TicketPriority::cases();

        return view('technician.tickets.index', compact('tickets', 'availableTickets', 'statuses', 'priorities'));
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

        return view('technician.tickets.show', compact('ticket'));
    }

    public function assignSelf(Request $request, Ticket $ticket): RedirectResponse
    {
        $authorization = Gate::inspect('selfAssign', $ticket);

        if ($authorization->denied()) {
            return redirect()
                ->route('technician.tickets.show', $ticket)
                ->with('error', __('tickets.self_assign_unavailable'));
        }

        $technician = $request->user();
        $assigned = DB::transaction(function () use ($ticket, $technician): bool {
            $affected = Ticket::query()
                ->whereKey($ticket->id)
                ->whereNull('assigned_to')
                ->where('status', TicketStatus::Open->value)
                ->update([
                    'assigned_to' => $technician->id,
                    'status' => TicketStatus::Assigned->value,
                ]);

            if ($affected !== 1) {
                return false;
            }

            $ticket->histories()->create([
                'user_id' => $technician->id,
                'action' => 'ticket_assigned',
                'old_value' => null,
                'new_value' => $technician->name,
                'description' => __('tickets.self_assign_history', ['name' => $technician->name]),
            ]);

            return true;
        });

        if (! $assigned) {
            return redirect()
                ->route('technician.tickets.show', $ticket)
                ->with('error', __('tickets.self_assign_unavailable'));
        }

        $ticket->refresh()->load(['user', 'assignee']);
        $admins = User::query()->where('role', UserRole::Admin)->get();
        $notification = TicketNotification::assigned($ticket, $technician, $technician);

        Notification::send($admins, $notification);
        $ticket->user->notify($notification);

        return redirect()
            ->route('technician.tickets.show', $ticket)
            ->with('success', __('tickets.self_assign_success'));
    }

    public function updateStatus(UpdateTicketStatusRequest $request, Ticket $ticket): RedirectResponse
    {
        // Request handles authorization and basic validation
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

        // Notify ticket owner and admins
        $admins = User::query()
            ->where('role', UserRole::Admin)
            ->get();

        Notification::send($admins, TicketNotification::statusChanged($ticket, $previousStatus, $nextStatus, $request->user()));
        $ticket->user->notify(TicketNotification::statusChanged($ticket, $previousStatus, $nextStatus, $request->user()));

        return redirect()
            ->route('technician.tickets.show', $ticket)
            ->with('success', 'Ticket status updated successfully.');
    }

    public function uploadEvidence(Request $request, Ticket $ticket): RedirectResponse
    {
        Gate::authorize('view', $ticket);

        $request->validate([
            'evidence' => ['required', 'array'],
            'evidence.*' => ['file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($request->hasFile('evidence')) {
            foreach ($request->file('evidence') as $file) {
                $path = $file->store('attachments/'.$ticket->id, 'public');

                TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'user_id' => $request->user()->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'type' => 'resolution',
                ]);
            }

            $ticket->histories()->create([
                'user_id' => $request->user()->id,
                'action' => 'evidence_uploaded',
                'description' => $request->user()->name.' uploaded resolution evidence',
            ]);

            // notify ticket owner
            $ticket->user->notify(TicketNotification::resolutionEvidenceUploaded($ticket, $request->user()));
        }

        return redirect()
            ->route('technician.tickets.show', $ticket)
            ->with('success', 'Evidence uploaded successfully.');
    }
}
