<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $stats = [
            'total_tickets' => Ticket::count(),
            'open' => Ticket::where('status', TicketStatus::Open)->count(),
            'assigned' => Ticket::where('status', TicketStatus::Assigned)->count(),
            'in_progress' => Ticket::where('status', TicketStatus::InProgress)->count(),
            'resolved' => Ticket::where('status', TicketStatus::Resolved)->count(),
            'closed' => Ticket::where('status', TicketStatus::Closed)->count(),
            'total_users' => User::where('role', UserRole::User)->count(),
            'total_technicians' => User::where('role', UserRole::Technician)->count(),
        ];

        $recentTicketsQuery = Ticket::query()
            ->with(['user', 'assignee']);

        if ($search = $request->input('ticket_search')) {
            $recentTicketsQuery->where(function ($query) use ($search): void {
                $query->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('assignee', fn ($assigneeQuery) => $assigneeQuery->where('name', 'like', "%{$search}%"));
            });
        }

        $sortField = $request->input('ticket_sort', 'created_at');
        $sortDirection = $request->input('ticket_direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['ticket_number', 'title', 'priority', 'status', 'created_at'];

        if (in_array($sortField, $allowedSorts, true)) {
            $recentTicketsQuery->orderBy($sortField, $sortDirection);
        } else {
            $recentTicketsQuery->latest();
        }

        $recentTickets = $recentTicketsQuery
            ->paginate(10, ['*'], 'tickets_page')
            ->withQueryString();

        $recentActivities = TicketHistory::query()
            ->with(['ticket', 'user'])
            ->latest('created_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentTickets', 'recentActivities'));
    }
}
