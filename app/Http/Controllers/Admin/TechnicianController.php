<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TechnicianAvailability;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTechnicianRequest;
use App\Http\Requests\Admin\UpdateTechnicianRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TechnicianController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()
            ->where('role', UserRole::Technician)
            ->withCount([
                'assignedTickets as active_assignments_count' => fn ($ticketQuery) => $ticketQuery->whereIn('status', ['open', 'assigned', 'in_progress']),
            ]);

        if ($search = $request->input('search')) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($department = $request->input('department')) {
            $query->where('department', $department);
        }

        if ($availability = $request->input('availability')) {
            if (User::supportsAvailability()) {
                $query->where('availability', $availability);
            } elseif ($availability === TechnicianAvailability::Busy->value) {
                $query->whereHas('assignedTickets', fn ($ticketQuery) => $ticketQuery->whereIn('status', ['open', 'assigned', 'in_progress']));
            } elseif ($availability === TechnicianAvailability::Available->value) {
                $query->whereDoesntHave('assignedTickets', fn ($ticketQuery) => $ticketQuery->whereIn('status', ['open', 'assigned', 'in_progress']));
            }
        }

        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc') === 'asc' ? 'asc' : 'desc';
        $allowedSorts = ['name', 'email', 'department', 'created_at'];

        if (User::supportsAvailability()) {
            $allowedSorts[] = 'availability';
        }

        if (in_array($sortField, $allowedSorts, true)) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        $technicians = $query->paginate(10)->withQueryString();
        $departments = User::query()
            ->where('role', UserRole::Technician)
            ->whereNotNull('department')
            ->orderBy('department')
            ->distinct()
            ->pluck('department');

        return view('admin.technicians.index', [
            'technicians' => $technicians,
            'departments' => $departments,
            'availabilityOptions' => TechnicianAvailability::cases(),
            'supportsAvailability' => User::supportsAvailability(),
            'supportsSkills' => User::supportsSkills(),
        ]);
    }

    public function create(): View
    {
        return view('admin.technicians.create', [
            'availabilityOptions' => TechnicianAvailability::cases(),
            'supportsAvailability' => User::supportsAvailability(),
            'supportsSkills' => User::supportsSkills(),
        ]);
    }

    public function store(StoreTechnicianRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['skills_input']);

        if (! User::supportsAvailability()) {
            unset($validated['availability']);
        }

        if (! User::supportsSkills()) {
            unset($validated['skills']);
        }

        $validated['role'] = UserRole::Technician->value;

        User::create($validated);

        return redirect()
            ->route('admin.technicians.index')
            ->with('success', 'Technician created successfully.');
    }

    public function show(User $technician): View
    {
        abort_unless($technician->isTechnician(), 404);

        $technician->loadCount([
            'assignedTickets as active_assignments_count' => fn ($ticketQuery) => $ticketQuery->whereIn('status', ['open', 'assigned', 'in_progress']),
        ]);

        $assignedTickets = $technician->assignedTickets()
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.technicians.show', compact('technician', 'assignedTickets'));
    }

    public function edit(User $technician): View
    {
        abort_unless($technician->isTechnician(), 404);

        return view('admin.technicians.edit', [
            'technician' => $technician,
            'availabilityOptions' => TechnicianAvailability::cases(),
            'supportsAvailability' => User::supportsAvailability(),
            'supportsSkills' => User::supportsSkills(),
        ]);
    }

    public function update(UpdateTechnicianRequest $request, User $technician): RedirectResponse
    {
        abort_unless($technician->isTechnician(), 404);

        $validated = $request->validated();
        unset($validated['skills_input']);

        if (! User::supportsAvailability()) {
            unset($validated['availability']);
        }

        if (! User::supportsSkills()) {
            unset($validated['skills']);
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $validated['role'] = UserRole::Technician->value;

        $technician->update($validated);

        return redirect()
            ->route('admin.technicians.show', $technician)
            ->with('success', 'Technician updated successfully.');
    }

    public function destroy(User $technician): RedirectResponse
    {
        abort_unless($technician->isTechnician(), 404);

        $technician->delete();

        return redirect()
            ->route('admin.technicians.index')
            ->with('success', 'Technician deleted successfully.');
    }
}
