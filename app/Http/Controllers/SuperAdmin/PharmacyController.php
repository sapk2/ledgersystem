<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SuperAdmin\StorePharmacyRequest;
use App\Http\Requests\SuperAdmin\UpdatePharmacyPasswordRequest;
use App\Http\Requests\SuperAdmin\UpdatePharmacyRequest;
use App\Models\Pharmacy;
use App\Models\Scopes\PharmacyScope;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PharmacyController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));

        $pharmacies = Pharmacy::withCount('users')
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('owner_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            }))
            ->when($request->query('status') === 'active', fn ($query) => $query->where('is_active', true))
            ->when($request->query('status') === 'inactive', fn ($query) => $query->where('is_active', false))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('super-admin.pharmacies.index', [
            'pharmacies' => $pharmacies,
            'search' => $search,
            'status' => $request->query('status'),
        ]);
    }

    public function create(): View
    {
        return view('super-admin.pharmacies.create', [
            'pharmacy' => new Pharmacy,
        ]);
    }

    public function store(StorePharmacyRequest $request): RedirectResponse
    {
        $pharmacy = DB::transaction(function () use ($request): Pharmacy {
            $pharmacy = Pharmacy::create($request->safe()->except('password'));

            $owner = $pharmacy->users()->create([
                'name' => $request->validated('owner_name'),
                'email' => $request->validated('email'),
                'password' => $request->validated('password'),
                'role' => User::ROLE_USER,
            ]);

            $owner->forceFill(['email_verified_at' => now()])->save();

            return $pharmacy;
        });

        return redirect()
            ->route('super-admin.pharmacies.show', $pharmacy)
            ->with('status', "{$pharmacy->name} was created successfully.");
    }

    public function show(Pharmacy $pharmacy): View
    {
        $counts = [
            'distributors' => $pharmacy->distributors()->withoutGlobalScope(PharmacyScope::class)->count(),
            'purchases' => $pharmacy->purchases()->withoutGlobalScope(PharmacyScope::class)->count(),
            'payments' => $pharmacy->payments()->withoutGlobalScope(PharmacyScope::class)->count(),
        ];

        return view('super-admin.pharmacies.show', [
            'pharmacy' => $pharmacy,
            'users' => $pharmacy->users()->orderBy('id')->get(),
            'counts' => $counts,
            'paymentTotal' => (float) $pharmacy->payments()->withoutGlobalScope(PharmacyScope::class)->sum('amount'),
        ]);
    }

    public function edit(Pharmacy $pharmacy): View
    {
        return view('super-admin.pharmacies.edit', [
            'pharmacy' => $pharmacy,
        ]);
    }

    public function update(UpdatePharmacyRequest $request, Pharmacy $pharmacy): RedirectResponse
    {
        $pharmacy->update($request->validated());

        $owner = $pharmacy->users()->orderBy('id')->first();

        if ($owner && $request->validated('email') !== $owner->email) {
            $owner->update(['email' => $request->validated('email')]);
        }

        return redirect()
            ->route('super-admin.pharmacies.show', $pharmacy)
            ->with('status', "{$pharmacy->name} was updated.");
    }

    public function updateStatus(Request $request, Pharmacy $pharmacy): RedirectResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $pharmacy->update($validated);

        return back()->with('status', $pharmacy->is_active
            ? "{$pharmacy->name} is now active."
            : "{$pharmacy->name} was deactivated and can no longer sign in.");
    }

    public function updatePassword(UpdatePharmacyPasswordRequest $request, Pharmacy $pharmacy): RedirectResponse
    {
        // A query builder update bypasses the model cast, so the password is hashed here.
        $pharmacy->users()->update(['password' => Hash::make($request->validated('password'))]);

        return back()->with('status', "The password for {$pharmacy->name} was reset.");
    }
}
