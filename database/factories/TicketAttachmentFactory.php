<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketAttachment>
 */
class TicketAttachmentFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'file_name' => fake()->word().'.jpg',
            'file_path' => 'attachments/'.fake()->uuid().'.jpg',
            'file_type' => 'image/jpeg',
            'file_size' => fake()->numberBetween(50000, 5000000),
            'type' => 'evidence',
        ];
    }

    public function resolution(): static
    {
        return $this->state(fn () => [
            'type' => 'resolution',
        ]);
    }
}
