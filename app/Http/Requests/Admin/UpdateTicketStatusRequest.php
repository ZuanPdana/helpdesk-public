<?php

namespace App\Http\Requests\Admin;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateTicketStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(TicketStatus::class)->except(TicketStatus::Closed)],
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
                $nextStatus = TicketStatus::tryFrom((string) $this->input('status'));

                if (! $nextStatus) {
                    return;
                }

                if ($nextStatus->stepIndex() < $ticket->status->stepIndex()) {
                    $validator->errors()->add('status', 'Status changes must follow the configured workflow order.');
                }

                if (
                    in_array($nextStatus, [TicketStatus::Assigned, TicketStatus::InProgress, TicketStatus::Resolved, TicketStatus::Closed], true)
                    && ! $ticket->assigned_to
                ) {
                    $validator->errors()->add('status', 'Assign a technician before moving the ticket beyond Open.');
                }
            },
        ];
    }
}
