<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'pharmacy_id', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** The platform administrator, who manages every pharmacy. */
    public const ROLE_ADMIN = 'admin';

    /** A pharmacy user, who only ever sees their own pharmacy's data. */
    public const ROLE_USER = 'user';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isUser(): bool
    {
        return ! $this->isAdmin();
    }

    /**
     * A disabled account cannot sign in, regardless of its role.
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Inactive pharmacies are signed out and blocked from the application.
     */
    public function hasActivePharmacy(): bool
    {
        return ! $this->isUser() || (bool) $this->pharmacy?->is_active;
    }

    /**
     * Whether this account may currently authenticate.
     */
    public function canSignIn(): bool
    {
        return $this->isActive() && $this->hasActivePharmacy();
    }

    public function isActivePharmacy(): bool
    {
        return $this->hasActivePharmacy() && ! $this->isAdmin();
    }

    /**
     * The dashboard this user belongs on after signing in.
     */
    public function homeRoute(): string
    {
        return $this->isAdmin() ? 'super-admin.dashboard' : 'dashboard';
    }
}
