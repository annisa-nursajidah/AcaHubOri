<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_profile_id',
        'subject_id',
        'teacher_profile_id',
        'nilai',
        'tipe',
        'semester',
        'tahun_ajaran',
        'catatan',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
    ];

    // ─── Relationships ───────────────────────────────────────────

    /**
     * Get the student that this grade belongs to.
     */
    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    /**
     * Get the subject that this grade belongs to.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the teacher who gave this grade.
     */
    public function teacherProfile()
    {
        return $this->belongsTo(TeacherProfile::class);
    }
}
