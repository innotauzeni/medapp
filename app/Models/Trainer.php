<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trainer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'email', 'phone',
        'qualifications', 'specialty', 'bio', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function fullName(): Attribute
    {
        return Attribute::get(fn () => trim("{$this->first_name} {$this->last_name}"));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(CourseSchedule::class, 'schedule_trainers', 'trainer_id', 'schedule_id')
            ->withPivot('role')->withTimestamps();
    }

    public function leadSchedules(): HasMany
    {
        return $this->hasMany(CourseSchedule::class, 'lead_trainer_id');
    }
}
