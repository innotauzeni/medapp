<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = ['enrolment_id', 'date', 'status', 'remarks', 'recorded_by'];

    protected $casts = ['date' => 'date'];

    public function enrolment(): BelongsTo
    {
        return $this->belongsTo(Enrolment::class);
    }
}
