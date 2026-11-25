<?php

namespace App\Http\Controllers\User;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\TicketNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;

class TicketCommentController extends Controller
{
    public function store(StoreCommentRequest $request, Ticket $ticket): RedirectResponse
    {
        $ticket->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->validated('body'),
        ]);

        $ticket->histories()->create([
            'user_id' => $request->user()->id,
            'action' => 'comment_added',
            'description' => $request->user()->name.' added a comment',
        ]);

        if ($request->user()->isUser()) {
            $admins = User::query()
                ->where('role', UserRole::Admin)
                ->get();

            Notification::send($admins, TicketNotification::commentAdded($ticket, $request->user()));
        }

        return redirect()
            ->route('user.tickets.show', $ticket)
            ->with('success', 'Comment added successfully.');
    }
}
