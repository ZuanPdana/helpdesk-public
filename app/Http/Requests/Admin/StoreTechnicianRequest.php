<?php

namespace App\Http\Requests\Admin;

use App\Enums\TechnicianAvailability;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTechnicianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'skills' => collect(preg_split('/[\r\n,]+/', (string) $this->input('skills_input', '')))
                ->map(fn (?string $skill): string => trim((string) $skill))
                ->filter()
                ->values()
                ->all(),
        ]);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique((new User)->getTable(), 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:50'],
            'department' => ['nullable', 'string', 'max:255'],
            'skills_input' => ['nullable', 'string'],
        ];

        if (User::supportsAvailability()) {
            $rules['availability'] = ['nullable', Rule::enum(TechnicianAvailability::class)];
        }

        if (User::supportsSkills()) {
            $rules['skills'] = ['nullable', 'array'];
            $rules['skills.*'] = ['string', 'max:100'];
        }

        return $rules;
    }
}
