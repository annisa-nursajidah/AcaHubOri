<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'teacher_id',
        'classroom_id',
        'subject_id',
        'date',
        'start_time',
        'end_time',
        'qr_code_token',
        'status',
    ];

    protected $casts = [
        'date'       => 'datetime',
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
