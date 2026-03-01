<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_profile_id',
        'classroom_id',
        'academic_year_id',
        'status',
    ];

    // ─── Relationships ───────────────────────────────────────────

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class);
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
