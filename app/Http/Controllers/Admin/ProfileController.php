<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProfilePasswordRequest;
use App\Http\Requests\Admin\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $admin = $request->user();

        $recentTickets = $admin->tickets()
            ->latest()
            ->take(5)
            ->get();

        return view('admin.profile.show', compact('admin', 'recentTickets'));
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('avatar');

        if ($request->hasFile('avatar')) {
            $oldAvatar = $request->user()->avatarPath();
            $newAvatar = $request->file('avatar')->store('avatars/'.$request->user()->id, 'public');

            if ($oldAvatar && $oldAvatar !== $newAvatar) {
                Storage::disk('public')->delete($oldAvatar);
            }

            $data['avatar'] = $newAvatar;
        }

        $request->user()->update($data);

        return redirect()
            ->route('admin.profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(UpdateProfilePasswordRequest $request): RedirectResponse
    {
        $request->user()->update([
            'password' => $request->validated('password'),
        ]);

        return redirect()
            ->route('admin.profile.show')
            ->with('success', 'Password updated successfully.');
    }
}
