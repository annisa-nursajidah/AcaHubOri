<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'user_id',
        'judul',
        'konten',
        'target',
        'is_pinned',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
    ];

    // ─── Relationships ───────────────────────────────────────────

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ─── Scopes ──────────────────────────────────────────────────

    /**
     * Filter announcements visible to the given role.
     */
    public function scopeVisibleTo($query, string $role)
    {
        return $query->where(function ($q) use ($role) {
            $q->where('target', 'all')->orWhere('target', $role);
        });
    }
}
