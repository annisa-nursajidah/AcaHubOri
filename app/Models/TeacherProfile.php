<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nip',
        'telepon',
        'alamat',
    ];

    // ─── Relationships ───────────────────────────────────────────

    /**
     * Get the user that owns this teacher profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The subjects that this teacher teaches.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'subject_teacher');
    }

    /**
     * Get all grades given by this teacher.
     */
    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
