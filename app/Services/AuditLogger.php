<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuditLogger
{
    public function __construct(private Request $request) {}

    /**
     * Record an administrative action for the platform audit trail.
     */
    public function log(string $action, string $description, ?Model $subject = null, ?User $actor = null): AuditLog
    {
        return AuditLog::create([
            'user_id' => ($actor ?? Auth::user())?->id,
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject?->getMorphClass(),
            'subject_id' => $subject?->getKey(),
            'ip_address' => $this->request->ip(),
            'user_agent' => Str::limit((string) $this->request->userAgent(), 255),
        ]);
    }
}
