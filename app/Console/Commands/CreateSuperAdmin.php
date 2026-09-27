<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdmin extends Command
{
    /**
     * @var string
     */
    protected $signature = 'super-admin:create
                            {--email= : Super Admin email address}
                            {--password= : Super Admin password}
                            {--name= : Super Admin display name}';

    /**
     * @var string
     */
    protected $description = 'Create or update the platform Super Admin account';

    public function handle(): int
    {
        $email = $this->option('email') ?: config('ledger.super_admin.email');
        $password = $this->option('password') ?: config('ledger.super_admin.password');
        $name = $this->option('name') ?: config('ledger.super_admin.name');

        $validator = Validator::make([
            'email' => $email,
            'password' => $password,
            'name' => $name,
        ], [
            'email' => ['required', 'email', 'max:190'],
            'password' => ['required', 'string', 'min:8'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            $this->line('');
            $this->line('Pass credentials with --email/----password or set SUPER_ADMIN_EMAIL / SUPER_ADMIN_PASSWORD in .env.');

            return self::FAILURE;
        }

        $admin = User::query()->firstOrNew(['email' => $email]);

        $admin->forceFill([
            'name' => $name,
            'password' => $password,
            'role' => User::ROLE_ADMIN,
            'pharmacy_id' => null,
            'email_verified_at' => now(),
        ])->save();

        $this->info("Super Admin ready: {$admin->email}");
        $this->line('Sign in at: '.route('login'));

        return self::SUCCESS;
    }
}
