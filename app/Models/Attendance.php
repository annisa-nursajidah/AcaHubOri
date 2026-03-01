<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_profile_id',
        'subject_id',
        'teacher_profile_id',
        'tanggal',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // ─── Relationships ───────────────────────────────────────────

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacherProfile()
    {
        return $this->belongsTo(TeacherProfile::class);
    }
}
