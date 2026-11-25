<?php

namespace App\Http\Requests\Technician;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateTicketStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');

        if (! $this->user()?->isTechnician()) {
            return false;
        }

        // only assigned technician may update
        return (int) $ticket->assigned_to === (int) $this->user()->id;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::enum(TicketStatus::class),
                Rule::in([
                    TicketStatus::InProgress->value,
                    TicketStatus::Resolved->value,
                ]),
            ],
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

                // Allow assigned tickets to start work and preserve the existing work/resolution transitions
                $allowedTransitions = [
                    TicketStatus::Assigned->value => [TicketStatus::InProgress->value],
                    TicketStatus::InProgress->value => [TicketStatus::Resolved->value],
                    TicketStatus::Resolved->value => [TicketStatus::InProgress->value],
                ];

                $currentValue = $ticket->status->value;
                $nextValue = $nextStatus->value;

                if (! isset($allowedTransitions[$currentValue]) || ! in_array($nextValue, $allowedTransitions[$currentValue], true)) {
                    $validator->errors()->add('status', 'Invalid status transition. You can only move between In Progress and Resolved.');
                }
            },
        ];
    }
}
