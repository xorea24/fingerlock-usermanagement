<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'status',          // 'success' | 'failed' | 'warning'
        'fingerprint_id',  // raw slot/ID from hardware
        'description',
        'ip_address',
        'user_agent',
    ];

    // ─── Relationships ──────────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ─── Query Scopes ───────────────────────────────────────────────────────────

    /** Only failed/rejected attempts */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /** Only recent (within last N minutes) */
    public function scopeRecent($query, int $minutes = 60)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }

    // ─── Static Helpers ─────────────────────────────────────────────────────────

    /**
     * Log any admin action (default: success).
     */
    public static function log(string $action, string $description = null, string $status = 'success'): static
    {
        return static::create([
            'user_id'      => auth()->id(),
            'action'       => $action,
            'status'       => $status,
            'description'  => $description,
            'ip_address'   => request()->ip(),
            'user_agent'   => request()->userAgent(),
        ]);
    }

    /**
     * Log a failed fingerprint access attempt from the hardware.
     * @param string|null $fingerprintId  Raw slot/ID the hardware reported
     * @param string|null $description    Extra context (e.g., "Unknown fingerprint scanned")
     */
    public static function logFailedAttempt(string $fingerprintId = null, string $description = null): static
    {
        return static::create([
            'user_id'        => null,  // unknown — no authenticated user
            'action'         => 'failed_attempt',
            'status'         => 'failed',
            'fingerprint_id' => $fingerprintId,
            'description'    => $description ?? 'Unrecognized fingerprint scan on hardware.',
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }

    /**
     * Log a successful hardware access (matched fingerprint).
     * @param \App\Models\User|null $user  Matched user (if found)
     * @param string|null $fingerprintId   Fingerprint slot that matched
     */
    public static function logAccessGranted($user = null, string $fingerprintId = null): static
    {
        return static::create([
            'user_id'        => $user?->id,
            'action'         => 'access_granted',
            'status'         => 'success',
            'fingerprint_id' => $fingerprintId,
            'description'    => $user
                ? "Access granted to {$user->name} (fingerprint ID: {$fingerprintId})."
                : "Access granted (fingerprint ID: {$fingerprintId}).",
            'ip_address'     => request()->ip(),
            'user_agent'     => request()->userAgent(),
        ]);
    }
}
