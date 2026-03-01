<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'nama',
        'kode',
        'deskripsi',
    ];

    // ─── Relationships ───────────────────────────────────────────

    /**
     * Get the school this subject belongs to.
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * The teachers that teach this subject.
     */
    public function teachers()
    {
        return $this->belongsToMany(TeacherProfile::class, 'subject_teacher');
    }

    /**
     * Get all grades for this subject.
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
