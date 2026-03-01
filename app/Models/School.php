<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'logo',
        'invite_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────────

    /**
     * Users belonging to this school.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Subscriptions for this school.
     */
    public function subscriptions()
    {
        return $this->hasMany(SchoolSubscription::class);
    }

    /**
     * The school admin user.
     */
    public function admin()
    {
        return $this->hasOne(User::class)->where('role', 'school_admin');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function academicYears()
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────

    /**
     * Get the currently active subscription.
     */
    public function activeSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->latest()
            ->first();
    }

    /**
     * Total accounts purchased across active subscriptions.
     */
    public function totalAccountsQuota(): int
    {
        return (int) $this->subscriptions()
            ->where('status', 'active')
            ->where('expires_at', '>', now())
            ->sum('total_accounts');
    }

    /**
     * Number of user accounts currently used (excludes school_admin).
     */
    public function usedAccountsCount(): int
    {
        return $this->users()->where('role', '!=', 'school_admin')->count();
    }

    /**
     * Remaining account slots.
     */
    public function remainingAccountsQuota(): int
    {
        return max(0, $this->totalAccountsQuota() - $this->usedAccountsCount());
    }

    /**
     * Check if the school can still create new accounts.
     */
    public function canCreateAccount(): bool
    {
        return $this->is_active && $this->remainingAccountsQuota() > 0;
    }
}
