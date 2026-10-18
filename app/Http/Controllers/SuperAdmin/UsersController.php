<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\UpdateUserPasswordRequest;
use App\Http\Requests\SuperAdmin\UpdateUserRequest;
use App\Models\AuditLog;
use App\Models\Pharmacy;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UsersController extends Controller
{
    public function __construct(private AuditLogger $audit) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $role = $request->query('role');
        $status = $request->query('status');

        $users = User::with('pharmacy')
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('pharmacy', fn ($query) => $query->where('name', 'like', "%{$search}%"));
            }))
            ->when(in_array($role, [User::ROLE_ADMIN, User::ROLE_USER], true), fn ($query) => $query->where('role', $role))
            ->when($status === 'active', fn ($query) => $query->where('is_active', true))
            ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('super-admin.users.index', [
            'users' => $users,
            'search' => $search,
            'role' => $role,
            'status' => $status,
        ]);
    }

    public function edit(User $user): View
    {
        return view('super-admin.users.edit', [
            'user' => $user,
            'pharmacies' => Pharmacy::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $attributes = $request->safe()->only(['name', 'email', 'role', 'pharmacy_id']);
        $attributes['is_active'] = $request->boolean('is_active');

        if ($request->user()->is($user)) {
            unset($attributes['role'], $attributes['is_active']);
        }

        $role = $attributes['role'] ?? $user->role;

        if ($role === User::ROLE_ADMIN) {
            $attributes['pharmacy_id'] = null;
        }

        $this->assertAnAdminRemains($user, $role === User::ROLE_ADMIN && ($attributes['is_active'] ?? $user->is_active), 'role');

        $user->update($attributes);

        $this->audit->log(AuditLog::USER_UPDATED, "Updated the account for {$user->name}.", $user);

        return redirect()
            ->route('super-admin.users.index')
            ->with('status', "{$user->name}'s account was updated.");
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        if ($request->user()->is($user)) {
            throw ValidationException::withMessages([
                'is_active' => 'You cannot change the status of your own account.',
            ]);
        }

        $this->assertAnAdminRemains($user, (bool) $validated['is_active'] && $user->isAdmin(), 'is_active');

        $user->update(['is_active' => $validated['is_active']]);

        $this->audit->log(AuditLog::USER_STATUS, $user->is_active
            ? "Enabled the account for {$user->name}."
            : "Disabled the account for {$user->name}.", $user);

        return back()->with('status', $user->is_active
            ? "{$user->name}'s account is now active."
            : "{$user->name}'s account was disabled.");
    }

    public function updatePassword(UpdateUserPasswordRequest $request, User $user): RedirectResponse
    {
        $user->update(['password' => $request->validated('password')]);

        $this->audit->log(AuditLog::USER_PASSWORD, "Reset the password for {$user->name}.", $user);

        return back()->with('status', "The password for {$user->name} was reset.");
    }

    /**
     * Guard against disabling or demoting the last remaining administrator.
     */
    private function assertAnAdminRemains(User $user, bool $stillActiveAdmin, string $field): void
    {
        if (! $user->isAdmin() || ! $user->is_active || $stillActiveAdmin) {
            return;
        }

        $activeAdmins = User::where('role', User::ROLE_ADMIN)->where('is_active', true)->count();

        if ($activeAdmins <= 1) {
            throw ValidationException::withMessages([
                $field => 'At least one active administrator must remain.',
            ]);
        }
    }
}
