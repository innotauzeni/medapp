<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSchedule extends Model
{
    protected $fillable = [
        'course_id', 'location_id', 'lead_trainer_id',
        'start_date', 'end_date', 'capacity', 'status', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function leadTrainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'lead_trainer_id');
    }

    public function trainers(): BelongsToMany
    {
        return $this->belongsToMany(Trainer::class, 'schedule_trainers', 'schedule_id', 'trainer_id')
            ->withPivot('role')->withTimestamps();
    }

    public function enrolments(): HasMany
    {
        return $this->hasMany(Enrolment::class, 'schedule_id');
    }
}
