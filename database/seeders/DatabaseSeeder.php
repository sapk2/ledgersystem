<?php

namespace Database\Seeders;

use App\Models\Distributor;
use App\Models\Medicine;
use App\Models\Payment;
use App\Models\Pharmacy;
use App\Models\Purchase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * The pharmacies created by the seeder.
     *
     * @var list<array{name: string, owner_name: string, email: string, city: string, address: string}>
     */
    private array $pharmacies = [
        [
            'name' => 'Pratap Pharmacy',
            'owner_name' => 'Pratap Shrestha',
            'email' => 'pratap@pharmacy.test',
            'phone' => '9841000001',
            'address' => 'Koteshwor, Kathmandu',
            'city' => 'Kathmandu',
        ],
        [
            'name' => 'Apex Pharma House',
            'owner_name' => 'Apex Gurung',
            'email' => 'apex@pharmacy.test',
            'phone' => '9841000002',
            'address' => 'Lalitpur',
            'city' => 'Lalitpur',
        ],
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedSuperAdmin();

        foreach ($this->pharmacies as $attributes) {
            $this->seedPharmacy($attributes);
        }
    }

    /**
     * The platform administrator. Credentials come from the configuration so
     * that they still resolve when the configuration is cached.
     */
    private function seedSuperAdmin(): void
    {
        $admin = User::firstOrCreate(
            ['email' => config('ledger.super_admin.email')],
            [
                'name' => config('ledger.super_admin.name'),
                'password' => config('ledger.super_admin.password'),
                'role' => User::ROLE_ADMIN,
                'pharmacy_id' => null,
            ]
        );

        $this->markAsVerified($admin);
    }

    /**
     * `email_verified_at` is intentionally not mass assignable, so it is set explicitly.
     */
    private function markAsVerified(User $user): void
    {
        if ($user->email_verified_at === null) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }
    }

    /**
     * @param  array<string, string>  $attributes
     */
    private function seedPharmacy(array $attributes): void
    {
        $pharmacy = Pharmacy::firstOrCreate(['email' => $attributes['email']], $attributes);

        $owner = $pharmacy->users()->firstOrCreate(
            ['email' => $attributes['email']],
            [
                'name' => $attributes['owner_name'],
                'password' => 'password',
                'role' => User::ROLE_USER,
            ]
        );

        $this->markAsVerified($owner);

        $medicines = $this->seedMedicines($pharmacy);
        $this->seedDistributors($pharmacy, $medicines);
    }

    /**
     * @return Collection<int, Medicine>
     */
    private function seedMedicines(Pharmacy $pharmacy): Collection
    {
        $medicines = [
            ['name' => 'Paracetamol 500mg', 'generic_name' => 'Paracetamol', 'packing' => '10×10'],
            ['name' => 'Amoxicillin 250mg', 'generic_name' => 'Amoxicillin', 'packing' => '10×10'],
            ['name' => 'Ibuprofen 400mg', 'generic_name' => 'Ibuprofen', 'packing' => '10×10'],
            ['name' => 'Metformin 500mg', 'generic_name' => 'Metformin', 'packing' => '10×10'],
            ['name' => 'Omeprazole 20mg', 'generic_name' => 'Omeprazole', 'packing' => '14×10'],
            ['name' => 'Cough Syrup 100ml', 'generic_name' => 'Dextromethorphan', 'packing' => '1×100'],
            ['name' => 'Vit-C 1000mg', 'generic_name' => 'Ascorbic Acid', 'packing' => '10×10'],
            ['name' => 'Azithromycin 500mg', 'generic_name' => 'Azithromycin', 'packing' => '3×10'],
        ];

        return collect($medicines)->map(function (array $medicine) use ($pharmacy): Medicine {
            return Medicine::firstOrCreate(
                ['pharmacy_id' => $pharmacy->id, 'name' => $medicine['name']],
                $medicine + ['pharmacy_id' => $pharmacy->id],
            );
        });
    }

    /**
     * @param  Collection<int, Medicine>  $medicines
     */
    private function seedDistributors(Pharmacy $pharmacy, Collection $medicines): void
    {
        $distributors = [
            ['name' => 'Life Line Medicine Distributors', 'phone' => '9841012345', 'address' => 'Bharatpur-11', 'city' => 'Chitwan', 'pan_no' => '501234567', 'opening_balance' => 5000],
            ['name' => 'Parat Pharma', 'phone' => '9841012345', 'city' => 'Kathmandu', 'pan_no' => '503456789'],
            ['name' => 'Apex Medicine Center', 'phone' => '9851023456', 'city' => 'Lalitpur', 'pan_no' => '504567890'],
            ['name' => 'Bharat Medical Supply', 'phone' => '9861034567', 'city' => 'Bhaktapur', 'pan_no' => '505678901'],
            ['name' => 'City Drug House', 'phone' => '9871045678', 'city' => 'Kathmandu', 'pan_no' => '506789012'],
            ['name' => 'Everest Distributors', 'phone' => '9881056789', 'city' => 'Pokhara', 'pan_no' => '507890123'],
            ['name' => 'Ganga Pharma Traders', 'phone' => '9891067890', 'city' => 'Kathmandu', 'pan_no' => '508901234'],
            ['name' => 'Himal Drug Agency', 'phone' => '9811078901', 'city' => 'Lalitpur', 'pan_no' => '509012345'],
            ['name' => 'Sagarmatha Medicine Store', 'phone' => '9821089012', 'city' => 'Kathmandu', 'pan_no' => '510123456'],
            ['name' => 'Universal Healthcare', 'phone' => '9831090123', 'city' => 'Butwal', 'pan_no' => '511234567'],
        ];

        $today = Carbon::today();

        foreach ($distributors as $index => $data) {
            $distributor = Distributor::firstOrCreate(
                ['pharmacy_id' => $pharmacy->id, 'name' => $data['name']],
                $data + ['pharmacy_id' => $pharmacy->id],
            );

            $this->seedHistory($pharmacy, $distributor, $medicines, $today->copy()->subDays((10 - $index) * 7));
        }
    }

    /**
     * @param  Collection<int, Medicine>  $medicines
     */
    private function seedHistory(Pharmacy $pharmacy, Distributor $distributor, Collection $medicines, Carbon $start): void
    {
        $billCount = random_int(2, 4);
        $ledgerBalance = (float) $distributor->opening_balance;
        $prefix = strtoupper(substr($distributor->name, 0, 2));

        for ($i = 1; $i <= $billCount; $i++) {
            $billDate = $start->copy()->addDays($i * 5 + random_int(0, 3));

            if ($billDate->isFuture()) {
                break;
            }

            $items = collect([]);
            $subtotal = 0.0;

            foreach ($medicines->random(random_int(2, 3)) as $medicine) {
                $quantity = random_int(2, 15);
                $rate = random_int(10, 150) + 0.5;
                $amount = round($quantity * $rate, 2);

                $items->push([
                    'medicine' => $medicine,
                    'quantity' => $quantity,
                    'rate' => $rate,
                    'amount' => $amount,
                ]);

                $subtotal += $amount;
            }

            $discount = round($subtotal * random_int(0, 5) / 100, 2);
            $vat = 13;
            $total = round($subtotal - $discount + (($subtotal - $discount) * $vat / 100), 2);

            $purchase = Purchase::create([
                'pharmacy_id' => $pharmacy->id,
                'distributor_id' => $distributor->id,
                'bill_no' => $prefix.str_pad((string) $i, 4, '0', STR_PAD_LEFT),
                'bill_date' => $billDate,
                'discount' => $discount,
                'vat' => $vat,
                'total_amount' => $total,
            ]);

            foreach ($items as $item) {
                $purchase->items()->create([
                    'pharmacy_id' => $pharmacy->id,
                    'medicine_id' => $item['medicine']->id,
                    'medicine_name' => $item['medicine']->name,
                    'batch_no' => 'BT'.random_int(1000, 9999),
                    'expiry_date' => $billDate->copy()->addMonths(random_int(12, 36)),
                    'quantity' => $item['quantity'],
                    'rate' => $item['rate'],
                    'amount' => $item['amount'],
                ]);
            }

            $purchase->ledgerEntry()->create([
                'pharmacy_id' => $pharmacy->id,
                'distributor_id' => $distributor->id,
                'entry_date' => $billDate,
                'voucher_no' => $purchase->bill_no,
                'description' => "Purchase Bill {$purchase->bill_no}",
                'type' => 'PURCHASE',
                'dr_amount' => $total,
                'cr_amount' => 0,
            ]);

            $ledgerBalance += $total;
        }

        $paymentCount = random_int(1, 3);

        for ($i = 1; $i <= $paymentCount; $i++) {
            $paymentDate = $start->copy()->addDays($i * 8 + random_int(0, 5));

            if ($paymentDate->isFuture() || $ledgerBalance <= 0) {
                break;
            }

            $amount = min(round($ledgerBalance * random_int(40, 80) / 100, 2), $ledgerBalance);

            $payment = Payment::create([
                'pharmacy_id' => $pharmacy->id,
                'distributor_id' => $distributor->id,
                'amount' => $amount,
                'payment_date' => $paymentDate,
                'reference' => random_int(0, 1) ? 'CHQ-'.random_int(1000, 9999) : null,
            ]);

            $payment->ledgerEntry()->create([
                'pharmacy_id' => $pharmacy->id,
                'distributor_id' => $distributor->id,
                'entry_date' => $paymentDate,
                'voucher_no' => $payment->reference ?? "PMT-{$payment->id}",
                'description' => $payment->reference ? "Payment - {$payment->reference}" : 'Payment received',
                'type' => 'PAYMENT',
                'dr_amount' => 0,
                'cr_amount' => $amount,
            ]);

            $ledgerBalance -= $amount;
        }
    }
}
