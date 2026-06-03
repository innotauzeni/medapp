<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code', 'title', 'category_id', 'description',
        'image_path', 'image_url',
        'duration_hours', 'passing_score', 'fee', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'fee'       => 'decimal:2',
    ];

    /**
     * Resolved URL for the feature image: prefers an uploaded file,
     * falls back to an external image_url, then null.
     */
    public function getFeatureImageUrlAttribute(): ?string
    {
        if ($this->image_path) {
            // Return a host-relative URL (e.g. /storage/courses/x.jpg) rather than
            // the absolute APP_URL-based one, so images load no matter which host
            // or port the app is served on (artisan serve port, Apache vhost, etc.).
            $url = Storage::disk('public')->url($this->image_path);
            return parse_url($url, PHP_URL_PATH) ?: $url;
        }

        return $this->image_url ?: null;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function modules(): HasMany
    {
        return $this->hasMany(CourseModule::class)->orderBy('order_index');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(CourseSchedule::class);
    }

    public function enrolments(): HasMany
    {
        return $this->hasMany(Enrolment::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }
}
