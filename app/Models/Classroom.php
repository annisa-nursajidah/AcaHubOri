<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'nama',
        'tingkat',
        'wali_kelas_id',
        'academic_year_id',
    ];

    // ─── Relationships ───────────────────────────────────────────

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function waliKelas()
    {
        return $this->belongsTo(TeacherProfile::class, 'wali_kelas_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function students()
    {
        return $this->hasManyThrough(
            StudentProfile::class,
            Enrollment::class,
            'classroom_id',        // FK on enrollments
            'id',                  // FK on student_profiles
            'id',                  // local key on classrooms
            'student_profile_id'   // local key on enrollments
        );
    }
}
