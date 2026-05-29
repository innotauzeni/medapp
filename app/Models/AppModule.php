<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppModule extends Model
{
    protected $fillable = [
        'key', 'label', 'icon', 'route_name',
        'required_permission', 'order_index', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function submodules(): HasMany
    {
        return $this->hasMany(AppSubmodule::class)->orderBy('order_index');
    }
}
