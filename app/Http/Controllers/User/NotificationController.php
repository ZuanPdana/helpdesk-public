<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function read(Request $request, string $notification): RedirectResponse
    {
        $notification = $this->notificationForUser($request, $notification);
        $notification->markAsRead();

        return redirect()->to($notification->data['action_url'] ?? route('user.tickets.index'));
    }

    private function notificationForUser(Request $request, string $notification): DatabaseNotification
    {
        return $request->user()->notifications()
            ->whereKey($notification)
            ->firstOrFail();
    }
}
