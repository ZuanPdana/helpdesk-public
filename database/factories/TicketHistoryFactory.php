<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketHistory>
 */
class TicketHistoryFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'action' => 'status_changed',
            'old_value' => 'open',
            'new_value' => 'assigned',
            'description' => 'Status changed from Open to Assigned',
            'created_at' => now(),
        ];
    }
}
