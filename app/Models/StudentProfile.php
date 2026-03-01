<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nis',
        'kelas',
        'tanggal_lahir',
        'alamat',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    // ─── Relationships ───────────────────────────────────────────

    /**
     * Get the user that owns this student profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all grades for this student.
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Get all enrollments for this student.
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Get classrooms through enrollments.
     */
    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'enrollments')
                    ->withPivot('academic_year_id', 'status')
                    ->withTimestamps();
    }
}
