<?php

namespace Database\Seeders;

use App\Models\Distributor;
use App\Models\Medicine;
use App\Models\Payment;
use App\Models\Purchase;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        $this->seedMedicines();
        $this->seedDistributors();
    }

    private function seedMedicines(): void
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

        foreach ($medicines as $medicine) {
            Medicine::firstOrCreate(['name' => $medicine['name']], $medicine);
        }
    }

    private function seedDistributors(): void
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
            $distributor = Distributor::firstOrCreate(['name' => $data['name']], $data);

            $this->seedHistory($distributor, $today->copy()->subDays((10 - $index) * 7));
        }
    }

    private function seedHistory(Distributor $distributor, Carbon $start): void
    {
        $medicines = Medicine::all();

        $billCount = random_int(2, 4);
        $ledgerBalance = (float) $distributor->opening_balance;

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
                'distributor_id' => $distributor->id,
                'bill_no' => strtoupper(substr($distributor->name, 0, 2)).random_int(100000, 999999),
                'bill_date' => $billDate,
                'discount' => $discount,
                'vat' => $vat,
                'total_amount' => $total,
            ]);

            foreach ($items as $item) {
                $purchase->items()->create([
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
                'distributor_id' => $distributor->id,
                'amount' => $amount,
                'payment_date' => $paymentDate,
                'reference' => random_int(0, 1) ? 'CHQ-'.random_int(1000, 9999) : null,
            ]);

            $payment->ledgerEntry()->create([
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
