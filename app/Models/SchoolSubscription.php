<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'plan_id',
        'total_accounts',
        'price_per_account',
        'total_price',
        'status',
        'starts_at',
        'expires_at',
        'midtrans_order_id',
        'midtrans_snap_token',
        'notes',
    ];

    protected $casts = [
        'price_per_account' => 'decimal:2',
        'total_price'       => 'decimal:2',
        'starts_at'         => 'datetime',
        'expires_at'        => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('expires_at', '>', now());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /**
     * Check if this subscription is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at && $this->expires_at->isFuture();
    }

    /**
     * Activate the subscription (after payment confirmed).
     */
    public function activate(): void
    {
        $durationDays = $this->plan->duration_days ?? 365;
        $this->update([
            'status'     => 'active',
            'starts_at'  => now(),
            'expires_at' => now()->addDays($durationDays),
        ]);

        // Also activate the school
        $this->school->update(['is_active' => true]);
    }

    /**
     * Auto-calculate total price before saving.
     */
    protected static function booted(): void
    {
        static::creating(function (SchoolSubscription $sub) {
            $sub->total_price = round($sub->total_accounts * $sub->price_per_account, 2);
        });
    }

    /**
     * Get status badge color for UI.
     */
    public function statusColor(): string
    {
        return match ($this->status) {
            'pending'   => 'yellow',
            'paid'      => 'blue',
            'active'    => 'green',
            'expired'   => 'gray',
            'cancelled' => 'red',
            default     => 'gray',
        };
    }
}
