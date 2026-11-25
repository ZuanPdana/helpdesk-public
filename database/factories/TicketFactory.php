<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraphs(2, true),
            'location' => fake()->randomElement(['IT Department', 'Finance Department', 'HR Department', 'Marketing Department', 'Operations Room', 'Server Room', 'Meeting Room A', 'Lobby']),
            'priority' => fake()->randomElement(TicketPriority::cases()),
            'status' => TicketStatus::Open,
        ];
    }

    public function assigned(User $technician): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::Assigned,
            'assigned_to' => $technician->id,
        ]);
    }

    public function inProgress(User $technician): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::InProgress,
            'assigned_to' => $technician->id,
        ]);
    }

    public function resolved(User $technician): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::Resolved,
            'assigned_to' => $technician->id,
            'resolved_at' => now(),
        ]);
    }

    public function closed(User $technician): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::Closed,
            'assigned_to' => $technician->id,
            'resolved_at' => now()->subDay(),
            'closed_at' => now(),
        ]);
    }

    public function withPriority(TicketPriority $priority): static
    {
        return $this->state(fn () => [
            'priority' => $priority,
        ]);
    }
}
