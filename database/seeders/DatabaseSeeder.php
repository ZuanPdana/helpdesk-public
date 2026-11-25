<?php

namespace Database\Seeders;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\TicketComment;
use App\Models\TicketHistory;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@helpdesk.test',
            'department' => 'IT',
        ]);

        // Create technicians
        $tech1 = User::factory()->technician()->create([
            'name' => 'John Technician',
            'email' => 'john@helpdesk.test',
            'department' => 'IT Support',
        ]);

        $tech2 = User::factory()->technician()->create([
            'name' => 'Alex Technician',
            'email' => 'alex@helpdesk.test',
            'department' => 'IT Support',
        ]);

        // Create normal user
        $user = User::factory()->user()->create([
            'name' => 'Demo User',
            'email' => 'user@helpdesk.test',
            'department' => 'Finance',
        ]);

        // Create tickets for the demo user with various statuses
        $ticketData = [
            [
                'title' => 'Printer not working in Finance Department',
                'description' => 'The main printer in the Finance Department has stopped working. It shows an error message "Paper Jam" but there is no paper stuck inside. We have tried restarting it multiple times but the issue persists.',
                'location' => 'Finance Department',
                'priority' => TicketPriority::High,
                'status' => TicketStatus::InProgress,
                'assigned_to' => $tech1->id,
            ],
            [
                'title' => 'Cannot connect to company VPN',
                'description' => 'I am unable to connect to the company VPN when working from home. The VPN client shows "Connection timed out" error. This started happening after the latest Windows update.',
                'location' => 'Remote / Work from Home',
                'priority' => TicketPriority::Urgent,
                'status' => TicketStatus::Assigned,
                'assigned_to' => $tech2->id,
            ],
            [
                'title' => 'Request for new monitor',
                'description' => 'My current monitor is flickering intermittently. This causes eye strain during long working hours. Requesting a replacement monitor.',
                'location' => 'Finance Department - Desk 4B',
                'priority' => TicketPriority::Medium,
                'status' => TicketStatus::Open,
                'assigned_to' => null,
            ],
            [
                'title' => 'Email not syncing on mobile device',
                'description' => 'Company email stopped syncing on my mobile phone. I have tried removing and re-adding the account but it keeps asking for credentials repeatedly.',
                'location' => 'N/A',
                'priority' => TicketPriority::Low,
                'status' => TicketStatus::Resolved,
                'assigned_to' => $tech1->id,
                'resolved_at' => now()->subDay(),
            ],
            [
                'title' => 'Software installation request - Adobe Acrobat',
                'description' => 'I need Adobe Acrobat Pro installed on my workstation for editing PDF documents. This is required for processing financial reports.',
                'location' => 'Finance Department - Desk 4B',
                'priority' => TicketPriority::Medium,
                'status' => TicketStatus::Closed,
                'assigned_to' => $tech2->id,
                'resolved_at' => now()->subDays(3),
                'closed_at' => now()->subDays(2),
            ],
            [
                'title' => 'Network speed is very slow',
                'description' => 'The network speed in the Finance Department has been very slow for the past two days. It takes a long time to load web pages and download files.',
                'location' => 'Finance Department',
                'priority' => TicketPriority::High,
                'status' => TicketStatus::Open,
                'assigned_to' => null,
            ],
        ];

        foreach ($ticketData as $data) {
            $ticket = Ticket::create(array_merge($data, [
                'user_id' => $user->id,
            ]));

            // Add history entries based on status
            $this->addHistoryForTicket($ticket, $user, $tech1, $tech2);

            // Add comments to some tickets
            if (in_array($ticket->status, [TicketStatus::InProgress, TicketStatus::Resolved, TicketStatus::Closed], true)) {
                $this->addCommentsForTicket($ticket, $user, $ticket->assignee);
            }
        }

        // Create a few more tickets for the user to have meaningful stats
        Ticket::factory(3)
            ->for($user)
            ->create(['status' => TicketStatus::Open]);

        Ticket::factory(2)
            ->for($user)
            ->inProgress($tech1)
            ->create();
    }

    private function addHistoryForTicket(Ticket $ticket, User $user, User $tech1, User $tech2): void
    {
        $baseTime = $ticket->created_at->copy();
        $techId = $ticket->assigned_to;

        if ($ticket->status !== TicketStatus::Open) {
            TicketHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => $techId,
                'action' => 'status_changed',
                'old_value' => 'open',
                'new_value' => 'assigned',
                'description' => 'Ticket assigned to technician',
                'created_at' => $baseTime->addMinutes(30),
            ]);
        }

        if (in_array($ticket->status, [TicketStatus::InProgress, TicketStatus::Resolved, TicketStatus::Closed], true)) {
            TicketHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => $techId,
                'action' => 'status_changed',
                'old_value' => 'assigned',
                'new_value' => 'in_progress',
                'description' => 'Technician started working on the ticket',
                'created_at' => $baseTime->addHour(),
            ]);
        }

        if (in_array($ticket->status, [TicketStatus::Resolved, TicketStatus::Closed], true)) {
            TicketHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => $techId,
                'action' => 'status_changed',
                'old_value' => 'in_progress',
                'new_value' => 'resolved',
                'description' => 'Issue has been resolved',
                'created_at' => $baseTime->addHours(2),
            ]);
        }

        if ($ticket->status === TicketStatus::Closed) {
            TicketHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'action' => 'status_changed',
                'old_value' => 'resolved',
                'new_value' => 'closed',
                'description' => 'Ticket closed by user',
                'created_at' => $baseTime->addHours(4),
            ]);
        }
    }

    private function addCommentsForTicket(Ticket $ticket, User $user, ?User $technician): void
    {
        if (! $technician) {
            return;
        }

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'body' => 'Any updates on this issue? It is affecting my daily work.',
            'created_at' => $ticket->created_at->copy()->addHours(2),
            'updated_at' => $ticket->created_at->copy()->addHours(2),
        ]);

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => $technician->id,
            'body' => 'I am looking into this issue now. Will update you shortly with my findings.',
            'created_at' => $ticket->created_at->copy()->addHours(3),
            'updated_at' => $ticket->created_at->copy()->addHours(3),
        ]);

        if (in_array($ticket->status, [TicketStatus::Resolved, TicketStatus::Closed], true)) {
            TicketComment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $technician->id,
                'body' => 'The issue has been resolved. Please let me know if you experience any further problems.',
                'created_at' => $ticket->created_at->copy()->addHours(5),
                'updated_at' => $ticket->created_at->copy()->addHours(5),
            ]);
        }
    }
}
