<?php

namespace App\Http\Controllers\User;

use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $stats = [
            'total' => Ticket::whereBelongsTo($user)->count(),
            'open' => Ticket::whereBelongsTo($user)->where('status', TicketStatus::Open)->count(),
            'assigned' => Ticket::whereBelongsTo($user)->where('status', TicketStatus::Assigned)->count(),
            'in_progress' => Ticket::whereBelongsTo($user)->where('status', TicketStatus::InProgress)->count(),
            'resolved' => Ticket::whereBelongsTo($user)->where('status', TicketStatus::Resolved)->count(),
            'closed' => Ticket::whereBelongsTo($user)->where('status', TicketStatus::Closed)->count(),
        ];

        $recentTickets = Ticket::whereBelongsTo($user)
            ->with('assignee')
            ->latest()
            ->take(5)
            ->get();

        return view('user.dashboard', compact('stats', 'recentTickets'));
    }
}
