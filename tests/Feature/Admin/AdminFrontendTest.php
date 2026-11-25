<?php

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserAccountStatus;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('admin can view the dashboard with ticket data', function () {
    $admin = User::factory()->admin()->create();
    $requester = User::factory()->user()->create();
    $technician = User::factory()->technician()->create();

    $openTicket = Ticket::factory()->for($requester)->create(['title' => 'Network outage']);
    $assignedTicket = Ticket::factory()->for($requester)->assigned($technician)->create(['title' => 'Printer issue']);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertSuccessful()
        ->assertSee('Admin Dashboard')
        ->assertSee($openTicket->ticket_number)
        ->assertSee($assignedTicket->ticket_number)
        ->assertSee('Quick Actions');
});

test('non admin users cannot access the admin dashboard', function () {
    $user = User::factory()->user()->create();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('regular users can still view their dashboard', function () {
    $user = User::factory()->user()->create();

    $this->actingAs($user)
        ->get(route('user.dashboard'))
        ->assertSuccessful()
        ->assertSee('Dashboard');
});

test('admin can create a managed user', function () {
    $admin = User::factory()->admin()->create();

    $response = $this->actingAs($admin)->post(route('admin.users.store'), [
        'name' => 'Managed User',
        'email' => 'managed@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'role' => 'user',
        'phone' => '555-0101',
        'department' => 'Operations',
        'status' => UserAccountStatus::Active->value,
    ]);

    $managedUser = User::query()->where('email', 'managed@example.com')->firstOrFail();

    $response->assertRedirect(route('admin.users.index'));

    expect($managedUser->name)->toBe('Managed User');
    expect($managedUser->role->value)->toBe('user');
    $this->assertModelExists($managedUser);
});

test('admin can view the reports page', function () {
    $admin = User::factory()->admin()->create();
    $requester = User::factory()->user()->create(['department' => 'Finance']);
    $technician = User::factory()->technician()->create();

    Ticket::factory()->for($requester)->assigned($technician)->create();

    $this->actingAs($admin)
        ->get(route('admin.reports.index'))
        ->assertSuccessful()
        ->assertSee('Operational Reports')
        ->assertSee('Finance');
});

test('admin can assign a technician and advance ticket status', function () {
    $admin = User::factory()->admin()->create();
    $requester = User::factory()->user()->create();
    $technician = User::factory()->technician()->create();
    $ticket = Ticket::factory()->for($requester)->create();

    $this->actingAs($admin)
        ->patch(route('admin.tickets.assignment.update', $ticket), [
            'assigned_to' => $technician->id,
        ])
        ->assertRedirect(route('admin.tickets.show', $ticket));

    $ticket->refresh();

    expect($ticket->assigned_to)->toBe($technician->id);
    expect($ticket->status)->toBe(TicketStatus::Assigned);

    $this->actingAs($admin)
        ->patch(route('admin.tickets.status.update', $ticket), [
            'status' => TicketStatus::InProgress->value,
        ])
        ->assertRedirect(route('admin.tickets.show', $ticket));

    $ticket->refresh();

    expect($ticket->status)->toBe(TicketStatus::InProgress);
    expect($ticket->histories()->latest('id')->first()?->action)->toBe('status_changed');
});

test('admin cannot change a ticket status to closed', function () {
    $admin = User::factory()->admin()->create();
    $requester = User::factory()->user()->create();
    $technician = User::factory()->technician()->create();
    $ticket = Ticket::factory()->for($requester)->assigned($technician)->create();

    $this->actingAs($admin)
        ->get(route('admin.tickets.show', $ticket))
        ->assertSuccessful()
        ->assertDontSee('value="closed"');

    $this->patch(route('admin.tickets.status.update', $ticket), [
        'status' => TicketStatus::Closed->value,
    ])->assertSessionHasErrors('status');

    expect($ticket->fresh()->status)->toBe(TicketStatus::Assigned);
});

test('admin can update a ticket priority and the change is recorded', function () {
    $admin = User::factory()->admin()->create();
    $requester = User::factory()->user()->create();
    $ticket = Ticket::factory()->for($requester)->create([
        'priority' => TicketPriority::Low,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.tickets.show', $ticket))
        ->assertSuccessful()
        ->assertSee(route('admin.tickets.priority.update', $ticket))
        ->assertSee('Update Priority');

    $this->patch(route('admin.tickets.priority.update', $ticket), [
        'priority' => TicketPriority::Urgent->value,
    ])->assertRedirect(route('admin.tickets.show', $ticket));

    $ticket->refresh();
    $history = $ticket->histories()->latest('id')->firstOrFail();

    expect($ticket->priority)->toBe(TicketPriority::Urgent);
    expect($history->action)->toBe('priority_changed');
    expect($history->old_value)->toBe(TicketPriority::Low->value);
    expect($history->new_value)->toBe(TicketPriority::Urgent->value);
    expect($history->user_id)->toBe($admin->id);
});

test('non admins cannot update ticket priority', function () {
    $user = User::factory()->user()->create();
    $ticket = Ticket::factory()->for($user)->create([
        'priority' => TicketPriority::Medium,
    ]);

    $this->actingAs($user)
        ->patch(route('admin.tickets.priority.update', $ticket), [
            'priority' => TicketPriority::High->value,
        ])
        ->assertForbidden();

    expect($ticket->fresh()->priority)->toBe(TicketPriority::Medium);
});

test('admin cannot set an invalid ticket priority', function () {
    $admin = User::factory()->admin()->create();
    $requester = User::factory()->user()->create();
    $ticket = Ticket::factory()->for($requester)->create([
        'priority' => TicketPriority::Low,
    ]);

    $this->actingAs($admin)
        ->patch(route('admin.tickets.priority.update', $ticket), [
            'priority' => 'critical',
        ])
        ->assertSessionHasErrors('priority');

    expect($ticket->fresh()->priority)->toBe(TicketPriority::Low);
});

test('ticket creation notifies admins and the requester', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->user()->create();

    $response = $this->actingAs($user)->post(route('user.tickets.store'), [
        'title' => 'Network outage',
        'description' => 'The office internet is down.',
        'location' => 'Main Office',
        'priority' => TicketPriority::Medium->value,
    ]);

    $ticket = Ticket::query()->firstOrFail();

    $response->assertRedirect(route('user.tickets.show', $ticket));

    expect($admin->notifications)->toHaveCount(1);
    expect($user->notifications)->toHaveCount(1);
    expect($admin->notifications->first()->data['title'])->toBe('New Ticket');
    expect($user->notifications->first()->data['title'])->toBe('Ticket Created');
});

test('admin can open the notification center and mark notifications as read', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->user()->create();

    $this->actingAs($user)->post(route('user.tickets.store'), [
        'title' => 'Printer issue',
        'description' => 'The printer is jammed.',
        'location' => 'Front Desk',
        'priority' => TicketPriority::Low->value,
    ]);

    $ticket = Ticket::query()->firstOrFail();
    $notification = $admin->notifications()->firstOrFail();

    $this->actingAs($admin)
        ->get(route('admin.notifications.index'))
        ->assertSuccessful()
        ->assertSee('Notifications')
        ->assertSee('New Ticket');

    $this->actingAs($admin)
        ->patch(route('admin.notifications.read', $notification->id))
        ->assertRedirect(route('admin.tickets.show', $ticket));

    expect($notification->fresh()->read_at)->not->toBeNull();

    $this->actingAs($admin)
        ->patch(route('admin.notifications.read-all'))
        ->assertRedirect();
});

test('users can read their notification from the dashboard dropdown', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->user()->create();

    $this->actingAs($user)->post(route('user.tickets.store'), [
        'title' => 'Login problem',
        'description' => 'I cannot sign in.',
        'location' => 'Remote',
        'priority' => TicketPriority::Low->value,
    ]);

    $ticket = Ticket::query()->firstOrFail();
    $notification = $user->notifications()->firstOrFail();

    $this->actingAs($user)
        ->patch(route('user.notifications.read', $notification->id))
        ->assertRedirect(route('user.tickets.show', $ticket));

    expect($notification->fresh()->read_at)->not->toBeNull();
});
