<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $query = $request->user()->notifications()->latest();

        if ($filter = $request->string('filter')->toString()) {
            if ($filter === 'unread') {
                $query->whereNull('read_at');
            } elseif ($filter === 'read') {
                $query->whereNotNull('read_at');
            }
        }

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where(function ($builder) use ($search): void {
                $builder->where('data->title', 'like', "%{$search}%")
                    ->orWhere('data->message', 'like', "%{$search}%")
                    ->orWhere('data->ticket_number', 'like', "%{$search}%");
            });
        }

        $notifications = $query->paginate(10)->withQueryString();

        return view('admin.notifications.index', [
            'notifications' => $notifications,
            'filter' => $request->string('filter', 'all')->toString(),
            'search' => $request->string('search')->toString(),
        ]);
    }

    public function read(Request $request, string $notification): RedirectResponse
    {
        $notification = $this->notificationForUser($request, $notification);
        $notification->markAsRead();

        return redirect()->to($notification->data['action_url'] ?? route('admin.notifications.index'));
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back()->with('success', 'All notifications marked as read.');
    }

    private function notificationForUser(Request $request, string $notification): DatabaseNotification
    {
        return $request->user()->notifications()
            ->whereKey($notification)
            ->firstOrFail();
    }
}
