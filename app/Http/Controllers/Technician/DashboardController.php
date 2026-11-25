<?php

namespace App\Http\Controllers\Technician;

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
            'total' => Ticket::where('assigned_to', $user->id)->count(),
            'assigned' => Ticket::where('assigned_to', $user->id)->where('status', TicketStatus::Assigned)->count(),
            'in_progress' => Ticket::where('assigned_to', $user->id)->where('status', TicketStatus::InProgress)->count(),
            'resolved' => Ticket::where('assigned_to', $user->id)->where('status', TicketStatus::Resolved)->count(),
            'closed' => Ticket::where('assigned_to', $user->id)->where('status', TicketStatus::Closed)->count(),
        ];

        $recentTickets = Ticket::where('assigned_to', $user->id)
            ->with('user')
            ->orderByDesc('updated_at')
            ->take(6)
            ->get();

        return view('technician.dashboard', compact('stats', 'recentTickets'));
    }
}
