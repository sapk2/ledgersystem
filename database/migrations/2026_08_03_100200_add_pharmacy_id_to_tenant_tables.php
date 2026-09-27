<?php

use App\Models\Pharmacy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables that hold pharmacy (tenant) data.
     *
     * @var list<string>
     */
    private array $tables = [
        'distributors',
        'medicines',
        'purchases',
        'purchase_items',
        'payments',
        'distributor_ledger',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreignId('pharmacy_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
                $blueprint->index('pharmacy_id');
            });
        }

        // Medicine names are only unique within a pharmacy.
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropUnique('medicines_name_unique');
            $table->unique(['pharmacy_id', 'name']);
        });

        $this->backfillExistingData();
    }

    /**
     * Attach the records that predate multi-pharmacy support to a single pharmacy.
     */
    private function backfillExistingData(): void
    {
        $owner = DB::table('users')->orderBy('id')->first();

        $pharmacy = Pharmacy::query()->create([
            'name' => config('ledger.pharmacy_name') ?: 'Our Pharmacy',
            'owner_name' => $owner?->name,
            'email' => $owner?->email ?: 'owner@pharmacy.test',
            'phone' => config('ledger.pharmacy_phone'),
            'address' => config('ledger.pharmacy_address'),
            'country' => 'Nepal',
            'is_active' => true,
        ]);

        DB::table('users')->whereNull('pharmacy_id')->update([
            'pharmacy_id' => $pharmacy->id,
            'role' => 'pharmacy',
        ]);

        foreach ($this->tables as $table) {
            DB::table($table)->whereNull('pharmacy_id')->update(['pharmacy_id' => $pharmacy->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('medicines', function (Blueprint $table) {
            $table->dropUnique(['pharmacy_id', 'name']);
            $table->unique('name');
        });

        foreach (array_reverse($this->tables) as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['pharmacy_id']);
                $table->dropColumn('pharmacy_id');
            });
        }
    }
};
