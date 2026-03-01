<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price_per_account',
        'min_accounts',
        'max_accounts',
        'features',
        'duration_days',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price_per_account' => 'decimal:2',
        'features'          => 'array',
        'is_active'         => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────────

    public function subscriptions()
    {
        return $this->hasMany(SchoolSubscription::class, 'plan_id');
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /**
     * Calculate total price for a given number of accounts.
     */
    public function calculatePrice(int $accounts): float
    {
        return round($accounts * $this->price_per_account, 2);
    }
}
