<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('distributor_ledger', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distributor_id')->constrained()->cascadeOnDelete();
            $table->date('entry_date')->index();
            $table->string('voucher_no', 100)->nullable();
            $table->string('description')->nullable();
            $table->string('type', 20); // PURCHASE | PAYMENT
            $table->decimal('dr_amount', 14, 2)->default(0);
            $table->decimal('cr_amount', 14, 2)->default(0);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();

            $table->index(['distributor_id', 'entry_date']);
            $table->unique(['reference_type', 'reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_ledger');
    }
};
