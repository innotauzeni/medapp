<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Service extends Model
{
    protected $fillable = [
        'icon', 'title', 'description', 'image_path', 'image_url', 'order_index', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function getFeatureImageUrlAttribute(): ?string
    {
        if ($this->image_path) {
            $disk = config('filesystems.service_image_disk', 'public');
            $url  = Storage::disk($disk)->url($this->image_path);

            if ($disk === 'public') {
                return parse_url($url, PHP_URL_PATH) ?: $url;
            }

            return $url;
        }

        return $this->image_url ?: null;
    }
}
