<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserAccountStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()
            ->whereIn('role', [UserRole::Admin, UserRole::User]);

        if ($search = $request->input('search')) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        if ($status = $request->input('status')) {
            if (User::supportsStatus()) {
                $query->where('status', $status);
            } elseif ($status === UserAccountStatus::Active->value) {
                $query->whereNotNull('email_verified_at');
            } elseif ($status === UserAccountStatus::Pending->value) {
                $query->whereNull('email_verified_at');
            }
        }

        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['name', 'email', 'role', 'created_at'];

        if (User::supportsStatus()) {
            $allowedSorts[] = 'status';
        }

        if (in_array($sortField, $allowedSorts, true)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = [UserRole::Admin, UserRole::User];
        $statuses = User::supportsStatus()
            ? UserAccountStatus::cases()
            : [UserAccountStatus::Active, UserAccountStatus::Pending];

        return view('admin.users.index', compact('users', 'roles', 'statuses'));
    }

    public function create(): View
    {
        $roles = [UserRole::Admin, UserRole::User];
        $statuses = UserAccountStatus::cases();

        return view('admin.users.create', [
            'roles' => $roles,
            'statuses' => $statuses,
            'supportsStatus' => User::supportsStatus(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if (! User::supportsStatus()) {
            unset($validated['status']);
        }

        User::create($validated);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user): View
    {
        abort_if($user->isTechnician(), 404);

        $recentTickets = $user->tickets()
            ->with('assignee')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.users.show', [
            'managedUser' => $user,
            'recentTickets' => $recentTickets,
        ]);
    }

    public function edit(User $user): View
    {
        abort_if($user->isTechnician(), 404);

        return view('admin.users.edit', [
            'managedUser' => $user,
            'roles' => [UserRole::Admin, UserRole::User],
            'statuses' => UserAccountStatus::cases(),
            'supportsStatus' => User::supportsStatus(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        abort_if($user->isTechnician(), 404);

        $validated = $request->validated();

        if (! User::supportsStatus()) {
            unset($validated['status']);
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->isTechnician(), 404);

        if ($user->is(auth()->user())) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
