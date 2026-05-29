<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CourseCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::saving(function (CourseCategory $cat) {
            if (empty($cat->slug)) {
                $cat->slug = Str::slug($cat->name);
            }
        });
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'category_id');
    }
}
