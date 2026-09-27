<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Collapse the previous `super_admin` / `pharmacy` pair into a single
     * `admin` / `user` pair understood by the shared login page.
     */
    public function up(): void
    {
        DB::table('users')->where('role', 'super_admin')->update(['role' => 'admin']);
        DB::table('users')->where('role', 'pharmacy')->update(['role' => 'user']);

        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 20)->default('user')->change();
        });
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'admin')->update(['role' => 'super_admin']);
        DB::table('users')->where('role', 'user')->update(['role' => 'pharmacy']);

        Schema::table('users', function (Blueprint $table): void {
            $table->string('role', 20)->default('pharmacy')->change();
        });
    }
};
