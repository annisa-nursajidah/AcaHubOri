<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'user_id',
        'judul',
        'deskripsi',
        'tanggal_mulai',
        'tanggal_selesai',
        'tipe',
        'warna',
    ];

    protected $casts = [
        'tanggal_mulai'   => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    // ─── Relationships ───────────────────────────────────────────

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────

    public function getTypeColorAttribute(): string
    {
        return match ($this->tipe) {
            'ujian'    => '#ef4444',
            'libur'    => '#22c55e',
            'akademik' => '#0891b2',
            default    => '#6b7280',
        };
    }
}
