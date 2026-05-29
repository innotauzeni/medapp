<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    protected $fillable = [
        'name', 'address_line1', 'city', 'province', 'country',
        'contact_phone', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function schedules(): HasMany
    {
        return $this->hasMany(CourseSchedule::class);
    }
}
