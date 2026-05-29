<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppSubmodule extends Model
{
    protected $fillable = [
        'app_module_id', 'key', 'label', 'icon', 'route_name',
        'required_permission', 'order_index', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function module(): BelongsTo
    {
        return $this->belongsTo(AppModule::class, 'app_module_id');
    }
}
