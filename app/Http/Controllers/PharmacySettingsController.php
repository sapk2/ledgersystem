<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PharmacySettingsController extends Controller
{
    /**
     * The logged-in pharmacy manages its own letterhead and contact details.
     */
    public function edit(Request $request): View
    {
        return view('pharmacy.settings', [
            'pharmacy' => $request->user()->pharmacy,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $pharmacy = $request->user()->pharmacy;

        abort_if($pharmacy === null, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'owner_name' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        $pharmacy->update($validated);

        return back()->with('status', 'Pharmacy details updated.');
    }
}
