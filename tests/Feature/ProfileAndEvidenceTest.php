<?php

use App\Enums\TechnicianAvailability;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
    Storage::fake('public');
});

function profileImage(string $name = 'profile.png'): UploadedFile
{
    return UploadedFile::fake()->createWithContent(
        $name,
        base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=')
    );
}

test('admin can change their profile picture', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->put(route('admin.profile.update'), [
            'name' => $admin->name,
            'email' => $admin->email,
            'phone' => $admin->phone,
            'department' => $admin->department,
            'avatar' => profileImage(),
        ])
        ->assertRedirect(route('admin.profile.show'));

    $admin->refresh();

    expect($admin->avatar)->toStartWith('avatars/'.$admin->id.'/')
        ->and($admin->avatarUrl())->toContain('/storage/'.$admin->avatar);
    Storage::disk('public')->assertExists($admin->avatar);
});

test('technician can change their profile picture', function () {
    $technician = User::factory()->technician()->create();

    $this->actingAs($technician)
        ->put(route('technician.profile.update'), [
            'name' => $technician->name,
            'email' => $technician->email,
            'phone' => $technician->phone,
            'avatar' => profileImage(),
        ])
        ->assertRedirect(route('technician.profile.show'));

    $technician->refresh();

    expect($technician->avatar)->toStartWith('avatars/'.$technician->id.'/')
        ->and($technician->avatar)->not->toContain('http');
    Storage::disk('public')->assertExists($technician->avatar);
});

test('technician can change their availability to busy', function () {
    $technician = User::factory()->technician()->create([
        'availability' => TechnicianAvailability::Available,
    ]);

    $this->actingAs($technician)
        ->get(route('technician.profile.show'))
        ->assertSuccessful()
        ->assertSee('name="availability"', false)
        ->assertSee('Busy');

    $this->put(route('technician.profile.update'), [
        'name' => $technician->name,
        'email' => $technician->email,
        'phone' => $technician->phone,
        'availability' => TechnicianAvailability::Busy->value,
    ])->assertRedirect(route('technician.profile.show'));

    expect($technician->fresh()->availability)->toBe(TechnicianAvailability::Busy);
});

test('technician availability only accepts a supported value', function () {
    $technician = User::factory()->technician()->create([
        'availability' => TechnicianAvailability::Available,
    ]);

    $this->actingAs($technician)
        ->put(route('technician.profile.update'), [
            'name' => $technician->name,
            'email' => $technician->email,
            'phone' => $technician->phone,
            'availability' => 'offline',
        ])
        ->assertSessionHasErrors('availability');

    expect($technician->fresh()->availability)->toBe(TechnicianAvailability::Available);
});

test('changed technician avatar appears in the dashboard navigation', function () {
    $technician = User::factory()->technician()->create();

    $this->actingAs($technician)->put(route('technician.profile.update'), [
        'name' => $technician->name,
        'email' => $technician->email,
        'phone' => $technician->phone,
        'avatar' => profileImage(),
    ]);

    $technician->refresh();

    $this->actingAs($technician)
        ->get(route('technician.dashboard'))
        ->assertSuccessful()
        ->assertSee($technician->avatarUrl(), false);
});

test('user can change their profile picture', function () {
    $user = User::factory()->user()->create();

    $this->actingAs($user)
        ->put(route('user.profile.update'), [
            'name' => $user->name,
            'phone' => $user->phone,
            'avatar' => profileImage(),
        ])
        ->assertRedirect(route('user.profile.show'));

    $user->refresh();

    expect($user->avatar)->toStartWith('avatars/'.$user->id.'/');
    Storage::disk('public')->assertExists($user->avatar);
});

test('replacing a profile picture deletes the old file', function () {
    $user = User::factory()->user()->create();
    $oldPath = 'avatars/'.$user->id.'/old.png';
    Storage::disk('public')->put($oldPath, 'old image');
    $user->update(['avatar' => $oldPath]);

    $this->actingAs($user)->put(route('user.profile.update'), [
        'name' => $user->name,
        'phone' => $user->phone,
        'avatar' => profileImage('new.png'),
    ]);

    $user->refresh();

    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($user->avatar);
    expect($user->avatar)->not->toBe($oldPath);
});
