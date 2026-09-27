<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Distributor;
use App\Models\Payment;
use App\Models\Pharmacy;
use App\Models\Purchase;
use App\Models\Scopes\PharmacyScope;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('super-admin.dashboard', [
            'pharmacyCount' => Pharmacy::count(),
            'activePharmacyCount' => Pharmacy::where('is_active', true)->count(),
            'inactivePharmacyCount' => Pharmacy::where('is_active', false)->count(),
            'userCount' => User::where('role', User::ROLE_USER)->count(),
            'distributorCount' => Distributor::withoutGlobalScope(PharmacyScope::class)->count(),
            'purchaseCount' => Purchase::withoutGlobalScope(PharmacyScope::class)->count(),
            'paymentTotal' => (float) Payment::withoutGlobalScope(PharmacyScope::class)->sum('amount'),
            'recentPharmacies' => Pharmacy::withCount('users')->latest()->limit(8)->get(),
        ]);
    }
}
