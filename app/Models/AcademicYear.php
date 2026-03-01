<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'tahun',
        'semester',
        'tanggal_mulai',
        'tanggal_selesai',
        'is_active',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
        'is_active'       => 'boolean',
    ];

    // ─── Helpers ─────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->tahun} — {$this->semester}";
    }

    // ─── Scopes ──────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ─── Relationships ───────────────────────────────────────────

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}
