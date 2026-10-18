<?php

namespace App\Models;

use Database\Factories\AuditLogFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

#[Fillable([
    'user_id',
    'action',
    'description',
    'subject_type',
    'subject_id',
    'ip_address',
    'user_agent',
])]
class AuditLog extends Model
{
    /** @use HasFactory<AuditLogFactory> */
    use HasFactory;

    public const PHARMACY_CREATED = 'pharmacy.created';

    public const PHARMACY_UPDATED = 'pharmacy.updated';

    public const PHARMACY_DELETED = 'pharmacy.deleted';

    public const PHARMACY_STATUS = 'pharmacy.status';

    public const PHARMACY_PASSWORD = 'pharmacy.password';

    public const USER_UPDATED = 'user.updated';

    public const USER_STATUS = 'user.status';

    public const USER_PASSWORD = 'user.password';

    public const SETTINGS_UPDATED = 'settings.updated';

    /** Audit entries are written once and never updated. */
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * A human-friendly label for a recorded action.
     */
    public static function label(string $action): string
    {
        return match ($action) {
            self::PHARMACY_CREATED => 'Pharmacy created',
            self::PHARMACY_UPDATED => 'Pharmacy updated',
            self::PHARMACY_DELETED => 'Pharmacy deleted',
            self::PHARMACY_STATUS => 'Pharmacy status changed',
            self::PHARMACY_PASSWORD => 'Pharmacy password reset',
            self::USER_UPDATED => 'User updated',
            self::USER_STATUS => 'User status changed',
            self::USER_PASSWORD => 'User password reset',
            self::SETTINGS_UPDATED => 'Settings updated',
            default => ucfirst(str_replace(['.', '_'], ' ', $action)),
        };
    }
}
