<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $ticketTable = (new Ticket)->getTable();
        $userTable = (new User)->getTable();
        $databaseDriver = DB::connection()->getDriverName();

        $monthlyTicketsQuery = Ticket::query()
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth());

        if ($databaseDriver === 'sqlite') {
            $monthlyTicketsQuery
                ->selectRaw("strftime('%Y', created_at) as year, strftime('%m', created_at) as month, COUNT(*) as total")
                ->groupByRaw("strftime('%Y', created_at), strftime('%m', created_at)")
                ->orderByRaw("strftime('%Y', created_at), strftime('%m', created_at)");
        } else {
            $monthlyTicketsQuery
                ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total')
                ->groupByRaw('YEAR(created_at), MONTH(created_at)')
                ->orderByRaw('YEAR(created_at), MONTH(created_at)');
        }

        $monthlyTickets = $monthlyTicketsQuery
            ->get()
            ->map(function ($row): array {
                $period = now()
                    ->setDate((int) $row->year, (int) $row->month, 1)
                    ->format('M Y');

                return [
                    'period' => $period,
                    'total' => (int) $row->total,
                ];
            });

        $priorityDistribution = Ticket::query()
            ->select('priority')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('priority')
            ->orderByDesc('total')
            ->get();

        $statusDistribution = Ticket::query()
            ->select('status')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        $departmentStatistics = Ticket::query()
            ->join($userTable.' as requesters', 'requesters.id', '=', $ticketTable.'.user_id')
            ->selectRaw('COALESCE(requesters.department, ?) as department, COUNT(*) as total', ['No Department'])
            ->groupBy('requesters.department')
            ->orderByDesc('total')
            ->get();

        $technicianPerformance = User::query()
            ->where('role', UserRole::Technician)
            ->withCount([
                'assignedTickets as total_assigned_tickets',
                'assignedTickets as resolved_tickets' => fn ($query) => $query->where('status', TicketStatus::Resolved),
                'assignedTickets as closed_tickets' => fn ($query) => $query->where('status', TicketStatus::Closed),
                'assignedTickets as active_assignments_count' => fn ($query) => $query->whereIn('status', ['open', 'assigned', 'in_progress']),
            ])
            ->orderByDesc('closed_tickets')
            ->orderByDesc('resolved_tickets')
            ->orderBy('name')
            ->get();

        return view('admin.reports.index', compact(
            'monthlyTickets',
            'priorityDistribution',
            'statusDistribution',
            'departmentStatistics',
            'technicianPerformance',
        ));
    }
}
