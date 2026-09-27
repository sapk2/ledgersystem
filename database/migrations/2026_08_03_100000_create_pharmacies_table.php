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
        Schema::create('pharmacies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('owner_name')->nullable();
            $table->string('email');
            $table->string('phone', 30)->nullable();
            $table->string('address')->nullable();
            $table->string('city', 100)->nullable()->index();
            $table->string('district', 100)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('country', 100)->default('Nepal');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->unique('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacies');
    }
};
