<?php

namespace App\Http\Requests\SuperAdmin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateUserRequest extends FormRequest
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
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => [
                'required', 'string', 'email', 'max:190',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_USER])],
            'pharmacy_id' => ['nullable', 'integer', Rule::exists('pharmacies', 'id')->whereNull('deleted_at')],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->input('role') === User::ROLE_USER && ! $this->filled('pharmacy_id')) {
                    $validator->errors()->add('pharmacy_id', 'A pharmacy user must be assigned to a pharmacy.');
                }
            },
        ];
    }
}
