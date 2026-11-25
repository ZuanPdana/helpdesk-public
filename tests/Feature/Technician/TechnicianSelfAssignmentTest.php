<?php

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('technician can self assign an unassigned open ticket', function () {
    $technician = User::factory()->technician()->create();
    $requester = User::factory()->user()->create();
    $ticket = Ticket::factory()->for($requester)->create();

    $this->actingAs($technician)
        ->post(route('technician.tickets.assign-self', $ticket))
        ->assertRedirect(route('technician.tickets.show', $ticket))
        ->assertSessionHas('success', __('tickets.self_assign_success'));

    $ticket->refresh();

    expect($ticket->assigned_to)->toBe($technician->id)
        ->and($ticket->status)->toBe(TicketStatus::Assigned)
        ->and($ticket->histories()->where('action', 'ticket_assigned')->first())
        ->not->toBeNull();
});

test('technician can update the status after self assigning a ticket', function () {
    $technician = User::factory()->technician()->create();
    $ticket = Ticket::factory()->create();

    $this->actingAs($technician)
        ->post(route('technician.tickets.assign-self', $ticket));

    $this->actingAs($technician)
        ->patch(route('technician.tickets.status.update', $ticket), [
            'status' => TicketStatus::InProgress->value,
        ])
        ->assertRedirect(route('technician.tickets.show', $ticket));

    expect($ticket->refresh()->status)->toBe(TicketStatus::InProgress);
});

test('self assignment notifies the requester and admins', function () {
    Notification::fake();

    $technician = User::factory()->technician()->create();
    $admin = User::factory()->admin()->create();
    $requester = User::factory()->user()->create();
    $ticket = Ticket::factory()->for($requester)->create();

    $this->actingAs($technician)->post(route('technician.tickets.assign-self', $ticket));

    Notification::assertSentTo($requester, TicketNotification::class, function (TicketNotification $notification) use ($ticket, $technician): bool {
        return $notification->event === 'ticket_assigned'
            && $notification->ticketId === $ticket->id
            && $notification->assignedTechnicianName === $technician->name;
    });
    Notification::assertSentTo($admin, TicketNotification::class);
});

test('technician cannot overwrite an assigned ticket', function () {
    $technician = User::factory()->technician()->create();
    $existingAssignee = User::factory()->technician()->create();
    $requester = User::factory()->user()->create();
    $ticket = Ticket::factory()->for($requester)->assigned($existingAssignee)->create();

    $this->actingAs($technician)
        ->post(route('technician.tickets.assign-self', $ticket))
        ->assertRedirect(route('technician.tickets.show', $ticket))
        ->assertSessionHas('error', __('tickets.self_assign_unavailable'));

    $ticket->refresh();

    expect($ticket->assigned_to)->toBe($existingAssignee->id)
        ->and($ticket->status)->toBe(TicketStatus::Assigned);
});

test('a second self assignment attempt loses without overwriting the winner', function () {
    $winningTechnician = User::factory()->technician()->create();
    $otherTechnician = User::factory()->technician()->create();
    $requester = User::factory()->user()->create();
    $ticket = Ticket::factory()->for($requester)->create();

    $this->actingAs($winningTechnician)->post(route('technician.tickets.assign-self', $ticket));

    $this->actingAs($otherTechnician)
        ->post(route('technician.tickets.assign-self', $ticket))
        ->assertSessionHas('error', __('tickets.self_assign_unavailable'));

    expect($ticket->refresh()->assigned_to)->toBe($winningTechnician->id);
});

test('only technicians can use the self assignment endpoint', function () {
    $user = User::factory()->user()->create();
    $admin = User::factory()->admin()->create();
    $ticket = Ticket::factory()->create();

    $this->actingAs($user)
        ->post(route('technician.tickets.assign-self', $ticket))
        ->assertForbidden();

    $this->actingAs($admin)
        ->post(route('technician.tickets.assign-self', $ticket))
        ->assertForbidden();
});

test('guests cannot use the self assignment endpoint', function () {
    $ticket = Ticket::factory()->create();

    $this->post(route('technician.tickets.assign-self', $ticket))
        ->assertRedirect(route('login'));
});

test('technician sees available and assigned tickets separately', function () {
    $technician = User::factory()->technician()->create();
    $requester = User::factory()->user()->create();
    $availableTicket = Ticket::factory()->for($requester)->create();
    $assignedTicket = Ticket::factory()->for($requester)->assigned($technician)->create();

    $this->actingAs($technician)
        ->get(route('technician.tickets.index'))
        ->assertSuccessful()
        ->assertSee(__('tickets.available_tickets'))
        ->assertSee($availableTicket->ticket_number)
        ->assertSee($assignedTicket->ticket_number);
});

test('technician can still access an assigned ticket', function () {
    $technician = User::factory()->technician()->create();
    $ticket = Ticket::factory()->assigned($technician)->create();

    $this->actingAs($technician)
        ->get(route('technician.tickets.show', $ticket))
        ->assertSuccessful()
        ->assertSee(__('tickets.self_assigned'));
});
