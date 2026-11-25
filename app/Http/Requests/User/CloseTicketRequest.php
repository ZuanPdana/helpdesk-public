<?php

namespace App\Http\Requests\User;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CloseTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');

        // only ticket requester (user) may close their own ticket
        return (int) $ticket->user_id === (int) $this->user()?->id;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'reason' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                /** @var Ticket $ticket */
                $ticket = $this->route('ticket');

                // Users can only close when status is Open or Resolved
                if (! in_array($ticket->status, [TicketStatus::Open, TicketStatus::Resolved], true)) {
                    $validator->errors()->add('status', 'You can only close tickets that are Open or Resolved.');
                }
            },
        ];
    }
}
