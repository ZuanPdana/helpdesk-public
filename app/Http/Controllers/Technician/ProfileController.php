<?php

namespace App\Http\Controllers\Technician;

use App\Enums\TechnicianAvailability;
use App\Http\Controllers\Controller;
use App\Http\Requests\Technician\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $technician = $request->user();

        $recentTickets = $technician->assignedTickets()
            ->latest()
            ->take(5)
            ->get();

        return view('technician.profile', [
            'technician' => $technician,
            'recentTickets' => $recentTickets,
            'availabilityOptions' => TechnicianAvailability::cases(),
            'supportsAvailability' => $technician::supportsAvailability(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $technician = $request->user();

        $data = $request->safe()->except('avatar');

        if (! $technician::supportsAvailability()) {
            unset($data['availability']);
        }

        if ($request->hasFile('avatar')) {
            $oldAvatar = $technician->avatarPath();
            $newAvatar = $request->file('avatar')->store('avatars/'.$technician->id, 'public');

            if ($oldAvatar && $oldAvatar !== $newAvatar) {
                Storage::disk('public')->delete($oldAvatar);
            }

            $data['avatar'] = $newAvatar;
        }

        $technician->update($data);

        return redirect()->route('technician.profile.show')->with('success', 'Profile updated successfully.');
    }
}
