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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('pharmacy_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->string('role', 20)->default('pharmacy')->after('password');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'pharmacy_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['pharmacy_id']);
            $table->dropIndex(['role', 'pharmacy_id']);
            $table->dropColumn(['pharmacy_id', 'role']);
        });
    }
};
