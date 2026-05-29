<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Enrolment extends Model
{
    protected $fillable = [
        'student_id', 'course_id', 'schedule_id',
        'status', 'enrolled_on', 'completed_on', 'final_score',
        'trainer_comments', 'created_by',
    ];

    protected $casts = [
        'enrolled_on'  => 'date',
        'completed_on' => 'date',
        'final_score'  => 'decimal:2',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(CourseSchedule::class, 'schedule_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }
}
