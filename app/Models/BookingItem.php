<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingItem extends Model
{
    protected $fillable = ['booking_id', 'course_id', 'schedule_id', 'quantity', 'unit_price', 'notes'];

    protected $casts = ['unit_price' => 'decimal:2'];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(CourseSchedule::class, 'schedule_id');
    }
}
