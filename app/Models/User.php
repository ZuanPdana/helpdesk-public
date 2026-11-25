<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\TechnicianAvailability;
use App\Enums\UserAccountStatus;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

#[Fillable(['name', 'email', 'password', 'role', 'phone', 'department', 'avatar', 'status', 'availability', 'skills'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $attributes = [
        'role' => 'user',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => UserAccountStatus::class,
            'availability' => TechnicianAvailability::class,
            'skills' => 'array',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isTechnician(): bool
    {
        return $this->role === UserRole::Technician;
    }

    public function isUser(): bool
    {
        return $this->role === UserRole::User;
    }

    public static function supportsStatus(): bool
    {
        return Schema::hasColumn((new static)->getTable(), 'status');
    }

    public static function supportsAvailability(): bool
    {
        return Schema::hasColumn((new static)->getTable(), 'availability');
    }

    public static function supportsSkills(): bool
    {
        return Schema::hasColumn((new static)->getTable(), 'skills');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function accountStatusValue(): string
    {
        if (self::supportsStatus() && $this->status instanceof UserAccountStatus) {
            return $this->status->value;
        }

        return $this->email_verified_at ? UserAccountStatus::Active->value : UserAccountStatus::Pending->value;
    }

    public function accountStatusLabel(): string
    {
        if (self::supportsStatus() && $this->status instanceof UserAccountStatus) {
            return $this->status->label();
        }

        return $this->email_verified_at ? 'Active' : 'Pending';
    }

    public function availabilityValue(): string
    {
        if (self::supportsAvailability() && $this->availability instanceof TechnicianAvailability) {
            return $this->availability->value;
        }

        $activeAssignments = $this->getAttribute('active_assignments_count');

        if ($activeAssignments === null) {
            $activeAssignments = $this->assignedTickets()
                ->whereIn('status', ['open', 'assigned', 'in_progress'])
                ->count();
        }

        return $activeAssignments > 0
            ? TechnicianAvailability::Busy->value
            : TechnicianAvailability::Available->value;
    }

    public function availabilityLabel(): string
    {
        return TechnicianAvailability::from($this->availabilityValue())->label();
    }

    /**
     * @return array<int, string>
     */
    public function skillsList(): array
    {
        if (! self::supportsSkills() || ! is_array($this->skills)) {
            return [];
        }

        return array_values(array_filter($this->skills, fn (mixed $skill): bool => is_string($skill) && $skill !== ''));
    }

    public function initials(): string
    {
        $words = explode(' ', $this->name);

        return mb_strtoupper(
            mb_substr($words[0], 0, 1).mb_substr(end($words), 0, 1)
        );
    }

    public function avatarPath(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        $path = (string) $this->avatar;

        if (Str::startsWith($path, ['http://', 'https://'])) {
            $path = (string) parse_url($path, PHP_URL_PATH);
        }

        $path = ltrim($path, '/');

        return Str::startsWith($path, 'storage/')
            ? Str::after($path, 'storage/')
            : $path;
    }

    public function avatarUrl(): ?string
    {
        $path = $this->avatarPath();

        return $path ? url('storage/'.$path) : null;
    }
}
