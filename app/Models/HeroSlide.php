<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeroSlide extends Model
{
    protected $fillable = [
        'badge', 'title', 'subtitle', 'image_path', 'image_url',
        'cta_label', 'cta_url', 'secondary_cta_label', 'secondary_cta_url',
        'background_color', 'order_index', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function imageSrc(): ?string
    {
        if ($this->image_url) return $this->image_url;
        if ($this->image_path) return asset('storage/' . $this->image_path);
        return null;
    }
}
