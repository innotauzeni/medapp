<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assessment extends Model
{
    protected $fillable = [
        'enrolment_id', 'course_module_id', 'title', 'type',
        'score', 'max_score', 'outcome', 'trainer_comments',
        'assessed_on', 'assessed_by',
    ];

    protected $casts = [
        'assessed_on' => 'date',
        'score'       => 'decimal:2',
        'max_score'   => 'decimal:2',
    ];

    public function enrolment(): BelongsTo
    {
        return $this->belongsTo(Enrolment::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }
}
