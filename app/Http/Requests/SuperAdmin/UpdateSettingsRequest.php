<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
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
            'platform_name' => ['required', 'string', 'max:100'],
            'support_email' => ['nullable', 'string', 'email', 'max:190'],
            'default_pharmacy_status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
